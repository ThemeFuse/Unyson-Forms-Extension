<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}

class FW_Ext_Forms_Mailer {
	/**
	 * @internal
	 */
	protected function _init() {

	}

	public static function send( $to, $subject, $message ) {
		$sender = new FW_Ext_Form_Mailer_Sender(
			fw_ext( 'forms' )->get_db_settings_option( 'mailer' )
		);

		return $sender->send( $to, $subject, $message );
	}

	/**
	 * Check if extension settings options are valid
	 * @return bool
	 */
	public static function is_configured() {
		$sender = new FW_Ext_Form_Mailer_Sender(
			fw_ext( 'forms' )->get_db_settings_option( 'mailer' )
		);

		return (bool) $sender->get_prepared_config();
	}
}
