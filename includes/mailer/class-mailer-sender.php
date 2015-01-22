<?php if (!defined('FW')) die('Forbidden');

class FW_Ext_Form_Mailer_Sender
{
	private $config;

	public function __construct($config)
	{
		$this->config = $config;
	}

	public function send($to, $subject, $message)
	{
		$config = $this->get_prepared_config();

		if (!$config) {
			return array(
				'status'  => 0,
				'message' => __('Invalid email configuration', 'fw')
			);
		} else {
			switch ($config['_method']) {
				case 'smtp':
					return $this->send_through_smtp($to, $subject, $message, $config);
					break;
				case 'wpmail':
					return $this->send_through_wpmail($to, $subject, $message, $config);
					break;
			}
		}
	}

	public function get_prepared_config()
	{
		$settings = $this->config;
		if (
			!$settings
			|| empty($settings['general']['from_name'])
			|| empty($settings['general']['from_address'])
		) {
			return false;
		}

		$conf   = false;
		$method = trim($settings['method']);
		switch ($method) {
			case 'wpmail':
				$conf = array();
				break;
			case 'smtp':
				$smtp_settings  = $settings['smtp'];
				$host           = trim($smtp_settings['host']);
				$username       = trim($smtp_settings['username']);
				$password       = trim($smtp_settings['password']);

				if (
					$username
					&& $password
					&& fw_is_valid_domain_name($host)
				) {
					$conf = array(
						'host'      => $host,
						'username'  => $username,
						'password'  => $password,
						'secure'    => $smtp_settings['secure'],
						'port'      => trim($smtp_settings['port'])
					);

					if (!in_array($conf['secure'], array('ssl', 'tls'))) {
						$conf['secure'] = false;
					}

					// in case the port is missing or invalid
					if (empty($conf['port']) || !is_numeric($conf['port'])) {
						$conf['port'] = $conf['secure'] ? 465 : 25;
					}
				}
				break;
		}

		// add general settings
		if (false !== $conf) {
			$conf = array_merge($conf, array(
				'from_address'  => trim($settings['general']['from_address']),
				'from_name'     => trim($settings['general']['from_name']),
				'_method'       => $method
			));
		}

		return $conf;
	}

	private function send_through_smtp($to, $subject, $message, $config)
	{
		if(!class_exists('PHPMailer')) {
			require_once ABSPATH . WPINC . '/class-phpmailer.php';
		}

		$mailer = new PHPMailer();

		$mailer->isSMTP();
		$mailer->IsHTML(true);
		$mailer->Host       = $config['host'];
		$mailer->Port       = $config['port'];
		$mailer->SMTPSecure = $config['secure'];
		$mailer->SMTPAuth   = true;
		$mailer->Username   = $config['username'];
		$mailer->Password   = $config['password'];
		$mailer->From       = $config['from_address'];
		$mailer->FromName   = $config['from_name'];

		//$mailer->SMTPDebug = true;

		if (is_array($to)) {
			foreach ($to as $mail)
				$mailer->AddAddress($mail);
		} else {
			$mailer->AddAddress($to);
		}

		$mailer->Subject = $subject;
		$mailer->Body    = $message;

		$result = $mailer->send();

		$mailer->ClearAddresses();
		$mailer->ClearAllRecipients();

		unset($mailer);

		return $result
				? array('status' => 1, 'message' => __('Email sent', 'fw'))
				: array('status' => 0, 'message' => __('Could not send via smtp', 'fw'));
	}

	private function send_through_wpmail($to, $subject, $message, $config)
	{
		$headers = array();

		$headers[] = "From:". htmlspecialchars($config['from_name'], null, 'UTF-8')
			." <". htmlspecialchars($config['from_address'], null, 'UTF-8') .">";

		$result = wp_mail($to, $subject, $message, $headers);

		return $result
				? array('status' => 1, 'message' => __('Email sent', 'fw'))
				: array('status' => 0, 'message' => __('Could not send via wp_mail', 'fw'));
	}
}
