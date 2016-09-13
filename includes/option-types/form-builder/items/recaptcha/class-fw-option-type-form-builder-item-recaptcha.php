<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

fw_include_file_isolated( dirname( __FILE__ ) . '/includes/includes.php', true );


class FW_Option_Type_Form_Builder_Item_Recaptcha extends FW_Option_Type_Form_Builder_Item {

	protected $number_regex = '/^\-?([\d]+)?[,\.]?([\d]+)?$/';

	public function get_type() {
		return 'recaptcha';
	}

	private function get_uri( $append = '' ) {
		return fw_get_framework_directory_uri(
			'/extensions/forms/includes/option-types/' .
			$this->get_builder_type() . '/items/' .
			$this->get_type() . $append
		);
	}

	public function get_thumbnails() {
		return array(
			array(
				'html' =>
					'<div class="item-type-icon-title" data-hover-tip="' . __( 'Add a Recaptcha field', 'fw' ) . '">' .
					'<div class="item-type-icon"><img src="' . esc_attr( $this->get_uri( '/static/images/icon.png' ) ) . '" /></div>' .
					'<div class="item-type-title">' . __( 'Recaptcha', 'fw' ) . '</div>' .
					'</div>'
			)
		);
	}

	public function enqueue_static() {
		wp_enqueue_style(
			'fw-builder-' . $this->get_builder_type() . '-item-' . $this->get_type(),
			$this->get_uri( '/static/css/styles.css' )
		);

		wp_enqueue_script(
			'fw-builder-' . $this->get_builder_type() . '-item-' . $this->get_type(),
			$this->get_uri( '/static/js/scripts.js' ),
			array(
				'fw-events',
			),
			false,
			true
		);

		fw()->backend->enqueue_options_static( $this->get_options() );
	}

	/**
	 * @since 1.0.2
	 */
	public function get_item_localization() {
		return array(
			'options'  => $this->get_options(),
			'l10n'     => array(
				'item_title' => __( 'Recaptcha', 'fw' ),
				'label'      => __( 'Label', 'fw' ),
				'edit_label' => __( 'Edit Label', 'fw' ),
				'edit'       => __( 'Edit', 'fw' ),
				'delete'     => __( 'Delete', 'fw' ),
				'site_key'   => __( 'Set site key', 'fw' ),
				'secret_key' => __( 'Set secret key', 'fw' ),
			),
			'defaults' => array(
				'type'    => $this->get_type(),
				'options' => fw_get_options_values_from_input( $this->get_options(), array() )
			)
		);
	}

	private function get_options() {
		return array(
			'label'     => array(
				'type'  => 'text',
				'label' => __( 'Label', 'fw' ),
				'desc'  => __( 'Enter field label (it will be displayed on the web site)', 'fw' ),
				'value' => __( 'Recaptcha', 'fw' ),
			),
			'recaptcha' => array(
				'type'  => 'recaptcha',
				'label' => false,
				'value' => null,
			)
		);
	}

	protected function get_fixed_attributes( $attributes ) {
		return $attributes;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_value_from_attributes( $attributes ) {
		return $attributes;
		//return $this->get_fixed_attributes( $attributes );
	}

	/**
	 * {@inheritdoc}
	 */
	public function frontend_render( array $item, $input_value ) {

		$keys = fw_ext( 'forms' )->get_db_settings_option( 'recaptcha-keys' );

		if ( empty( $keys ) ) {
			return '';
		}

		wp_register_script(
			'g-recaptcha',
			'https://www.google.com/recaptcha/api.js?onload=fw_forms_builder_item_recaptcha_init&render=explicit&hl=' . get_locale(),
			array( 'jquery' ),
			null,
			true
		);

		wp_enqueue_script( 'frontend-recaptcha',
			$this->get_uri( '/static/js/frontend-recaptcha.js' ),
			array( 'g-recaptcha' ),
			fw_ext( 'forms' )->manifest->get_version(),
			true
		);
		wp_localize_script( 'frontend-recaptcha', 'form_builder_item_recaptcha', array(
			'site_key' => $keys['site-key']
		) );

		return fw_render_view(
			$this->locate_path( '/views/view.php', dirname( __FILE__ ) . '/view.php' ),
			array(
				'item'  => $item,
				'label' => ( isset( $input_value['label'] ) ) ? $input_value['label'] : __( 'Security Code', 'fw' ),
				'attr'  => array(
					'class' => 'form-builder-item-recaptcha',
				),
			)
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function frontend_validate( array $item, $input_value ) {

		$mesages = array(
			'not-configured' => __( 'Could not validate the form', 'fw' ),
			'not-human'      => __( 'Please fill the recaptcha', 'fw' ),
		);

		$keys = fw_ext( 'forms' )->get_db_settings_option( 'recaptcha-keys' );

		if ( empty( $keys ) ) {
			return $mesages['not-configured'];
		}

		$recaptcha = new ReCaptcha(
			$keys['secret-key'],
			(function_exists('ini_get') && ini_get('allow_url_fopen')) ? null : new ReCaptchaSocketPost()
		);
		$gRecaptchaResponse = FW_Request::POST( 'g-recaptcha-response' );

		if ( empty( $gRecaptchaResponse ) ) {
			return $mesages['not-human'];
		}
		$resp = $recaptcha->verify( $gRecaptchaResponse );

		if ( $resp->isSuccess() ) {
			return false;
		} else {
			$errors = $resp->getErrorCodes();

			return $mesages['not-human'];
		}
	}
}

FW_Option_Type_Builder::register_item_type( 'FW_Option_Type_Form_Builder_Item_Recaptcha' );
