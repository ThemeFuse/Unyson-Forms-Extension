<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Forms sub extensions should extend this class
 */
abstract class FW_Extension_Forms_Form extends FW_Extension {

	/**
	 * Specify which builder option type this form type is using (in options)
	 * @return string
	 */
	abstract public function get_form_builder_type();

	/**
	 * Return value of the option type $this->get_form_builder_type() used in options
	 *
	 * @param int $form_id Post id
	 *
	 * @return array
	 */
	abstract public function get_form_builder_value( $form_id );

	/**
	 * Render frontend form html
	 *
	 * @param array $view_data
	 *
	 * @return string html
	 */
	abstract public function render( $view_data );

	/**
	 * Do something with form items values on frontend form submit after successful validation
	 *
	 * @param array $form_values Frontend form values {shortcode => value}
	 * @param array $data
	 */
	abstract public function process_form( $form_values, $data );
}
