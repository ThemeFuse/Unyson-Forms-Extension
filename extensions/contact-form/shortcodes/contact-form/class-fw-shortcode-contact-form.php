<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode_Contact_Form extends FW_Shortcode
{
	public function _init() {
		add_action( 'admin_enqueue_scripts', array( $this, '_action_admin_add_static' ) );
	}

	public function _action_admin_add_static() {
		$static_file = $this->locate_path('/static.php');
		if ($static_file) {
			require_once $static_file;
		}
	}

	protected function _render($atts, $content = null, $tag = '')
	{
		return fw_ext('contact-form')->render( $atts );
	}
}