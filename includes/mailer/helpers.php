<?php if (!defined('FW')) die('Forbidden');

function fw_ext_forms_mailer_send_mail($to, $subject, $message) {
	return FW_Ext_Forms_Mailer::send($to, $subject, $message);
}

function fw_ext_forms_mailer_is_configured() {
	return FW_Ext_Forms_Mailer::is_configured();
}
