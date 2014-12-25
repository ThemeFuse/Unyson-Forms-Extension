<?php if (!defined('FW')) die('Forbidden');

class FW_Shortcode_Fw_Form extends FW_Shortcode
{
	protected function _render($atts, $content = null, $tag = '')
	{
		// todo: use view

		return fw()->extensions->get('forms')->_render_shortcode($atts, $content);
	}
}