<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

fw_include_file_isolated( dirname( __FILE__ ) . '/option-type-recaptcha/class-fw-option-type-recaptcha.php' );

foreach (
	array(
		'ReCaptcha'         => 'ReCaptcha',
		'RequestMethod'     => 'RequestMethod',
		'RequestParameters' => 'RequestParameters',
		'Response'          => 'Response',
		'Post'              => 'RequestMethod/Post'
	)
	as $classname => $location
) {
	if ( ! class_exists( $classname ) ) {
		fw_include_file_isolated( dirname( __FILE__ ) . '/ReCaptcha/' . $location . '.php' );
	}

}