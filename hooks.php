<?php if (!defined('FW')) die('Forbidden');

/**
 * @internal
 * @param array $widths Default widths
 * @return array
 */
function _filter_ext_forms_change_builder_item_widths($widths) {
	foreach ($widths as &$width) {
		$width['frontend_class'] .= ' form-builder-item';
	}

	return $widths;
}
add_filter('fw_builder_item_widths:form-builder', '_filter_ext_forms_change_builder_item_widths');

function _action_fw_ext_forms_option_types_init() {
	require dirname(__FILE__) .'/includes/option-types/form-builder/class-fw-option-type-form-builder.php';
}
add_action('fw_option_types_init', '_action_fw_ext_forms_option_types_init');
