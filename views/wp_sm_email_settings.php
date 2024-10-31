<div class="wrap">
	<div class='sm_wrapper'>
		<div class="sm_header" >
			<h2>
				<img height="22" src="<?php echo plugins_url('static/images/wp_sendmachine_logo_big.png', SM_PLUGIN_FILE); ?>">
				<?php _e('Email Settings', SM_LANGUAGE_DOMAIN); ?>
			</h2>
			<p><?php _e('If you want your emails to be sent through our service (sendmachine), just complete the configuration settings bellow and let us do the rest.', SM_LANGUAGE_DOMAIN); ?></p>
		</div>
		<div class="sm_content">
			<form method="POST">
				<input type="hidden" name="sm_admin_wp_request" value="1" />
				<input type="hidden" name="sm_action" value="update_email_settings" />
				<table class="list_sm_table" >
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("Enable email sending", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content'>
							<label>
								<input type='radio' name="update[email][enable_service]" <?php checked($this->app['email']['enable_service'], 1); ?> value='1'/>
								<?php _e("Yes", SM_LANGUAGE_DOMAIN); ?>
							</label>
							<label>
								<input type='radio' name="update[email][enable_service]" <?php checked($this->app['email']['enable_service'], 0); ?> value='0'/>
								<?php _e("No", SM_LANGUAGE_DOMAIN); ?>
							</label>
						</td>
						<td class="sm_row_description">
							<span><?php _e("Use our services to deliver your emails.", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("From Email", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content_merged' colspan='2'>
							<?php
							if ($this->api_connected) {
								
								$class = "sm_full";
								
								if(empty($this->app['email']['emailconfirmed'])){
									echo "<div class='error' ><p>".__("Email address is not confirmed. You will be able to send emails only after you confirm it!", SM_LANGUAGE_DOMAIN)."</p></div>";
									
									if(empty($this->app['email']['enable_service']))
										echo "<div class='error' ><p>".__("You must enable <b>email sending</b> in order to send yourself a confirmation email!", SM_LANGUAGE_DOMAIN)."</p></div>";
								}
								
								echo "<input class='sm_full' type='text' name='update[email][from_email]' value='".$this->app['email']['from_email']."' />";
							}
							else
								echo "<a href='" . admin_url('admin.php?page=sendmachine_settings') . "' class='sm_list_api_not_connected' >" . __("Api not connected", SM_LANGUAGE_DOMAIN) . "</a>";
							?>
						</td>
					</tr>
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("From Name", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content_merged' colspan='2'>
							<input type="text" class='sm_full' name='update[email][from_name]' value='<?php echo isset($this->app['email']['from_name']) ? $this->app['email']['from_name'] : "" ?>' />
						</td>
					</tr>
					<tr>
						<td class='sm_row_title hold_top'>
							<span><?php _e("SMTP Encryption", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content' colspan='2' >
							<label class="breathe_through_elements" >
								<input type='radio' name="update[email][encryption]" <?php checked($this->app['email']['encryption'], 'no_encryption'); ?> value='no_encryption'/>
								<?php _e("No encryption", SM_LANGUAGE_DOMAIN); ?>
							</label><br>
							<label class="breathe_through_elements">
								<input type='radio' name="update[email][encryption]" <?php checked($this->app['email']['encryption'], 'ssl'); ?> value='ssl'/>
								<?php _e("SSL encryption", SM_LANGUAGE_DOMAIN); ?>
							</label><br>
							<label class="breathe_through_elements">
								<input type='radio' name="update[email][encryption]" <?php checked($this->app['email']['encryption'], 'tls'); ?> value='tls'/>
								<?php _e("TLS encryption", SM_LANGUAGE_DOMAIN); ?>
							</label>
						</td>
					</tr>
					<tr>
						<td class='sm_row_title hold_top'>
							<span><?php _e("Track emails", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content_merged' colspan="2">
							<div>
								<label>
									<input name="update[email][register_post]" type="hidden" value="0"/> 
									<input name="update[email][register_post]" type="checkbox" value="1" <?php checked($this->app['email']['register_post'],1) ?> /> 
									<?php _e("Register", SM_LANGUAGE_DOMAIN); ?>
								</label> 
								<input name="update[email][register_post_label]" class="track_email_input" type="text" value="<?php echo $this->app['email']['register_post_label']; ?>" />
							</div>
							<div style="clear:right;">
								<label>
									<input name="update[email][comment_post]" type="hidden" value="0"/>
									<input name="update[email][comment_post]" type="checkbox" value="1" <?php checked($this->app['email']['comment_post'],1) ?> /> 
									<?php _e("Comment", SM_LANGUAGE_DOMAIN); ?>
								</label>
								<input name="update[email][comment_post_label]" class="track_email_input" type="text" value="<?php echo $this->app['email']['comment_post_label']; ?>" />
							</div>
							<div style="clear:right;margin-top:25px;" class="general_sm_description">
								<span><?php _e("Track sent emails using transactional campaigns.Just check the action from which you want your emails to be tracked and then set a name for your transactional campaign.", SM_LANGUAGE_DOMAIN); ?></span>
							</div>
						</td>
					</tr>
					<tr>
						<td style="padding-top:20px;" colspan="3" >
							<input type="submit" class="button button-primary" value="<?php _e("Save Changes", SM_LANGUAGE_DOMAIN) ?>">
							<?php if(empty($this->app['email']['emailconfirmed']) && !empty($this->app['email']['emailpending']) && $this->app['email']['emailpending'] == $this->app['email']['from_email']): ?>
							<input style="margin-left:15px;" type="submit" name="sm_already_confirmed_wp" class="button" value="<?php _e("I already confirmed my address", SM_LANGUAGE_DOMAIN) ?>">
							<?php endif; ?>
						</td>
					</tr>
				</table>
			</form>
			<table class="list_sm_table" >
				<tr>
					<td colspan='3' >
						<h3><?php _e('SMTP Configuration:', SM_LANGUAGE_DOMAIN); ?></h3>
						<p class='sm_description_general' ><?php _e("Preview configuration settings.", SM_LANGUAGE_DOMAIN); ?></p>
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("SMTP Host:", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content'>
						<input type='text' disabled class='sm_full' value='<?php echo isset($this->app['email']['host']) ? $this->app['email']['host'] : "" ?>'/>
					</td>
					<td class='sm_row_description'>
						<span><?php _e("This is the SMTP host.You can't change it.", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("SMTP Port:", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content'>
						<input type='text' disabled value='<?php echo isset($this->app['email']['port']) ? $this->app['email']['port'] : "" ?>'/>
					</td>
					<td class='sm_row_description'>
						<span><?php _e("SMTP Port, dependent of encryption type.", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Encryption type:", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content'>
						<input type='text' disabled value='<?php echo isset($this->app['email']['encryption']) ? $this->app['email']['encryption'] : "" ?>'/>
					</td>
					<td class='sm_row_description'>
						<span><?php _e("Encryption type.", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
				</tr>
			</table>
			<form method='POST' >
				<input type="hidden" name="sm_email_wp_request" value="1" />
				<input type="hidden" name="sm_action" value="send_test_email" />
				<table class="list_sm_table" >
					<tr>
						<td colspan='3' >
							<h3><?php _e('Send test email', SM_LANGUAGE_DOMAIN); ?></h3>
							<p class='sm_description_general' ><?php _e("Enter a email address to test if settings were applied smoothly.", SM_LANGUAGE_DOMAIN); ?></p>
						</td>
					</tr>
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("Email To:", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content_merged' colspan='2'>
							<input type='text' value='' class='sm_full' name='email' />
						</td>
					</tr>
					<tr>
						<td colspan="3" ><input type="submit" id="submit" class="button button-primary" value="<?php _e("Send", SM_LANGUAGE_DOMAIN) ?>"></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>