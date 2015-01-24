<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
wp_enqueue_script( 'fw-short-code-contact-form',
	fw_ext('contact-form')->get_declared_URI() . '/shortcodes/contact-form/static/js/scripts.js',
	array('fw-events'),
	fw()->manifest->get_version(),
	true
);