fwEvents.on('fw-builder:'+ 'form-builder' +':register-items', function(builder){
	var currentItemType = 'form-header-title';

	var localized = fw.unysonShortcodesData()['contact_form_items'][currentItemType];

	var ItemView = builder.classes.ItemView.extend({
		template: _.template(
			'<div class="fw-form-builder-item-style-default fw-form-builder-item-type-'+ currentItemType +' fw-form-item-control-edit">'+
				'<div class="fw-form-item-preview">'+
					'<div class="fw-form-item-preview-title">'+
						'<div class="fw-form-item-preview-title-wrapper"><label data-hover-tip="<%- edit_title %>"><%- title %></label></div>'+
						'<div class="fw-form-item-preview-title-edit"><!-- --></div>'+
					'</div>'+
					'<div class="fw-form-item-preview-subtitle">'+
						'<div class="fw-form-item-preview-subtitle-wrapper"><label data-hover-tip="<%- edit_subtitle %>"><%- subtitle %></label></div>'+
						'<div class="fw-form-item-preview-subtitle-edit"><!-- --></div>'+
					'</div>'+
				'</div>'+
			'</div>'
		),
		events: {
			'click': 'onWrapperClick',
			'click .fw-form-item-preview .fw-form-item-preview-title label': 'openTitleEditor',
			'click .fw-form-item-preview .fw-form-item-preview-subtitle label': 'openSubtitleEditor'
		},
		initialize: function() {
			this.defaultInitialize();

			// prepare edit options modal
			{
				this.modal = new fw.OptionsModal({
					title: localized.l10n.item_title,
					options: this.model.modalOptions,
					values: this.model.get('options'),
					size: 'medium'
				});

				this.listenTo(this.modal, 'change:values', function(modal, values) {
					this.model.set('options', values);
				});

				this.model.on('change:options', function() {
					this.modal.set(
						'values',
						this.model.get('options')
					);
				}, this);
			}

			this.titleInlineEditor = new FwBuilderComponents.ItemView.InlineTextEditor({
				model: this.model,
				editAttribute: 'options/title'
			});

			this.subtitleInlineEditor = new FwBuilderComponents.ItemView.InlineTextEditor({
				model: this.model,
				editAttribute: 'options/subtitle'
			});
		},
		render: function () {
			this.defaultRender({
				title: ( ( fw.opg('title', this.model.get('options')) ) || localized.l10n.edit_title ),
				subtitle: ( ( fw.opg('subtitle', this.model.get('options')) ) || localized.l10n.edit_subtitle ),
				edit_title: localized.l10n.edit_title,
				edit_subtitle: localized.l10n.edit_subtitle
			});

			if (this.titleInlineEditor) {
				this.$('.fw-form-item-preview-title-edit').append(
					this.titleInlineEditor.$el
				);
				this.titleInlineEditor.delegateEvents();
			}

			if (this.subtitleInlineEditor) {
				this.$('.fw-form-item-preview-subtitle-edit').append(
					this.subtitleInlineEditor.$el
				);
				this.subtitleInlineEditor.delegateEvents();
			}
		},
		openEdit: function() {
			this.modal.open();
		},
		onWrapperClick: function(e) {
			if (!this.$el.parent().length) {
				// The element doesn't exist in DOM. This listener was executed after the item was deleted
				return;
			}

			if (!fw.elementEventHasListenerInContainer(jQuery(e.srcElement), 'click', this.$el)) {
				this.openEdit();
			}
		},
		openTitleEditor: function( e ) {
			e.preventDefault();
			this.$('.fw-form-item-preview-title-wrapper').hide();

			this.titleInlineEditor.show();

			this.listenToOnce(this.titleInlineEditor, 'hide', function() {
				this.$('.fw-form-item-preview-title-wrapper').show();
			});
		},
		openSubtitleEditor: function(e) {
			e.preventDefault();
			this.$('.fw-form-item-preview-subtitle-wrapper').hide();

			this.subtitleInlineEditor.show();

			this.listenToOnce(this.subtitleInlineEditor, 'hide', function() {
				this.$('.fw-form-item-preview-subtitle-wrapper').show();
			});
		}
	});

	var Item = builder.classes.Item.extend({
		defaults: function() {
			var defaults = _.clone(localized.defaults);

			defaults.shortcode = defaults.type;
			return defaults;
		},
		initialize: function() {
			if (builder.rootItems.get(498157655) != undefined) {
				return;
			}

			this.defaultInitialize();

				/**
			* get options from wp_localize_script() variable
			*/

			this.modalOptions = localized.options;

			this.id = 498157655;

			this.view = new ItemView({
				id: 'fw-builder-item-'+ this.cid,
				model: this
			});
		}
	});

	builder.registerItemClass(Item);

	builder.rootItems.bind("add", function(model, attributes){
		var first_model = builder.rootItems.at(0);

		if (first_model.id != 498157655) {
			var current = builder.rootItems.get(498157655);

			if ( current == undefined ) {
				return;
			}

			builder.rootItems.remove( builder.rootItems.get(498157655) );

			var item = new Item;
			item.set( 'options', current.get('options') );

			builder.rootItems.unshift( item );
		}
	});
});
