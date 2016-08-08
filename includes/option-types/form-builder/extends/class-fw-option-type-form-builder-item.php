<?php if (!defined('FW')) die('Forbidden');

/**
 * Extend this class to create items for form-builder option type
 */
abstract class FW_Option_Type_Form_Builder_Item extends FW_Option_Type_Builder_Item
{
	final public function get_builder_type()
	{
		return 'form-builder';
	}

	/**
	 * Render item html for frontend form
	 *
	 * @param array $item Attributes from Backbone JSON
	 * @param null|string|array $input_value Value submitted by the user
	 * @return string HTML
	 */
	abstract public function frontend_render(array $item, $input_value);

	/**
	 * Validate item on frontend form submit
	 *
	 * @param array $item Attributes from Backbone JSON
	 * @param null|string|array $input_value Value submitted by the user
	 * @return null|string Error message
	 */
	abstract public function frontend_validate(array $item, $input_value);

	/**
	 * Search relative path in '/extensions/forms/{builder_type}/items/{item_type}/'
	 *
	 * @param string $rel_path
	 * @param string $default_path Used if no path found
	 *
	 * @return false|string
	 */
	final protected function locate_path($rel_path, $default_path)
	{
		if ($path = fw()->extensions->get('forms')->locate_path('/'. $this->get_builder_type() .'/items/'. $this->get_type() . $rel_path)) {
			return $path;
		} else {
			return $default_path;
		}
	}

	/**
	 * Tells if the form input is only for visual rendering in form purpose and will not be used to submit any data
	 *
	 * @return bool
	 */
	public function visual_only() {
		return false;
	}

	/**
	 * Returns the value of the input after the form successfully was submitted
	 * This method ca be used in order to filter of the modify the submit ted value
	 *
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	public function get_value_from_item( $value ) {
		return $value;
	}

	/**
	 * Search in '/extensions/forms/{builder_type}/items/{item_type}/options/options.php'
	 * @return array
	 * @since 2.0.23
	 */
	final protected function get_extra_options() {
		if ($extra_options = $this->locate_path( '/options/options.php', false )) {
			$extra_options = fw_get_variables_from_file($extra_options, array('options' => array()));
			return $extra_options['options'];
		} else {
			return array();
		}
	}
}
