<?php if (!defined('FW')) die('Forbidden');

$manifest = array();

$manifest['name'] = __('Forms', 'fw');
$manifest['description'] = __(
	'This extension adds the possibility to create a contact form.'
	.' Use the drag & drop form builder to create any contact form you\'ll ever want or need.',
	'fw'
);
$manifest['uri'] = 'http://manual.unyson.io/en/latest/extension/forms/index.html';
$manifest['author'] = 'ThemeFuse';
$manifest['author_uri'] = 'http://themefuse.com/';
$manifest['github_repo'] = 'https://github.com/ThemeFuse/Unyson-Forms-Extension';
$manifest['version'] = '2.0.31';
$manifest['standalone'] = false;
$manifest['display'] = false;
$manifest['github_update'] = 'ThemeFuse/Unyson-Forms-Extension';
$manifest['requirements']  = array(
	'extensions' => array(
		'builder' => array(),
	),
);
