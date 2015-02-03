<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Option_Type_Form_Builder_Item_Form_Header_Title extends FW_Option_Type_Form_Builder_Item {
	public function get_type() {
		return 'form-header-title';
	}

	private function get_uri( $append = '' ) {
		return fw_get_framework_directory_uri( '/extensions/forms/includes/option-types/' . $this->get_builder_type() . '/items/' . $this->get_type() . $append );
	}

	public function get_thumbnails() {
		return array(
			array(
				'html' => ''
			)
		);
	}

	public function enqueue_static() {
		wp_enqueue_style(
			'fw-builder-' . $this->get_builder_type() . '-item-' . $this->get_type(),
			$this->get_uri( '/static/css/styles.css' )
		);

		wp_enqueue_script(
			'fw-builder-' . $this->get_builder_type() . '-item-' . $this->get_type(),
			$this->get_uri( '/static/js/scripts.js' ),
			array(
				'fw-events',
			),
			false,
			true
		);

		wp_localize_script(
			'fw-builder-' . $this->get_builder_type() . '-item-' . $this->get_type(),
			'fw_form_builder_item_type_' . str_replace( '-', '_', $this->get_type() ),
			array(
				'l10n'     => array(
					'edit_title'      => __( 'Edit Title', 'fw' ),
					'edit_subtitle'      => __( 'Edit Subtitle', 'fw' ),
				),
				'options'  => $this->get_options(),
				'defaults' => array(
					'type'    => $this->get_type(),
					'options' => fw_get_options_values_from_input( $this->get_options(), array() )
				)
			)
		);

		fw()->backend->enqueue_options_static( $this->get_options() );
	}

	private function get_options() {
		return array(
			'title' => array(
				'type'  => 'text',
				'label' => __( 'Title', 'fw' ),
				'desc'  => __( 'The title will be displayed on contact form header', 'fw' ),
				'value' => '',
			),
			'subtitle' => array(
				'type'  => 'textarea',
				'label' => __( 'Subtitle', 'fw' ),
				'desc'  => __( 'The form header subtitle text', 'fw' ),
				'value' => '',
			)
		);
	}

	protected function get_fixed_attributes( $attributes ) {
		// do not allow sub items
		unset( $attributes['_items'] );

		$default_attributes = array(
			'type'      => $this->get_type(),
			'shortcode' => 'form-header-title',
			'width'     => '',
			'options'   => array()
		);

		// remove unknown attributes
		$attributes = array_intersect_key( $attributes, $default_attributes );

		$attributes = array_merge( $default_attributes, $attributes );

		$attributes['options'] = fw_get_options_values_from_input(
			$this->get_options(),
			$attributes['options']
		);

		return $attributes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_value_from_attributes( $attributes ) {
		return $this->get_fixed_attributes( $attributes );
	}

	/**
	 * {@inheritdoc}
	 */
	public function frontend_render( array $item, $input_value ) {
		return fw_render_view(
			$this->locate_path( '/views/view.php', dirname( __FILE__ ) . '/view.php' ),
			array(
				'title' => $item['options']['title'],
				'subtitle' => $item['options']['subtitle'],
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function frontend_validate( array $item, $input_value ) {}

	/**
	 * {@inheritdoc}
	 */
	public function visual_only() {
		return true;
	}
}

FW_Option_Type_Builder::register_item_type( 'FW_Option_Type_Form_Builder_Item_Form_Header_Title' );
