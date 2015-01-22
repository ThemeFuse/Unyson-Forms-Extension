<?php if (!defined('FW')) die('Forbidden');

$options = array(
	'mailer-wrapper' => array(
		'type' => 'box',
		'options' => array(
			'mailer' => array(
				'label' => false,
				'type' => 'multi',
				'inner-options' => array(
					'method' => array(
						'label'   => __('Send Method', 'fw'),
						'type'    => 'select',
						'value'   => 'wpmail',
						'choices' => array(
							'wpmail' => 'wp-mail',
							'smtp'   => 'SMTP',
						)
					),
					'smtp' => array(
						'type'      => 'group',
						'options'   => array(
							'smtp' => array(
								'label'         => false,
								'desc'          => false,
								'type'          => 'multi',
								'inner-options' => array(
									'host' => array(
										'label'     => __('Server Address', 'fw'),
										'type'      => 'text',
										'value'     => '',
									),
									'username' => array(
										'label'     => __('Username', 'fw'),
										'type'      => 'text',
										'value'     => '',
									),
									'password' => array(
										'label'     => __('Password', 'fw'),
										'type'      => 'password',
										'value'     => '',
									),
									'secure' => array(
										'label'     => __('Secure Connection', 'fw'),
										'type'      => 'radio',
										'value'     => 'no',
										'choices'   => array(
											'no'  => 'No',
											'ssl' => 'SSL',
											'tls' => 'TLS'
										)
									),
									'port' => array(
										'label'     => __('Custom Port', 'fw'),
										'desc'      => __('Optional - SMTP port number to use. Leave blank for default (SMTP - 25, SMTPS - 465).', 'fw'),
										'type'      => 'text',
										'attr'      => array(
											'maxlength' => 5,
										),
										'value'     => '',
									),
								),
								'value' => array()
							),
						)
					),
					'general'   => array(
						'label' => false,
						'desc'  => false,
						'type'  => 'multi',
						'inner-options' => array(
							'from_name' => array(
								'label'     => __('From Name', 'fw'),
								'desc'      => __('The form will look like was sent from this name.', 'fw'),
								'type'      => 'text',
								'value'     => '',
							),
							'from_address' => array(
								'label'     => __('From Address', 'fw'),
								'desc'      => __('The form will look like was sent from this address.', 'fw'),
								'type'      => 'text',
								'value'     => '',
							)
						),
						'value' => array()
					)
				),
			),
		)
	)
);
