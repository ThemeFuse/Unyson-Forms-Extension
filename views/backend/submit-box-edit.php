<?php if (!defined('FW')) die('Forbidden');
/**
 * @var WP_Post $post
 * @var array $options
 */
?>
<div class="submitbox" id="submitpost">
	<div>
		<?php echo fw()->backend->render_options($options); ?>
	</div>
	<div id="major-publishing-actions">
		<div id="delete-action">
			<?php
			if (current_user_can("delete_post", $post->ID)) {
				if (!EMPTY_TRASH_DAYS)
					$delete_text = __('Delete Permanently', 'fw');
				else
					$delete_text = __('Move to Trash', 'fw');
				?>
				<a class="submitdelete deletion"
				   href="<?php echo get_delete_post_link($post->ID); ?>"><?php echo $delete_text; ?></a><?php
			} ?>
		</div>

		<div id="publishing-action">
			<span class="spinner"></span>
			<input name="original_publish" type="hidden" id="original_publish"
			       value="<?php esc_attr_e('Update', 'fw') ?>"/>
			<input name="save" type="submit" class="button button-primary button-large" id="publish"
			       accesskey="p" value="<?php esc_attr_e('Save', 'fw') ?>"/>
		</div>
		<div class="clear"></div>
	</div>
</div>