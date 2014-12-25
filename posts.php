<?php if (!defined('FW')) die('Forbidden');

register_post_type(fw()->extensions->get('forms')->get_post_type(), array(
	'labels' => array(
		'name'               => __('Forms', 'tfuse'),
		'singular_name'      => __('Form', 'tfuse'),
		'add_new'            => __('Add New', 'tfuse'),
		'add_new_item'       => __('Add New Form', 'tfuse'),
		'edit_item'          => __('Edit Form', 'tfuse'),
		'new_item'           => __('New Form', 'tfuse'),
		'all_items'          => __('Forms', 'tfuse'),
		'view_item'          => __('View Form', 'tfuse'),
		'search_items'       => __('Search Forms', 'tfuse'),
		'not_found'          => __('Nothing found', 'tfuse'),
		'not_found_in_trash' => __('Nothing found in Trash', 'tfuse'),
		'parent_item_colon'  => ''
	),
	'public'                => false,
	'publicly_queryable'    => false,
	'show_ui'               => true,
	'query_var'             => false,
	'has_archive'           => false,
	'menu_position'         => 5,
	'supports'              => array(''),
	/**
	 * Add as Appearance sub-menu only if user has access to the Appearance menu
	 * else (to prevent Access denied page) show as standard post type menu
	 */
	'show_in_menu' => current_user_can('switch_themes') ? 'themes.php' : null,
));
