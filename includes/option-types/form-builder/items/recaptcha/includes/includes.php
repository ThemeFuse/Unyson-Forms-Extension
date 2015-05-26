<?php if ( ! defined( 'FW' ) ) {
    die( 'Forbidden' );
}

fw_include_file_isolated( dirname(__FILE__) . '/option-type-recaptcha/class-fw-option-type-recaptcha.php' );
fw_include_file_isolated( dirname(__FILE__) . '/ReCaptcha/ReCaptcha.php' );
fw_include_file_isolated( dirname(__FILE__) . '/ReCaptcha/RequestMethod.php' );
fw_include_file_isolated( dirname(__FILE__) . '/ReCaptcha/RequestParameters.php' );
fw_include_file_isolated( dirname(__FILE__) . '/ReCaptcha/Response.php' );
fw_include_file_isolated( dirname(__FILE__) . '/ReCaptcha/RequestMethod/Post.php' );
