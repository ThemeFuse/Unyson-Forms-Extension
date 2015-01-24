<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

$options = array(
	'main' => array(
		'type'    => 'box',
		'title'   => '',
		'options' => array(
			'id'       => array(
				'type'  => 'hidden',
				'value' => uniqid( 'contact-form-' )
			),
			'builder'  => array(
				'type'    => 'tab',
				'title'   => __( 'Visual Editor', 'fw' ),
				'options' => array(
					'form' => array(
						'label' => false,
						'type'  => 'form-builder',
						'value' => array(
							'json' => json_encode( array(
								array(
									'type'      => 'form-header-title',
									'shortcode' => 'form_header_title',
									'width'     => '',
									'options'   => array(
										'title'    => '',
										'subtitle' => '',
									)
								)
							) )
						)
					),
				),
			),
			'settings' => array(
				'type'    => 'tab',
				'title'   => __( 'Settings', 'fw' ),
				'options' => array(
					'settings-options' => array(
						'title'   => __( 'Options', 'fw' ),
						'type'    => 'tab',
						'options' => array(
							'form_text_settings'  => array(
								'type'    => 'group',
								'options' => array(
									'submit_button_text' => array(
										'type'  => 'text',
										'label' => __( 'Submit button', 'fw' ),
										'value' => __( 'Send', 'fw' ),
									),
									'success_message'    => array(
										'type'  => 'text',
										'label' => __( 'Success message', 'fw' ),
										'value' => __( 'Message sent!', 'fw' ),
									),
									'failure_message'    => array(
										'type'  => 'text',
										'label' => __( 'Failure message', 'fw' ),
										'value' => __( 'Oops something went wrong.', 'fw' ),
									),
								),
							),
							'form_email_settings' => array(
								'type'    => 'group',
								'options' => array(
									'email_to' => array(
										'type'  => 'text',
										'label' => __( 'Email to', 'fw' ),
										'desc'  => __( 'The form will be sent to this email address. We recommend you to use an email that you verify often',
											'fw' ),
									),
								),
							),
						)
					),
					'mailer-options'   => array(
						'title'   => __( 'Mailer', 'fw' ),
						'type'    => 'tab',
						'options' => array(
							'mailer' => array(
								'label' => false,
								'type'  => 'mailer'
							)
						)
					)
				),
			),
			'html'     => array(
				'label' => false,
				'type'  => 'html',
				'html'  => ( ! fw_ext_mailer_is_configured() ) ? sprintf( __( 'Please configure %sMailer%s', 'fw' ), '<a href="#" id="fw-ext-contact-form-get-mailer-page" style="color:red">', '</a>' ) : '',
			)
		)
	)
);