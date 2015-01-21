<?php if (!defined('FW')) die('Forbidden');

class FW_Extension_Contact_Forms extends FW_Extension_Forms_Form
{
	public function get_form_type()
	{
		return 'contact';
	}

	public function get_form_type_title()
	{
		return __('Contact Form', 'fw');
	}

	public function get_form_options()
	{
		return array(
			'main' => array(
				'type'  => 'box',
				'title' => '',
				'options' => array(
					'builder' => array(
						'type' => 'tab',
						'title' => __('Visual Editor', 'fw'),
						'options' => array(
							'form' => array(
								'label' => false,
								'type'  => 'form-builder',
							),
						),
					),
					'settings' => array(
						'type' => 'tab',
						'title' => __('Settings', 'fw'),
						'options' => array(
							'form_header_settings' => array(
								'type' => 'group',
								'options' => array(
									'form_title' => array(
										'type'  => 'text',
										'label' => __('Form title', 'fw'),
										'value' => __('Please submit form from below', 'fw'),
									),
									'form_subtitle' => array(
										'type'  => 'text',
										'label' => __('Form subtitle', 'fw'),
										'value' => '',
									)
								),
							),
							'form_text_settings' => array(
								'type' => 'group',
								'options' => array(
									'submit_button_text' => array(
										'type'  => 'text',
										'label' => __('Submit button', 'fw'),
										'value' => __('Send', 'fw'),
									),
									'success_message' => array(
										'type'  => 'text',
										'label' => __('Success message', 'fw'),
										'value' => __('Message sent!', 'fw'),
									),
									'failure_message' => array(
										'type'  => 'text',
										'label' => __('Failure message', 'fw'),
										'value' => __('Oops something went wrong.', 'fw'),
									),
								),
							),
							'form_email_settings' => array(
								'type' => 'group',
								'options' => array(
									'email_to' => array(
										'type'  => 'text',
										'label' => __('Email to', 'fw'),
										'desc'  => __('The form will be sent to this email address. We recommend you to use an email that you verify often', 'fw'),
									),
								),
							),
						),
					),
				)
			)
		);
	}

	public function get_form_builder_type()
	{
		return 'form-builder';
	}

	public function get_form_builder_value($form_id)
	{
		return fw_get_db_post_option($form_id, 'form');
	}

	public function render_form($form_id, $view_data)
	{
		return $this->render_view('form',
			array_merge(
				$view_data,
				array(
					'submit_button_text' => fw_get_db_post_option( $form_id, 'submit_button_text', __( 'Send', 'fw' ) ),
				)
			)
		);
	}

	public function process_form($form_id, $form_values, $data)
	{
		$flash_id = 'fw_ext_contact_form_process';

		$to = fw_get_db_post_option($form_id, 'email_to');

		if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
			FW_Flash_Messages::add(
				$flash_id,
				__('Invalid destination email (please contact the site administrator)', 'fw'),
				'error'
			);
			return;
		}

		$result = fw_ext_mailer_send_mail(
			$to,
			get_the_title($form_id),
			$this->render_view('email', array(
				'form_id' => $form_id,
				'form_values' => $form_values,
				'shortcode_to_item' => $data['shortcode_to_item'],
			))
		);

		if ($result['status']) {
			FW_Flash_Messages::add(
				$flash_id,
				fw_get_db_post_option($form_id, 'success_message', __('Message sent!', 'fw'))
			);
		} else {
			FW_Flash_Messages::add(
				$flash_id,
				fw_get_db_post_option($form_id, 'failure_message', __('Oops something went wrong.', 'fw')) .
				' <em>('. $result['message'] .')</em>'
			);
		}
	}

	/**
	 * @internal
	 */
	protected function _init()
	{
		add_action('fw_ext_forms:save_post:'. $this->get_form_type(), array($this, '_action_post_form_type_save'));
	}

	/**
	 * @internal
	 */
	public function _action_post_form_type_save()
	{
		if (!fw_ext_mailer_is_configured()) {
			FW_Flash_Messages::add(
				'fw-ext-forms-'. $this->get_form_type() .'-mailer',
				str_replace(
					array(
						'{mailer_link}'
					),
					array(
						// the fw()->extensions->manager->get_extension_link() method is available starting with v2.1.7
						version_compare(fw()->manifest->get_version(), '2.1.7', '>=')
							? fw_html_tag('a', array('href' => fw()->extensions->manager->get_extension_link('mailer')), __('Mailer', 'fw'))
							: __('Mailer', 'fw')
					),
					__('Please configure the {mailer_link} extension.', 'fw')
				),
				'error'
			);
		}
	}
}
