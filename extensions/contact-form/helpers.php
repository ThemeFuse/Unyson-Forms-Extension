<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/**
 * @param string $needle
 * @param array $haystack
 * @param mixed $default
 *
 * @return mixed
 */
function fw_ext_contact_form_search_option( $needle, $haystack, $default = null ) {
	if ( ! is_array( $haystack ) || empty( $haystack ) || ! is_string( $needle ) || empty( $needle ) ) {
		return $default;
	}

	$containers = array( 'tab', 'box', 'group', );

	foreach ( $haystack as $key => $value ) {
		if ( $key == $needle && isset( $value['type'] ) && ! in_array( $value['type'], $containers ) ) {
			return $haystack[$key];
		}

		if ( isset( $value['type'] ) && in_array( $value['type'], $containers ) && isset( $value['options'] ) ) {
			$return = fw_ext_contact_form_search_option( $needle, $value['options'] );
			if ( ! is_null( $return ) ) {
				return $return;
			}
		}
	}

	return $default;
}