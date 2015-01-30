<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Shortcode_Contact_Form extends FW_Shortcode
{
	/**
	 * @internal
	 */
	public function _init()
	{
		if (is_admin()) {
			$this->load_item_type();
		}
	}

	private function load_item_type()
	{
		require $this->get_declared_path('/includes/page-builder-contact-form-item/class-page-builder-contact-form-item.php');
	}

	protected function _render($atts, $content = null, $tag = '')
	{
		return fw_ext('contact-forms')->render( $atts );
	}
}