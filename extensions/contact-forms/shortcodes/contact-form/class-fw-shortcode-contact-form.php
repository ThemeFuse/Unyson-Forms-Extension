<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode_Contact_Form extends FW_Shortcode
{
	private $restricted_types = array( 'contact-form' );

	/**
	 * @internal
	 */
	public function _init()
	{
		add_action(
			'fw_option_type_builder:page-builder:register_items',
			array($this, '_action_register_builder_item_types')
		);

		add_filter( 'fw_ext:shortcodes:collect_shortcodes_data', array(
			$this, '_filter_add_contact_form_data'
		) );
	}

	/**
	 * @internal
	 */
	public function _filter_add_contact_form_data( $structure ) {
		$data['contact_form'] = $this->get_item_data();
		return array_merge( $structure, $data );
	}

	public function _action_register_builder_item_types() {
		if (fw_ext('page-builder')) {
			require $this->get_declared_path('/includes/item/class-page-builder-contact-form-item.php');
		}
	}

	protected function _render($atts, $content = null, $tag = '')
	{
		$form_data = array(
			'id' => $atts['id'],
			'form' => $atts['form'],
			'email_to' => $atts['email_to'],
			'subject_message' => $atts['subject_message'],
			'success_message' => $atts['success_message'],
			'failure_message' => $atts['failure_message'],
		);

		/**
		 * @var FW_Extension_Contact_Forms $extension
		 */
		$extension = fw_ext('contact-forms');

		/**
		 * Save form data because the extension needs to access it (by id) on form submit
		 *
		 * There is no other possibility to save form data by id because contact form is a shortcode
		 * it has no save action and we can't access it by id (we don't know in which post it is)
		 */
		$extension->_set_form_db_data($atts['id'], $atts);

		return $extension->render(
			array(
				'id' => $form_data['id'],
				'form' => $form_data['form'],
				'submit_button_text' => $atts['submit_button_text'],
			),
			/**
			 * Extra options added by theme developer in shortcode options.php will be sent in form view
			 */
			array_diff_key(
				$atts,
				array(
					'width' => true,
					'mailer' => true,
					'submit_button_text' => true,
				),
				$form_data
			)
		);
	}

	/**
	 * Collect data for the Contact Form Shortcode itself. This data is used
	 * for now just in Page Builder, may be used by anyone else around.
	 *
	 * @since 1.0.2
	 */
	public function get_item_data() {
		/**
		 * @var FW_Shortcode $cf_shortcode
		 */

		$data = array(
			'title'           => __( 'Contact Form', 'fw' ),
			'mailer'          => fw_ext_mailer_is_configured(),
			'configureMailer' => __( 'Configure Mailer', 'fw' ),
			'edit'            => __( 'Edit', 'fw' ),
			'duplicate'       => __( 'Duplicate', 'fw' ),
			'remove'          => __( 'Remove', 'fw' ),
			'restrictedTypes' => $this->restricted_types,
			'image'           => $this->locate_URI( '/static/img/page_builder.png' )
		);

		$options = $this->get_options();

		if ( $options ) {
			// fw()->backend->enqueue_options_static( $options );

			$data['options'] = $this->transform_options( $options );

			$data['default_values'] = fw_get_options_values_from_input(
				$options, array()
			);
		}

		$data['popup_size'] = 'large';
		$data['tag'] = 'contact_form';

		return $data;
	}

	/*
	 * Puts each option into a separate array
	 * to keep it's order inside the modal dialog
	 */
	private function transform_options( $options ) {
		$transformed_options = array();
		foreach ( $options as $id => $option ) {
			if ( is_int( $id ) ) {
				/**
				 * this happens when in options array are loaded external options using fw()->theme->get_options()
				 * and the array looks like this
				 * array(
				 *    'hello' => array('type' => 'text'), // this has string key
				 *    array('hi' => array('type' => 'text')) // this has int key
				 * )
				 */
				$transformed_options[] = $option;
			} else {
				$transformed_options[] = array( $id => $option );
			}
		}

		return $transformed_options;
	}
}
