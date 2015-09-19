<?php if ( ! defined( 'FW' ) ) die( 'Forbidden' );

/**
 * Save and get data about a form by id
 * @since Contact-Forms 1.0.1
 * @internal
 */
class _FW_Ext_Contact_Form_DB_Data
{
	/**
	 * @var string As short as possible because wp_option name has length limit 64
	 */
	private static $wp_option_name_prefix = 'fw:ext:cf:fd:';

	private static function get_wp_option_name($form_id)
	{
		return self::$wp_option_name_prefix . $form_id;
	}

	/**
	 * Before 1.0.1 data was saved in a single wp option where all extensions save their data
	 * https://github.com/ThemeFuse/Unyson-Forms-Extension/commit/8192e81ca04fd06b215fe938087b2d880d7d42cc?diff=unified#diff-ec141aa42759cc0c7735796a2c061f60R30
	 *
	 * @param string $form_id
	 * @return array|null Deleted data
	 */
	private static function delete_data_from_old_location($form_id)
	{
		/**
		 * @var FW_Extension_Contact_Forms $contact_forms_extension
		 */
		$contact_forms_extension = fw_ext('contact-forms');

		$data_key = $contact_forms_extension->get_name() . '-' . $form_id;

		if ($data = $contact_forms_extension->get_db_data($data_key)) {
			$contact_forms_extension->set_db_data($data_key, null);
		}

		return $data;
	}

	public static function set($form_id, $data)
	{
		self::delete_data_from_old_location($form_id);

		return update_option(
			self::get_wp_option_name($form_id),
			$data,
			false
		);
	}

	public static function get($form_id)
	{
		if ($data = self::delete_data_from_old_location($form_id)) {
			self::set($form_id, $data);
		}

		return get_option(
			self::get_wp_option_name($form_id)
		);
	}
}
