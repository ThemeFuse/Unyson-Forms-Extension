<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Option_Type_Recaptcha extends FW_Option_Type {

	/**
	 * @internal
	 */
	public function _init() {
	}

	public function get_type() {
		return 'recaptcha';
	}

	private function get_uri( $append = '' ) {
		return fw_get_framework_directory_uri( '/extensions/forms/includes/option-types/form-builder/items/recaptcha/includes/option-type-recaptcha' . $append );
	}

	/**
	 * @internal
	 */
	public function _get_backend_width_type() {
		return 'full';
	}

	/**
	 * @internal
	 */
	protected function _get_defaults() {
		return array(
			'label'         => false,
			'type'          => 'multi',
			'inner-options' => array(
				'site-key'    => array(
					'label' => __( 'Site key', 'unyson' ),
					'desc'  => __( 'Your website key. More on how to configure ReCaptcha', 'unyson' ) . ': <a href="https://www.google.com/recaptcha" target="_blank">https://www.google.com/recaptcha</a>',
					'type'  => 'text'
				),
				'secret-key'    => array(
					'label' => __( 'Secret key', 'unyson' ),
					'desc'  => __( 'Your secret key. More on how to configure ReCaptcha', 'unyson' ) . ': <a href="https://www.google.com/recaptcha" target="_blank">https://www.google.com/recaptcha</a>',
					'type'  => 'text'
				),
			),
			'value'         => array()
		);
	}

	/**
	 * @internal
	 * {@inheritdoc}
	 */
	protected function _enqueue_static( $id, $option, $data ) {
		wp_enqueue_style(
			'fw-option-type-'. $this->get_type(),
			$this->get_uri( '/static/css/styles.css' )
		);
	}

	/**
	 * @internal
	 */
	protected function _render( $id, $option, $data ) {
		$data['value'] = fw_ext( 'forms' )->get_db_settings_option('recaptcha-keys');

		return fw()->backend->option_type( 'multi' )->render( $id, $option, $data );
	}

	/**
	 * @internal
	 *
	 * @param array $option
	 * @param array|null|string $input_value
	 *
	 * @return array|bool|int|string
	 */
	protected function _get_value_from_input( $option, $input_value ) {

		if ( is_array( $input_value ) && ! empty( $input_value ) ) {
			fw_ext( 'forms' )->set_db_settings_option( 'recaptcha-keys', $input_value );
		}

		return fw_ext( 'forms' )->get_db_settings_option('recaptcha-keys', array());
	}
}

FW_Option_Type::register( 'FW_Option_Type_Recaptcha' );