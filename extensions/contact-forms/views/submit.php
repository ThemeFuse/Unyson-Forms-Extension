<?php if ( ! defined( 'FW' ) ) {
	die( 'Forbidden' );
}
/**
 * @var int $form_id
 * @var string $submit_button_text
 * @var array $extra_data
 */
?>
<div>
	<input type="submit" value="<?php echo esc_attr( $submit_button_text ) ?>"/>
</div>