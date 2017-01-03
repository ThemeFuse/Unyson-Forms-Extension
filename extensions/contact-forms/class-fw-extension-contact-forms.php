<?php if ( ! defined( 'FW' ) ) die( 'Forbidden' );

class FW_Extension_Contact_Forms extends FW_Extension_Forms_Form {

	public function _init() {}

	/**
	 * {@inheritdoc}
	 */
	public function get_form_builder_type() {
		return 'form-builder';
	}

	public function get_form_builder_value( $form_id ) {
		$form = $this->get_form_db_data( $form_id );

		return ( empty( $form['form'] ) ? array() : $form['form'] );
	}

	/**
	 * @param $form_id
	 * @param $data
	 * * id - Form id
	 * * form - Builder value
	 * * email_to - Destination email
	 * * [subject_message]
	 * * [success_message]
	 * * [failure_message]
	 *
	 * @return bool
	 * @internal
	 */
	public function _set_form_db_data($form_id, $data) {
		if (!class_exists('_FW_Ext_Contact_Form_DB_Data')) {
			require_once dirname(__FILE__) .'/includes/helper/class--fw-ext-contact-form-db-data.php';
		}

		return _FW_Ext_Contact_Form_DB_Data::set($form_id, $data);
	}

	private function get_form_db_data($form_id) {
		if (!class_exists('_FW_Ext_Contact_Form_DB_Data')) {
			require_once dirname(__FILE__) .'/includes/helper/class--fw-ext-contact-form-db-data.php';
		}

		return _FW_Ext_Contact_Form_DB_Data::get($form_id);
	}

	/**
	 * @param array $data
	 * * id   - form id
	 * * form - builder value
	 * * [submit_button_text]
	 * @param array $view_data
	 * @return string
	 */
	public function render( $data, $view_data = array() ) {
		$form = $data['form'];

		if ( empty( $form ) ) {
			return '';
		}

		$form_id = $data['id'];
		$submit_button_text = empty( $data['submit_button_text'] )
			? __( 'Submit', 'fw' )
			: $data['submit_button_text'];

		/**
		 * @var FW_Extension_Forms $forms_extension
		 */
		$forms_extension = fw_ext( 'forms' );

		return $this->render_view(
			'form',
			array(
				'form_id'   => $form_id,
				'form_html' => $forms_extension->render_form(
					$form_id,
					$form,
					$this->get_name(),
					$this->render_view(
						'submit',
						array(
							'submit_button_text' => $submit_button_text,
							'form_id' => $form_id,
							'extra_data' => $view_data,
						)
					)
				),
				'extra_data' => $view_data,
			)
		);
	}

	public function process_form( $form_values, $data ) {
		$flash_id = 'fw_ext_contact_form_process';

		if ( empty( $form_values ) ) {
			FW_Flash_Messages::add(
				$flash_id,
				__( 'Unable to process the form', 'fw' ),
				'error'
			);

			return;
		}

		$form_id = FW_Request::POST( 'fw_ext_forms_form_id' );

		if ( empty( $form_id ) ) {
			FW_Flash_Messages::add(
				$flash_id,
				__( 'Unable to process the form', 'fw' ),
				'error'
			);
		}

		$form = $this->get_form_db_data( $form_id );

		if ( empty( $form ) ) {
			FW_Flash_Messages::add(
				$flash_id,
				__( 'Unable to process the form', 'fw' ),
				'error'
			);
		}

		{
			$to = array();

			foreach (array_map('trim', explode(',', $form['email_to'])) as $to_email) {
				if ( filter_var( $to_email, FILTER_VALIDATE_EMAIL ) ) {
					$to[] = $to_email;
				} else {
					FW_Flash_Messages::add(
						$flash_id,
						__( 'Invalid destination email (please contact the site administrator)', 'fw' ),
						'error'
					);

					return;
				}
			}

			$to = implode(',', $to);
		}

		$entry_data = array(
			'form_values'       => $form_values,
			'shortcode_to_item' => $data['shortcode_to_item'],
			/** @since 2.0.30 */
			'cc'  => apply_filters('fw:ext:contact-forms:email:cc',  array( /* 'john@smith.com' => 'John Smith' */ )),
			/** @since 2.0.30 */
			'bcc' => apply_filters('fw:ext:contact-forms:email:bcc', array( /* 'john@smith.com' => 'John Smith' */ )),
		);

		/**
		 * Use the first email filed as Reply-To header
		 */
		foreach ($entry_data['shortcode_to_item'] as $item) {
			if ($item['type'] === 'email' && $item['options']['required']) {
				$entry_data['reply_to'] = $entry_data['form_values'][ $item['shortcode'] ];
				break;
			}
		}

		$result = fw_ext_mailer_send_mail(
			$to,
			fw_akg('subject_message', $form, ''),
			$this->render_view( 'email', $entry_data ),
			$entry_data
		);

		if ( $result['status'] ) {
			do_action('fw:ext:contact-forms:sent', $entry_data);

			FW_Flash_Messages::add(
				$flash_id,
				fw_akg('success_message', $form, __( 'Message sent!', 'fw' ) ),
				'success'
			);
		} else {
			FW_Flash_Messages::add(
				$flash_id,
				fw_akg('failure_message', $form, __( 'Oops something went wrong.', 'fw' ) ) . ' ' . $result['message'],
				'error'
			);
		}
	}

	/**
	 * @internal
	 */
	public function _action_post_form_type_save() {
		if ( ! fw_ext_mailer_is_configured() ) {
			FW_Flash_Messages::add(
				'fw-ext-forms-' . $this->get_form_type() . '-mailer',
				str_replace(
					array(
						'{mailer_link}'
					),
					array(
						// the fw()->extensions->manager->get_extension_link() method is available starting with v2.1.7
						version_compare( fw()->manifest->get_version(), '2.1.7', '>=' )
							? fw_html_tag( 'a',
							array( 'href' => fw()->extensions->manager->get_extension_link( 'forms' ) ),
							__( 'Mailer', 'fw' ) )
							: __( 'Mailer', 'fw' )
					),
					__( 'Please configure the {mailer_link} extension.', 'fw' )
				),
				'error'
			);
		}
	}

	/**
	 * Returns value of the form option
	 *
	 * @param string $id
	 * @param null|string $multikey
	 *
	 * @return mixed|null
	 */
	public function get_option( $id, $multikey = null ) {
		$form = $this->get_form_db_data( $id );

		if ( empty( $form ) ) {
			return null;
		}

		if ( is_null( $multikey ) ) {
			return $form;
		}

		return fw_akg( $multikey, $form );
	}
}
