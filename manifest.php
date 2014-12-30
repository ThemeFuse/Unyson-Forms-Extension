<?php if (!defined('FW')) die('Forbidden');

$manifest = array();

$manifest['name'] = __('Forms', 'fw');
$manifest['description'] = __('This extension adds the possibility to create a contact form. Use the drag & drop form builder to create any contact form you\'ll ever want or need.', 'fw');
$manifest['version'] = '1.0.2';
$manifest['standalone'] = false;
$manifest['display'] = true;
$manifest['github_update'] = 'ThemeFuse/Unyson-Forms-Extension';
$manifest['requirements'] = array(
	'extensions' => array(
		'page-builder' => array(), // this is the only way to display the built form
	),
);
