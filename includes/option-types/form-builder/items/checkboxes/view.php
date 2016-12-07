<?php if (!defined('FW')) die('Forbidden');
/**
 * @var array $item
 * @var array $choices
 * @var array $value
 */

$options = $item['options'];

switch ($options['layout']) {
	case 'one-column': $columns = 1; break;
	case 'two-columns': $columns = 2; break;
	case 'three-columns': $columns = 3; break;
	default: $columns = 0;
}
?>
<?php if (empty($choices)): ?>
	<!-- checkboxes not displayed: no choices -->
<?php else: ?>
	<div class="<?php echo esc_attr(fw_ext_builder_get_item_width('form-builder', $item['width'] .'/frontend_class')) ?>">
		<div class="field-checkbox input-styled">
			<?php if ($options['label']): ?>
			<label><?php echo fw_htmlspecialchars($options['label']) ?>
				<?php if ($options['required']): ?><sup>*</sup><?php endif; ?>
			</label>
			<?php endif; ?>
			<div class="custom-checkbox field-columns-<?php echo esc_attr($columns); ?>">
				<?php if ($columns > 1): ?>
					<?php
					$choices_count = count($choices);
					$choices_per_column = abs($choices_count / $columns)
					                      + (($choices_count % $columns > $columns / 2) ? 1 : 0);
					$counter = 0;
					?>
					<div class="field-column">
					<?php while ($choice = array_shift($choices)): ?>
						<?php $choice['id'] = 'rand-'. fw_unique_increment(); ?>
						<div class="options">
							<input <?php echo fw_attr_to_html($choice) ?> />
							<label for="<?php echo esc_attr($choice['id']) ?>"><?php echo $choice['value'] ?></label>
						</div>
						<?php if (!(++$counter % $choices_per_column)): ?>
							</div><div class="field-column">
						<?php endif; ?>
					<?php endwhile; ?>
					</div>
				<?php else: ?>
					<?php foreach ($choices as $choice):
						$choice['id'] = 'rand-'. fw_unique_increment(); ?>
						<div class="options">
							<input <?php echo fw_attr_to_html($choice) ?> />
							<label for="<?php echo esc_attr($choice['id']) ?>"><?php echo $choice['value'] ?></label>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<?php if ($options['info']): ?>
				<p><em><?php echo $options['info'] ?></em></p>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>