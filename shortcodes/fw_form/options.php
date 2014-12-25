<?php if (!defined('FW')) die('Forbidden');

$choices = fw()->extensions->get('forms')->get_all_forms_select_choices();

if (!empty($choices)) {
	$options = array(
		'id' => array(
			'type' => 'select',
			'label' => __('Forms', 'fw'),
			'choices' => $choices
		)
	);
} else {
	$options = array(
		'id' => array( // make sure it exists to prevent notices when try to get ['id'] somewhere in the code
			'type' => 'hidden',
		),
		'no-forms' => array(
			'type' => 'html-full',
			'label' => false,
			'desc' => false,
			'html' =>
				'<div>'.
				'<h1 style="font-weight:100; text-align:center; margin-top:80px">'. __('No Forms Available', 'fw') .'</h1>'.
				'<p style="text-align:center">'.
				'<em>'.
				str_replace(
					array(
						'{br}',
						'{add_form_link}'
					),
					array(
						'<br/>',
						fw_html_tag('a', array(
							'href' => admin_url('post-new.php?post_type='. fw()->extensions->get('forms')->get_post_type()),
							'target' => '_blank',
						), __('create a new Form', 'fw'))
					),
					__('No Forms created yet. Please go to the {br}Forms page and {add_form_link}.', 'fw')
				).
				'</em>'.
				'</p>'.
				'</div>'
		)
	);
}