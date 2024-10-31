<div class="wrap">
	<div class='sm_wrapper'>
		<div class="sm_header" >
			<h2>
				<img height="22" src="<?php echo plugins_url('static/images/wp_sendmachine_logo_big.png', SM_PLUGIN_FILE); ?>">
				<?php _e('Sendmachine Feed: Template Preview', SM_LANGUAGE_DOMAIN); ?>
			</h2>
			<p><?php _e('This is a preview of the newsletter that will be sent to your subscribers.', SM_PLUGIN_FILE); ?></p>
		</div>
		<div class="sm_content">
			<p><a class="button" href="<?php echo admin_url('admin.php?page=sendmachine_feed'); ?>" ><?php _e('back', SM_LANGUAGE_DOMAIN); ?></a></p>
			<div class="sm_nl_preview" >
				<iframe onload="resize_iframe(this)" id="sm_wp_feed_preview_pretty_template" width='100%' height='500px' src="<?php echo admin_url('admin.php?page=sendmachine_feed&sm_admin_wp_request=1&sm_action=preview_raw_template'); ?>"></iframe>
			</div>
		</div>
	</div>
</div>