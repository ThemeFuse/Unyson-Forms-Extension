<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * Returns value of the form option
 *
 * @param $form_id
 * @param null $multikey
 *
 * @return mixed
 */
function fw_ext_contact_forms_get_option( $form_id, $multikey = null ) {
	return fw_ext( 'contact-forms' )->get_option( $form_id, $multikey );
}