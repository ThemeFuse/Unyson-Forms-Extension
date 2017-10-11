<?php if (!defined('FW')) die('Forbidden');

wp_register_script( 'g-recaptcha',
  'https://www.google.com/recaptcha/api.js?onload=fw_forms_builder_item_recaptcha_init&render=explicit&hl=' . get_locale(),
  array(),
  null,
  true
);

wp_enqueue_script( 'frontend-recaptcha',
  $this->get_uri( '/static/js/frontend-recaptcha.js' ),
  array( 'g-recaptcha' ),
  fw_ext( 'forms' )->manifest->get_version(),
  true
);
wp_localize_script( 'frontend-recaptcha', 'form_builder_item_recaptcha', array(
  'site_key' => $keys['site-key']
) );
