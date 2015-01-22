<?php if (!defined('FW')) die('Forbidden');

/** Sub extensions will extend this class */
require dirname(__FILE__) .'/includes/extends/class-fw-extension-forms-form.php';

/**
 * Build frontend forms
 */
class FW_Extension_Forms extends FW_Extension
{
	/**
	 * @internal
	 */
	public function _child_extension_is_valid($child_extension_instance)
	{
		return is_subclass_of($child_extension_instance, 'FW_Extension_Forms_Form');
	}

	public function get_post_type()
	{
		return 'fw-form';
	}

	public function get_shortcode_name()
	{
		return 'fw_form';
	}

	/**
	 * Via this form will be rendered, validated and saved forms on frontend
	 * @var FW_Form
	 */
	private $frontend_form;

	public function get_posts_list_filter_GET_parameter()
	{
		return 'fw-form-type';
	}

	/**
	 * @internal
	 */
	protected function _init()
	{
		$this->frontend_form = new FW_Form('fw_form', array(
			'render'   => array($this, '_frontend_form_render'),
			'validate' => array($this, '_frontend_form_validate'),
			'save'     => array($this, '_frontend_form_save'),
		));

		$this->add_filters();
		$this->add_actions();
	}

	private function add_filters()
	{
		add_filter('fw_post_options', array($this, '_filter_post_options'), 10, 2);
		add_filter('manage_'. $this->get_post_type() .'_posts_columns', array($this, '_filter_admin_forms_list_columns'));
		add_filter('views_edit-'. $this->get_post_type(), array($this, '_filter_posts_list_filter_links'));
		add_filter('post_row_actions', array($this, '_filter_post_row_actions'), 12, 2);
	}

	private function add_actions()
	{
		add_action('manage_'. $this->get_post_type() .'_posts_custom_column', array($this, '_action_admin_forms_list_column_value'), 10, 2);
		add_action('admin_enqueue_scripts', array($this, '_admin_action_enqueue_static'));
		add_action('pre_get_posts', array($this, '_action_pre_get_posts'));
		add_action('admin_menu', array($this, '_action_replace_post_submit_meta_box'));
		add_action('save_post', array($this, '_action_save_post'), 10, 2);
		add_filter('post_updated_messages', array($this, '_filter_change_updated_messages'));

		if ( is_admin() ) {
			if ( is_admin() ) {
				add_action(
					'fw_extension_settings_form_render:' . $this->get_name(),
					array( $this, '_action_extension_settings_form_render' )
				);
			}
		}
	}

	/**
	 * Return 'choices' for "Form Type" select on the "Add New Form" page in admin
	 */
	public function get_all_forms_select_choices() {
		/**
		 * @var FW_Extension_Forms $extension
		 */
		$extension = fw()->extensions->get('forms');

		$cache_key = $extension->get_cache_key() .'/all_forms_select_choices';

		try {
			return FW_Cache::get($cache_key);
		} catch (FW_Cache_Not_Found_Exception $e) {
			global $wpdb;

			$forms_types = $extension->get_form_types();

			$choices = array();

			foreach (
				$wpdb->get_results(
					$wpdb->prepare(
						"SELECT ID, post_title
					FROM {$wpdb->posts}
					WHERE post_type = '%s' AND post_status = 'publish'",
						$extension->get_post_type()
					),
					ARRAY_A
				) as $post
			) {
				$form_type = fw_get_db_post_option($post['ID'], 'type');

				if (!$form_type || !isset($forms_types[$form_type])) {
					continue;
				}

				if (!isset($choices[$form_type])) {
					$choices[$form_type] = array(
						'attr' => array(
							'label' => $forms_types[$form_type]->get_form_type_title()
						),
						'choices' => array()
					);
				}

				$choices[$form_type]['choices'][ $post['ID'] ] = $post['post_title'];
			}

			FW_Cache::set($cache_key, $choices);

			return $choices;
		}
	}

	/**
	 * Children extensions that extended FW_Extension_Forms_Form class
	 * @param null|string $form_type Get specific form type
	 * @return false|FW_Extension_Forms_Form|FW_Extension_Forms_Form[]
	 */
	public function get_form_types($form_type = null)
	{
		$cache_key = $this->get_cache_key() .'/types';

		try {
			$types = FW_Cache::get($cache_key);
		} catch (FW_Cache_Not_Found_Exception $e) {
			$types = array();

			foreach ($this->get_children() as $ext_name => $instance) {
				/** @var FW_Extension_Forms_Form $instance */

				$type = $instance->get_form_type();

				if (isset($types[ $instance->get_form_type() ])) {
					trigger_error('Forms type "'. $type .'" already exists', E_USER_ERROR);
				}

				$types[ $type ] = $instance;
			}

			FW_Cache::set($cache_key, $types);
		}

		if (is_null($form_type)) {
			return $types;
		} else {
			if (isset($types[$form_type])) {
				return $types[$form_type];
			} else {
				return false;
			}
		}
	}

	/**
	 * @internal
	 */
	public function _action_extension_settings_form_render() {
		wp_enqueue_script(
			'fw_option_email_settings',
			$this->get_declared_URI( '/includes/mailer/static/js/scripts.js' ),
			array( 'jquery' ),
			false,
			true
		);
	}

	/**
	 * @param array $options
	 * @param string $post_type
	 *
	 * @return array
	 * @internal
	 */
	public function _filter_post_options($options, $post_type)
	{
		if ($this->get_post_type() !== $post_type) {
			return $options;
		}

		if (fw_is_post_edit()) {
			global $post;

			if (!$post) {
				trigger_error('No global $post', E_USER_WARNING);
				return array();
			}

			$type  = fw_get_db_post_option($post->ID, 'type');
			$types = $this->get_form_types();

			if (!isset($types[$type])) {
				return array();
			}

			$options = $types[$type]->get_form_options();

			/**
			 * hidden input to store form type
			 */
			$options['form-type-holder'] = array(
				'type' => 'box',
				'options' => array(
					/**
					 * Option id that stores the form type
					 */
					'type' => array(
						'value' => $type,
						'save-in-separate-meta' => true,
						'type' => 'hidden',
						'desc' => '<script type="text/javascript">'.
							'jQuery(document).ready(function($){ $("#fw-options-box-form-type-holder").hide(); });'.
						'</script>',
					)
				)
			);

			return $options;
		} else {
			$types_select_options = array();

			foreach ($this->get_form_types() as $type => $instance) {
				$types_select_options[$type] = $instance->get_form_type_title();
			}

			return array(
				'main' => array(
					'type'  => 'box',
					'title' => __('Form Settings', 'fw'),
					'options' => array(
						'title' => array(
							'label' => __('Title', 'fw'),
							'type' => 'html-fixed',
							'html' => fw_html_tag('input', array(
								'type' => 'text',
								'name' => 'post_title',
								'spellcheck' => 'true',
								'autocomplete' => 'off',
								'required' => 'required',
							)),
							'desc'  => __('Choose the form title (for internal use)', 'fw'),
						),
						'type' => array(
							'label' => __('Type', 'fw'),
							'type'  => 'select',
							'desc'  => __('Choose the type of your form', 'fw'),
							'choices' => $types_select_options,
							'save-in-separate-meta' => true,
						),
					),
				),
			);
		}
	}

	/**
	 * Render form shortcode on frontend
	 * @internal
	 */
	public function _render_shortcode($atts, $shortcode_content)
	{
		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			$this->get_shortcode_name()
		);

		$form_id = (int)$atts['id'];

		if (get_post_type($form_id) !== $this->get_post_type()) {
			return '[! invalid form id: '. $form_id .' ]';
		}

		$form_type  = fw_get_db_post_option($form_id, 'type');
		$form_types = $this->get_form_types();

		if (!isset($form_types[$form_type])) {
			return '[! invalid form ('. $form_id .') type: '. fw_htmlspecialchars($form_type) .' ]';
		}

		$builder_value = $form_types[$form_type]->get_form_builder_value($form_id);

		if (empty($builder_value) || !is_array($builder_value) || !isset($builder_value['json'])) {
			return '[! invalid form ('. $form_id .') builder value ]';
		}

		ob_start();
		{
			$this->frontend_form->render(array(
				'form_id'       => $form_id,
				'form_type'     => $form_type,
				'builder_value' => json_decode($builder_value['json'], true)
			));
		}
		return ob_get_clean();
	}

	/**
	 * @internal
	 */
	public function _frontend_form_render($data)
	{
		$form_id = $data['data']['form_id'];

		$form_id_input_name = 'fw_ext_forms_form_id';

		echo '<input type="hidden" name="'. $form_id_input_name .'" value="'. esc_attr($form_id) .'" />';

		$form_type  = fw_get_db_post_option($form_id, 'type');
		$form_types = $this->get_form_types();

		/**
		 * @var FW_Option_Type_Form_Builder $builder
		 */
		$builder = fw()->backend->option_type( $form_types[$form_type]->get_form_builder_type() );

		echo $form_types[$form_type]->render_form($form_id, array(
			'builder_html' => $builder->frontend_render($data['data']['builder_value'], FW_Request::POST()),
			'form_id' => $form_id
		));

		$data['submit']['html'] = '';

		return $data;
	}

	/**
	 * @internal
	 */
	public function _frontend_form_validate($errors)
	{
		$form_id_input_name = 'fw_ext_forms_form_id';

		do {
			if (!isset($_POST[$form_id_input_name]) || get_post_type($_POST[$form_id_input_name]) !== $this->get_post_type()) {
				$errors[$form_id_input_name] = sprintf(__('Invalid form id: %s', 'fw'), fw_htmlspecialchars($_POST[$form_id_input_name]));
				break;
			}

			$form_id    = intval($_POST[$form_id_input_name]);
			$form_type  = fw_get_db_post_option($form_id, 'type');
			$form_types = $this->get_form_types();

			if (!isset($form_types[$form_type])) {
				$errors[$form_id_input_name] = 'Invalid form ('. $form_id .') type: '. fw_htmlspecialchars($form_type);
				break;
			}

			$form_type_inst = $form_types[$form_type];

			$builder_value = $form_type_inst->get_form_builder_value($form_id);

			if (empty($builder_value) || !is_array($builder_value) || !isset($builder_value['json'])) {
				$errors[$form_id_input_name] = 'Invalid form ('. $form_id .') builder value';
				break;
			}

			/**
			 * @var FW_Option_Type_Form_Builder $builder
			 */
			$builder = fw()->backend->option_type($form_type_inst->get_form_builder_type());

			$items_errors = $builder->frontend_validate(
				json_decode($builder_value['json'], true),
				FW_Request::POST()
			);

			if (!empty($items_errors)) {
				$errors = array_merge($errors, $items_errors);
			}
		} while(false);

		return $errors;
	}

	/**
	 * @param array $fw_form_data
	 *
	 * @return array
	 *
	 * @internal
	 */
	public function _frontend_form_save($fw_form_data)
	{
		$form_id        = intval($_POST['fw_ext_forms_form_id']);
		$form_type      = fw_get_db_post_option($form_id, 'type');
		$form_types     = $this->get_form_types();
		$form_type_inst = $form_types[$form_type];

		/**
		 * @var FW_Option_Type_Form_Builder $builder
		 */
		$builder = fw()->backend->option_type($form_type_inst->get_form_builder_type());

		/**
		 * {json: '...', ...}
		 */
		$builder_value = $form_type_inst->get_form_builder_value($form_id);

		/**
		 * {[item], [item], ...}
		 */
		$builder_value_json_array = json_decode($builder_value['json'], true);

		/**
		 * By default redirect to the same page
		 * to prevent form submit alert on page refresh
		 */
		$fw_form_data['redirect'] = fw_current_url();

		if (empty($builder_value_json_array)) {
			return $fw_form_data;
		}

		{
			/**
			 * {shortcode => item}
			 */
			$shortcode_to_item = array();

			$this->extract_shortcode_item($shortcode_to_item, $builder_value_json_array);
		}

		$process_data = $form_type_inst->process_form(
			$form_id,
			$builder->frontend_get_value_from_items(
				$builder_value_json_array,
				FW_Request::POST()
			),
			array(
				'shortcode_to_item' => $shortcode_to_item,
				'builder_value' => $builder_value
			)
		);

		if (is_array($process_data)) {
			if (isset($process_data['redirect'])) {
				$fw_form_data['redirect'] = $process_data['redirect'];
			}
		}

		return $fw_form_data;
	}

	/**
	 * Add custom columns to forms posts list in admin
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function _filter_admin_forms_list_columns($columns)
	{
		return array(
			'cb' => $columns['cb'],
			'title' => $columns['title'],
			'form_type' => __('Type', 'fw'),
		);
	}

	/**
	 * Provide values for the forms posts list table custom columns
	 *
	 * @param string $column
	 * @param int $post_id
	 */
	public function _action_admin_forms_list_column_value($column, $post_id)
	{
		switch ($column) {
			case 'form_type' :
				$type  = fw_get_db_post_option($post_id, 'type');
				$types = $this->get_form_types();

				if (!isset($types[$type])) {
					echo sprintf(__('Form type "%s" does not exist', 'fw'), $type);
					break;
				}

				echo $types[$type]->get_form_type_title();
				break;
			default :
				break;
		}
	}

	/**
	 * @internal
	 */
	public function _admin_action_enqueue_static()
	{
		$is_add_or_edit_post_screen = fw_current_screen_match(
			array(
				'only' => array(
					// add post page
					array(
						'post_type' => $this->get_post_type(),
						'action' => 'add'
					),
					// edit post page
					array(
						'post_type' => $this->get_post_type(),
						'base' => 'post'
					)
				)
			));

		if (!$is_add_or_edit_post_screen) {
			return;
		}

		wp_enqueue_style(
			'fw-extension-' . $this->get_name() . '-post',
			$this->get_declared_URI('/static/css/post.css'),
			array(),
			fw()->manifest->get_version()
		);
	}

	/**
	 * Extract recursive all items in one level array
	 * @param array $extracted {shortcode => item}
	 * @param $items array, some items can have sub-items in the '_items' key
	 */
	private function extract_shortcode_item(&$extracted, &$items)
	{
		if (!is_array($items)) {
			return;
		}

		foreach ($items as &$item) {
			$extracted[ $item['shortcode'] ] = $item;

			if (!empty($item['_items'])) {
				$this->extract_shortcode_item($extracted, $item['_items']);
			}
		}
	}

	/**
	 * Add links in admin forms list to be able to filter forms by type
	 * @internal
	 */
	public function _filter_posts_list_filter_links($links)
	{
		$new_links = array(
			'all' => $links['all'],
		);

		unset($links['all'], $links['publish']);

		/**
		 * @var wpdb $wpdb
		 */
		global $wpdb;

		// count form types
		{
			$count = array();

			$IN_types = array();
			foreach (array_keys($this->get_form_types()) as $type) {
				$IN_types[] = $wpdb->prepare('%s', $type);
			}

			if ($IN_types) {
				// select count(forms) for every type in one select (not one select for each status)
				$dbrows = $wpdb->get_results(
					"SELECT " .
					"pm.meta_value as type, " .
					"COUNT(1) as forms " .
					"    FROM {$wpdb->postmeta} AS pm " .
					"INNER JOIN {$wpdb->posts} p ON (p.ID = pm.post_id) " .
					"    WHERE p.post_status = 'publish' " .
					"        AND pm.meta_key = 'fw_option:type' " .
					"        AND pm.meta_value IN (" . implode(',', $IN_types) . ") " .
					"GROUP BY pm.meta_value " .
					"LIMIT " . count($IN_types)
				);

				foreach ($dbrows as $row) {
					$count[$row->type] = intval($row->forms);
				}

				unset($IN_types, $dbrows, $row);
			}
		}

		if (count($count) > 1) {
			$param     = $this->get_posts_list_filter_GET_parameter();
			$base_link = 'edit.php?post_type=' . $this->get_post_type() . '&post_status=~&';

			foreach ( $this->get_form_types() as $type => $form_type_inst ) {
				if ( ! isset( $count[ $type ] ) ) {
					continue;
				}

				$new_links[ 'form-type:' . $type ] = fw_html_tag( 'a', array(
					'href'  => $base_link . $param . '=' . $type,
					'class' => ( isset( $_GET[ $param ] ) && $_GET[ $param ] === $type ) ? 'current' : '',
				), $form_type_inst->get_form_type_title() . ' <span class="count">(' . $count[ $type ] . ')</span>' );
			}
		}

		return $new_links + $links;
	}

	/**
	 * @param $query
	 * @internal
	 */
	public function _action_pre_get_posts($query)
	{
		/** @var WP_Query $query */

		/**
		 * Filter admin posts list by form type
		 */
		do {
			if (!$query->is_admin) {
				break;
			}

			if ($query->is_single) {
				break;
			}

			if ($query->get('post_type') !== $this->get_post_type()) {
				break;
			}

			$type = trim(FW_Request::GET($this->get_posts_list_filter_GET_parameter()));

			if (
				!empty($type)
				&&
				in_array($type, array_keys($this->get_form_types()))
			) {
				$query->set('meta_key', 'fw_option:type');
				$query->set('meta_value', $type);
			}
		} while(false);
	}

	/**
	 * @internal
	 *
	 * @return string
	 */
	public function _get_link() {
		return 'edit.php?post_type=' . $this->get_post_type();
	}

	/**
	 * @internal
	 */
	public function _action_replace_post_submit_meta_box() {
		remove_meta_box('submitdiv', $this->get_post_type(), 'core');

		add_meta_box(
			'submitdiv',
			__('Publish', 'fw'),
			array($this, '_render_submit_meta_box'),
			$this->get_post_type(),
			'side'
		);
	}

	/**
	 * @internal
	 * @param WP_Post $post
	 * @param array $args
	 */
	public function _render_submit_meta_box($post, $args = array())
	{
		// a modified version of post_submit_meta_box() (wp-admin/includes/meta-boxes.php, line 12)

		if (fw_is_post_edit()) {
			$type = fw_get_db_post_option($post->ID, 'type');
			$form_type = $this->get_form_types($type);

			if (!$form_type) {
				$form_type_title = sprintf(__('Not existing type: %s', 'fw'), $type);
			} else {
				$form_type_title = $form_type->get_form_type_title();
			}

			unset($form_types);

			fw_render_view(
				$this->get_declared_path('/views/backend/submit-box-edit.php'), array(
				'post' => $post,
				'options' => array(
					'title' => array(
						'label' => __('Title', 'fw'),
						'type' => 'html-fixed',
						'html' => fw_html_tag('input', array(
							'type' => 'text',
							'name' => 'post_title',
							'value' => $post->post_title,
							'spellcheck' => 'true',
							'autocomplete' => 'off',
							'required' => 'required',
						)),
					),
					'form_type_preview' => array(
						'label' => __('Type', 'fw'),
						'type' => 'select',
						'choices' => array('' => $form_type_title),
						'attr' => array('disabled' => 'disabled'),
					),
				),
			), false);
		} else {
			fw_render_view(
				$this->get_declared_path('/views/backend/submit-box-add.php'), array(
				'post' => $post,
			), false);
		}
	}

	/**
	 * @internal
	 */
	public function _action_save_post($post_id, $post)
	{
		if (!fw_is_real_post_save($post_id) || $post->post_type !== $this->get_post_type()) {
			return;
		}

		$type = fw_get_db_post_option($post_id, 'type');
		$form_type = $this->get_form_types($type);

		if (!$form_type) {
			do_action('fw_ext_forms:save_post:unknown_type', $type);
		} else {
			do_action('fw_ext_forms:save_post:'. $type, $form_type);
		}
	}

	/**
	 * Remove invalid link "View post" from notice messages after form save
	 * @internal
	 */
	function _filter_change_updated_messages($messages)
	{
		/** @var wpdb $wpdb */
		global $post;

		if ($post->post_type !== $this->get_post_type()) {
			return $messages;
		}

		$obj = get_post_type_object($post->post_type);
		$singular = $obj->labels->singular_name;

		$messages[$post->post_type] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf(__('%s updated.', 'fw'), $singular),
			2 => __('Custom field updated.', 'fw'),
			3 => __('Custom field deleted.', 'fw'),
			4 => sprintf(__('%s updated.', 'fw'), $singular),
			5 => isset($_GET['revision'])
				? sprintf(__('%s restored to revision from %s', 'fw'), $singular, wp_post_revision_title((int)$_GET['revision'], false))
				: false,
			6 => sprintf(__('%s published.', 'fw'), $singular),
			7 => __('Page saved.', 'fw'),
			8 => sprintf(__('%s submitted.', 'fw'), $singular),
			9 => sprintf(__('%s scheduled for: %s.', 'fw'), $singular,
				'<strong>' . date_i18n('M j, Y @ G:i') . '</strong>'),
			10 => sprintf(__('%s draft updated.', 'fw'), $singular),
		);

		return $messages;
	}

	/**
	 * Remove the "Quick Edit" link
	 * @param array $actions
	 * @param WP_Post $post
	 * @return array
	 * @internal
	 */
	public function _filter_post_row_actions($actions, $post)
	{
		if ($post->post_type !== $this->get_post_type()) {
			return $actions;
		}

		unset($actions['inline hide-if-no-js'], $actions['view']);

		return $actions;
	}
}
