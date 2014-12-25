<?php if (!defined('FW')) die('Forbidden');
/**
 * @var int $form_id
 * @var string $builder_html
 * @var string $submit_button_text
 */
?>

<?php if (trim(strip_tags($builder_html))): ?>
	<?php echo $builder_html ?>

	<div>
		<input type="submit" value="<?php echo esc_attr($submit_button_text) ?>" />
	</div>
<?php else: ?>
	<!-- no form builder content -->
<?php endif; ?>
