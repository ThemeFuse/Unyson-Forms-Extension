<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

wp_enqueue_style( 'fw-short-code-contact-form-style',
	fw_ext('contact-form')->get_declared_URI() . '/shortcodes/contact-form/static/css/style.css'
);

wp_enqueue_script( 'fw-short-code-contact-form-script',
	fw_ext('contact-form')->get_declared_URI() . '/shortcodes/contact-form/static/js/scripts.js',
	array('fw-events'),
	fw()->manifest->get_version(),
	true
);