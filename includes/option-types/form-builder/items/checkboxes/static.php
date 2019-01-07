<?php if (!defined('FW')) die('Forbidden');

// native JavaScript solution for required checkboxes, explained and explored here
// https://stackoverflow.com/questions/5884582/required-attribute-on-multiple-checkboxes-with-the-same-name
wp_enqueue_script(
  'fw-ext-forms-required-inputs',
  $this->get_uri( '/static/js/frontend.js' ),
  array(),
  NULL,
  true
);