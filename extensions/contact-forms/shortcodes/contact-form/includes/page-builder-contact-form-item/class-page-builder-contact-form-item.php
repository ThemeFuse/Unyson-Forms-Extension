<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class Page_Builder_Contact_Form_Item extends Page_Builder_Item {
	private $restricted_types = array( 'contact-form' );

	public function get_type() {
		return 'contact-form';
	}

	private function get_shortcode_options() {
		$shortcode_instance = fw()->extensions->get( 'shortcodes' )->get_shortcode( 'contact_form' );

		return $shortcode_instance->get_options();
	}

	public function enqueue_static() {
		$cf_shortcode = fw()->extensions->get( 'shortcodes' )->get_shortcode( 'contact_form' );
		wp_enqueue_style(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			$cf_shortcode->locate_URI( '/includes/page-builder-contact-form-item/static/css/styles.css' ),
			array(),
			fw()->theme->manifest->get_version()
		);
		wp_enqueue_script(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			$cf_shortcode->locate_URI( '/includes/page-builder-contact-form-item/static/js/scripts.js' ),
			array( 'fw-events', 'underscore', 'jquery' ),
			fw()->theme->manifest->get_version(),
			true
		);
		wp_localize_script(
			$this->get_builder_type() . '_item_type_' . $this->get_type(),
			str_replace( '-', '_', $this->get_builder_type() ) . '_item_type_contact_form_data',
			$this->get_item_data()
		);
	}

	private function get_item_data() {
		$cf_shortcode = fw_ext( 'shortcodes' )->get_shortcode( 'contact_form' );
		$data = array(
			'title' => __( 'Contact Form', 'fw' ),
			'mailer' => fw_ext_mailer_is_configured(),
			'configureMailer' => __( 'Configure Mailer', 'fw' ),
			'restrictedTypes' => $this->restricted_types,
			'image' => $cf_shortcode->locate_URI( "/includes/page-builder-contact-form-item/static/img/page_builder.png" )
		);

		$options = $this->get_shortcode_options();
		if ( $options ) {
			fw()->backend->enqueue_options_static( $options );
			$data['options'] = $this->transform_options( $options );
		}

		$data['popup_size'] = 'large';

		return $data;
	}

	/*
	 * Puts each option into a separate array
	 * to keep it's order inside the modal dialog
	 */
	private function transform_options( $options ) {
		$transformed_options = array();
		foreach ( $options as $id => $option ) {
			$transformed_options[] = array( $id => $option );
		}

		return $transformed_options;
	}

	protected function get_thumbnails_data() {
		$cf_shortcode = fw_ext( 'shortcodes' )->get_shortcode( 'contact_form' );

		$cf_thumbnail = array(
			array(
				'tab'         => __( 'Content Elements', 'fw' ),
				'title'       => __( 'Contact form', 'fw' ),
				'description' => __( "Add a Contact Form", 'fw' ),
				'image'       => $cf_shortcode->locate_URI( "/includes/page-builder-contact-form-item/static/img/page_builder.png" ),
			)
		);
		return $cf_thumbnail;
	}

	public function get_value_from_attributes( $attributes ) {
		$attributes['type'] = $this->get_type();

		/*
		 * when saving the modal, the options values go into the
		 * 'atts' key, if it is not present it could be
		 * because of two things:
		 * 1. The shortcode does not have options
		 * 2. The user did not open or save the modal (which will be more likely the case)
		 */
		if ( ! isset( $attributes['atts'] ) ) {
			$options = $this->get_shortcode_options();
			if ( ! empty( $options ) ) {
				$attributes['atts'] = fw_get_options_values_from_input( $options, array() );
			}
		}

		return $attributes;
	}

	public function get_shortcode_data( $atts = array() ) {

		$return_atts = array(
			'width' => '1_1'
		);
		if ( isset( $atts['atts'] ) ) {
			$return_atts = array_merge( $return_atts, $atts['atts'] );
		}

		return array(
			'tag'  => str_replace( '-', '_', $this->get_type() ),
			'atts' => $return_atts
		);
	}
}

FW_Option_Type_Builder::register_item_type( 'Page_Builder_Contact_Form_Item' );
