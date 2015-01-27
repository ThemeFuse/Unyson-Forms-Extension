<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

wp_enqueue_style( 'fw-short-code-contact-form-style',
	fw_ext('contact-form')->get_declared_URI() . '/shortcodes/contact-form/static/css/style.css'
);