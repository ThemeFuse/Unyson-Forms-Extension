(function (fwe) {
	fwe.on('fw-builder:' + 'page-builder' + ':register-items', function (builder) {
		var PageBuilderContactFormItem,
			PageBuilderContactFormItemView,
			triggerEvent = function(itemModel, event, eventData) {
				event = 'fw:builder-type:{builder-type}:item-type:{item-type}:'
					.replace('{builder-type}', builder.get('type'))
					.replace('{item-type}', itemModel.get('type'))
					+ event;

				var data = {
					modal: itemModel.view ? itemModel.view.modal : null,
					item: itemModel,
					itemView: itemModel.view,
					shortcode: itemModel.get('shortcode')
				};

				fwEvents.trigger(event, eventData
					? _.extend(eventData, data)
					: data
				);
			},
			getEventName = function(itemModel, event) {
				return 'fw:builder-type:{builder-type}:item-type:{item-type}:'
					.replace('{builder-type}', builder.get('type'))
					.replace('{item-type}', itemModel.get('type'))
					+ event;
			};

		PageBuilderContactFormItemView = builder.classes.ItemView.extend({
			initialize: function (options) {
				this.defaultInitialize();

				this.templateData = options.templateData;

				if (options.modalOptions) {
					var eventData = {modalSettings: {buttons: []}};

					/**
					 * eventData.modalSettings can be changed by reference
					 */
					triggerEvent(this.model, 'options-modal:settings', eventData);

					this.modal = new fw.OptionsModal({
						title: 'Contact Form', // TODO: make translatable
						options: options.modalOptions,
						values: this.model.get('atts'),
						size: options.modalSize
					}, eventData.modalSettings);

					this.listenTo(this.modal, 'change:values', function (modal, values) {
						this.model.set('atts', values);
					});

					this.listenTo(this.modal, {
						'open': function(){
							fwEvents.trigger(getEventName(this.model, 'options-modal:open'), {
								modal: this.modal,
								item: this.model,
								itemView: this
							});
						},
						'render': function(){
							fwEvents.trigger(getEventName(this.model, 'options-modal:render'), {
								modal: this.modal,
								item: this.model,
								itemView: this
							});
						},
						'close': function(){
							fwEvents.trigger(getEventName(this.model, 'options-modal:close'), {
								modal: this.modal,
								item: this.model,
								itemView: this
							});
						},
						'change:values': function(){
							fwEvents.trigger(getEventName(this.model, 'options-modal:change:values'), {
								modal: this.modal,
								item: this.model,
								itemView: this
							});
						}
					});
				}
			},
			template: _.template(
				'<div class="pb-item-type-contact-form pb-item-type-simple pb-item <% if (hasOptions) { print(' + '"has-options"' + ')} %>">' +
					'<img src="<%- image %>"><%- title %>' +
					'<div class="controls">' +
						'<% if (!isMailer) { %>' +
						'<i class="dashicons dashicons-info contact-form-item-mailer" data-hover-tip="<%- configureMailer %>"></i>' +
						'<%  } %>' +
						'<% if (hasOptions) { %>' +
						'<i class="dashicons dashicons-admin-generic edit-options" data-hover-tip="<%- edit %>"></i>' +
						'<%  } %>' +
						'<i class="dashicons dashicons-admin-page contact-form-item-clone" data-hover-tip="<%- duplicate %>"></i>' +
						'<i class="dashicons dashicons-no contact-form-item-delete" data-hover-tip="<%- remove %>"></i>' +
					'</div>' +
				'</div>'
			),
			render: function () {
				this.defaultRender(this.templateData);

				/**
				 * Other scripts can append/prepend other control $elements
				 */
				fwEvents.trigger('fw:page-builder:shortcode:contact-form:controls', {
					$controls: this.$('.controls:first'),
					model: this.model,
					builder: builder
				});
			},
			events: {
				'click .contact-form-item-mailer': 'configureMailer',
				'click .pb-item-type-contact-form': 'editOptions',
				'click .edit-options': 'editOptions',
				'click .contact-form-item-clone': 'cloneItem',
				'click .contact-form-item-delete': 'removeItem'
			},
			editOptions: function (e) {
				e.stopPropagation();

				if (!this.modal) {
					return;
				}

				var flow = {cancelModalOpening: false};

				/**
				 * Trigger before-open model just like we do this for
				 * item-simple shortcodes.
				 *
				 * http://bit.ly/1KY6tpP
				 */
				fwEvents.trigger('fw:page-builder:shortcode:contact-form:modal:before-open', {
					modal: this.modal,
					model: this.model,
					builder: builder,
					flow: flow
				});

				if (! flow.cancelModalOpening) {
					this.modal.open();
				}
			},
			configureMailer: function (e) {
				this.editOptions(e);

				fwe.on('fw:options:init', function (data) {
					data.$elements.find('.fw-options-tabs-wrapper').find('a[href="#fw-options-tab-settings"]').trigger('click');
					data.$elements.find('.fw-options-tabs-wrapper').find('#fw-options-tab-settings').find('a[href="#fw-options-tab-mailer-options"]').trigger('click');
				});
				return false;
			},
			cloneItem: function () {
				var index = this.model.collection.indexOf(this.model),
					attributes = this.model.toJSON(),
					_items = attributes['_items'],
					clonedContactForm;

				delete attributes['_items'];

				clonedContactForm = new PageBuilderContactFormItem(attributes);

				triggerEvent(clonedContactForm, 'clone-item:before');

				this.model.collection.add(clonedContactForm, {at: index + 1});
				clonedContactForm.get('_items').reset(_items);
				return false;
			},
			removeItem: function () {
				this.remove();
				this.model.collection.remove(this.model);
				return false;
			}
		});

		PageBuilderContactFormItem = builder.classes.Item.extend({
			defaults: {
				type: 'contact-form'
			},
			restrictedTypes: itemData().restrictedTypes,
			initialize: function (atts, opts) {

				this.view = new PageBuilderContactFormItemView({
					id: 'page-builder-item-' + this.cid,
					model: this,
					modalOptions: itemData().options,
					modalSize: itemData().popup_size,
					templateData: {
						title: itemData().title,
						image: itemData().image,
						isMailer : itemData().mailer,
						configureMailer : itemData().configureMailer,
						edit : itemData().edit,
						duplicate : itemData().duplicate,
						remove : itemData().remove,
						hasOptions: !!itemData().options
					}
				});

				this.defaultInitialize();
			},
			allowIncomingType: function (type) {
				return _.indexOf(this.restrictedTypes, type) === -1;
			}
		});

		builder.registerItemClass(PageBuilderContactFormItem);
	});

	function itemData () {
		// return fw.unysonShortcodesData()['contact_form'];
		// return page_builder_item_type_contact_form_data;
		return fw_form_builder_item_type_contact_form_data[
			'contact_form'
		];
	}
})(fwEvents);
