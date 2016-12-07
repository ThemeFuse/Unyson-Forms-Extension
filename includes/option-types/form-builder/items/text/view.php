<?php if (!defined('FW')) die('Forbidden');
/**
 * @var array $item
 * @var array $attr
 */

$options = $item['options'];
?>
<div class="<?php echo esc_attr(fw_ext_builder_get_item_width('form-builder', $item['width'] .'/frontend_class')) ?>">
	<div class="field-text">
		<?php if ($options['label']): ?>
		<label for="<?php echo esc_attr($attr['id']) ?>"><?php echo fw_htmlspecialchars($options['label']) ?>
			<?php if ($options['required']): ?><sup>*</sup><?php endif; ?>
		</label>
		<?php endif; ?>
		<input <?php echo fw_attr_to_html($attr) ?>>
		<?php if ($options['info']): ?>
			<p><em><?php echo $options['info'] ?></em></p>
		<?php endif; ?>
	</div>
</div>