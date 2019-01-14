<?php if (!defined('FW')) die('Forbidden');

if (!is_admin()) {
	wp_enqueue_style('fw-ext-builder-frontend-grid');

	wp_enqueue_style(
		'fw-ext-forms-default-styles',
		fw()->extensions->get('forms')->get_declared_URI('/static/css/frontend.css'),
		array(),
		fw()->manifest->get_version()
	);

	wp_enqueue_script(
		'fw-ext-forms-required-inputs',
		fw_get_framework_directory_uri(
			'/extensions/forms/includes/option-types/form-builder/items/checkboxes/static/js/frontend.js'
		),
		array(),
		false,
		true
	);	
}

