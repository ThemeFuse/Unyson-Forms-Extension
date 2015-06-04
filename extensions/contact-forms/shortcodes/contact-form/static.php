<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

if ( is_admin() ) {
	wp_enqueue_style( 'fw-short-code-contact-form-style',
		fw_ext('contact-forms')->get_uri( '/shortcodes/contact-form/static/css/style.css' )
	);
}