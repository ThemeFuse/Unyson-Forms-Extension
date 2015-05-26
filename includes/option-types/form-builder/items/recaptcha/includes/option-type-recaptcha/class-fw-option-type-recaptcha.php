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
					'desc'  => __( 'Paste here your code site serves to users.', 'unyson' ),
					'type'  => 'text'
				),
				'secret-key'    => array(
					'label' => __( 'Secret key', 'unyson' ),
					'desc'  => __( 'Paste here secret key for communication between your site and Google. Be sure to keep it a secret.', 'unyson' ),
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