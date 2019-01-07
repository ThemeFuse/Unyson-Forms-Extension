<?php if (!defined('FW')) die('Forbidden');

$options = array(
  'layout' => array(
    'type'    => 'select',
    'label'   => __( 'Field Layout', 'fw' ),
    'desc'    => __( 'Select choice display layout', 'fw' ),
    'choices' => array(
      'one-column'    => __( 'One column', 'fw' ),
      'two-columns'   => __( 'Two columns', 'fw' ),
      'three-columns' => __( 'Three columns', 'fw' ),
      'side-by-side'  => __( 'Side by side', 'fw' ),
    ),
  )
);