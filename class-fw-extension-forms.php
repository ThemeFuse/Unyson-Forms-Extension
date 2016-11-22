<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

/** Sub extensions will extend this class */
require dirname( __FILE__ ) . '/includes/extends/class-fw-extension-forms-form.php';

/**
 * Build frontend forms
 */
class FW_Extension_Forms extends FW_Extension {

	/**
	 * Via this form will be rendered, validated and saved forms on frontend
	 * @var FW_Form
	 */
	private $frontend_form;

	/**
	 * @internal
	 */
	protected function _init() {
		$this->frontend_form = new FW_Form( 'fw_form', array(
			'render'   => array( $this, '_frontend_form_render' ),
			'validate' => array( $this, '_frontend_form_validate' ),
			'save'     => array( $this, '_frontend_form_save' ),
		) );

		add_filter('fw:form:nonce-name-data', array($this, '_filter_frontend_nonce_name_date'), 10, 3);
	}

	/**
	 * Render from items
	 *
	 * @param string $form_id
	 * @param array $form
	 * @param string $form_type
	 * @param string $submit_button
	 *
	 * @return string
	 */
	public final function render_form( $form_id, $form, $form_type, $submit_button = null ) {

		if ( empty( $form['json'] ) ) {
			return '';
		}

		ob_start();
		{
			$this->frontend_form->render( array(
				'builder_value' => json_decode( $form['json'], true ),
				'form_type'     => $form_type,
				'form_id'       => $form_id,
				'submit'        => $submit_button,
			) );
		}

		return ob_get_clean();
	}

	/**
	 * @internal
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	public function _frontend_form_render( $data ) {
		$form_id              = $data['data']['form_id'];
		$form_type            = $data['data']['form_type'];
		$submit_button        = $data['data']['submit'];
		$form_type_input_name = 'fw_ext_forms_form_type';
		$form_type_input_id   = 'fw_ext_forms_form_id';

		$data['attr']['data-fw-ext-forms-type'] = $form_type;
		$data['attr']['class'] = apply_filters('fw:ext:forms:attr:class', $data['attr']['class']);

		echo '<input type="hidden" name="' . $form_type_input_name . '" value="' . esc_attr( $form_type ) . '" />';
		echo '<input type="hidden" name="' . $form_type_input_id . '" value="' . esc_attr( $form_id ) . '" />';

		/**
		 * @var FW_Ext_Forms_Type $form_type
		 */
		$form_type = fw_ext( $form_type );

		/**
		 * @var FW_Option_Type_Form_Builder $builder
		 */
		$builder = fw()->backend->option_type( $form_type->get_form_builder_type() );

		echo $builder->frontend_render( $data['data']['builder_value'], FW_Request::POST() );

		if ( ! is_null( $submit_button ) ) {
			$data['submit']['html'] = $submit_button;
		}


		return $data;
	}

	/**
	 * @internal
	 *
	 * @param array $errors
	 *
	 * @return array
	 */
	public function _frontend_form_validate( $errors ) {
		$form_id   = FW_Request::POST( 'fw_ext_forms_form_id' );
		$form_type = FW_Request::POST( 'fw_ext_forms_form_type' );

		if ( empty( $form_id ) || empty( $form_type ) ) {
			return array(
				'invalid-form-id' => __( 'Unable to process the form', 'fw' )
			);
		}

		/**
		 * @var FW_Ext_Forms_Type $form_instance
		 */
		$form_instance = fw_ext( $form_type );

		if ( ! $this->_child_extension_is_valid( $form_instance ) ) {
			return array(
				'invalid-form-id' => __( 'Unable to process the form', 'fw' )
			);
		}

		$form = $form_instance->get_form_builder_value( $form_id );

		if ( empty( $form ) ) {
			return array(
				'invalid-form-id' => __( 'Unable to process the form', 'fw' )
			);
		}

		/**
		 * @var FW_Option_Type_Form_Builder $builder
		 */
		$builder = fw()->backend->option_type( $form_instance->get_form_builder_type() );

		if ( ! $builder instanceof FW_Option_Type_Form_Builder ) {
			return array(
				'invalid-form-id' => __( 'Unable to process the form', 'fw' )
			);
		}

		$items_errors = $builder->frontend_validate(
			json_decode( $form['json'], true ),
			FW_Request::POST()
		);

		if ( ! empty( $items_errors ) ) {
			$errors = array_merge( $errors, $items_errors );
		}

		return $errors;
	}

	/**
	 * @param array $fw_form_data
	 *
	 * @return array
	 *
	 * @internal
	 */
	public function _frontend_form_save( $fw_form_data ) {
		$form_id   = FW_Request::POST( 'fw_ext_forms_form_id' );
		$form_type = FW_Request::POST( 'fw_ext_forms_form_type' );

		/**
		 * @var FW_Ext_Forms_Type $form_instance
		 */
		$form_instance = fw_ext( $form_type );

		/**
		 * @var FW_Option_Type_Form_Builder $builder
		 */
		$builder = fw()->backend->option_type( $form_instance->get_form_builder_type() );

		/**
		 * {json: '...', ...}
		 */
		$builder_value = $form_instance->get_form_builder_value( $form_id );

		/**
		 * {[item], [item], ...}
		 */
		$builder_value_json_array = json_decode( $builder_value['json'], true );

		/**
		 * By default redirect to the same page
		 * to prevent form submit alert on page refresh
		 */
		$fw_form_data['redirect'] = fw_current_url();

		if ( empty( $builder_value_json_array ) ) {
			return $fw_form_data;
		}

		{
			/**
			 * {shortcode => item}
			 */
			$shortcode_to_item = array();

			$this->extract_shortcode_item( $shortcode_to_item, $builder_value_json_array );
		}

		$process_data = $form_instance->process_form(
			$builder->frontend_get_value_from_items(
				$builder_value_json_array,
				FW_Request::POST()
			),
			array(
				'shortcode_to_item' => $shortcode_to_item,
				'builder_value'     => $builder_value
			)
		);

		if ( is_array( $process_data ) ) {
			if ( isset( $process_data['redirect'] ) ) {
				$fw_form_data['redirect'] = $process_data['redirect'];
			}
		}

		do_action('fw_ext_forms_frontend_submit', array(
			'id' => $form_id,
			'type' => $form_type,
			'instance' => $form_instance,
			'process_data' => $process_data,
			/** @since 2.0.28 */
			'shortcode_to_item' => $shortcode_to_item,
			/** @since 2.0.28 */
			'builder_value'     => $builder_value
		));

		return $fw_form_data;
	}

	/**
	 * @internal
	 *
	 * @param mixed $child_extension_instance
	 *
	 * @return bool
	 */
	public function _child_extension_is_valid( $child_extension_instance ) {
		return is_subclass_of( $child_extension_instance, 'FW_Extension_Forms_Form' );
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
	 * @param string $val
	 * @param FW_Form $form
	 * @param array $render_data
	 * @return string
	 */
	public function _filter_frontend_nonce_name_date($val, $form, $render_data) {
		if ($form->get_id() === $this->frontend_form->get_id()) {
			if (isset($render_data['data']['form_id'])) {
				return $render_data['data']['form_id'];
			} else {
				return FW_Request::POST('fw_ext_forms_form_id', '');
			}
		}

		return $val;
	}
}
