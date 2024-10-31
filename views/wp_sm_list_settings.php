<div class="wrap">
	<div class='sm_wrapper'>
		<div class="sm_header" >
			<h2>
				<img height="22" src="<?php echo plugins_url('static/images/wp_sendmachine_logo_big.png', SM_PLUGIN_FILE); ?>">
				<?php echo _e('List Settings', SM_LANGUAGE_DOMAIN); ?>
			</h2>
			<p> <?php echo _e('To use the Sendmachine sign-up form, setup a list below and then head out to the widgets page and add the widget to your site.Alternatively you can use this shortcode <code>[sm_subscribe_form]</code> to display the form inside a post or page.',SM_LANGUAGE_DOMAIN); ?> </p>
		</div>
		<div class="sm_content">
		<table class="list_sm_table" >
			<tr>
				<td class='sm_row_title'>
					<span><?php _e("Refresh cached contact lists", SM_LANGUAGE_DOMAIN); ?></span>
				</td>
				<td class='sm_row_content'>
					<form method="post">
						<input type="hidden" name="sm_admin_wp_request" value="1" />
						<input type="hidden" name="sm_action" value="refresh_list_data" />
						<input class="button" type="submit" value="<?php _e('Refresh', SM_LANGUAGE_DOMAIN); ?>"/>
					</form>
				</td>
				<td class='sm_row_description'>
					<span><?php _e("Contact lists do not update each time you visit this page. Click 'refresh' to update them when needed.", SM_LANGUAGE_DOMAIN); ?></span>
				</td>
			</tr>
			<tr>
				<td class='sm_row_title'>
					<span><?php _e("List where people will subscribe", SM_LANGUAGE_DOMAIN); ?></span>
				</td>
				<td colspan="2" class='sm_row_content'>
					<?php
					if ($this->api_connected) {

						if (isset($this->app['list']['data'])) {
							?>
							<form method='post'>
							<input type="hidden" name="sm_admin_wp_request" value="1" />
							<input type="hidden" name="sm_action" value="update_list_fields" />
							<select class='sm_list_select' name='update[list][id]' >
							<?php
							if(empty($this->app['list']['id'])) echo "<option></option>";
							
							if(count($this->app['list']['data'])){
								
								foreach ($this->app['list']['data'] as $list) {

									$selected = ($this->app['list']['id'] == $list['list_id']) ? "selected" : "";
									echo "<option value='" . $list['list_id'] . "' $selected>" . $list['name'] . "</option>";
								}
							}
							?>
							</select>
							<input class='button sm_list_button' value='<?php _e("Save", SM_LANGUAGE_DOMAIN); ?>' type="submit" />
							</form>
							<?php
						}
						else
							echo "<a href='" . admin_url('admin.php?page=sendmachine_settings') . "' class='sm_list_api_not_connected' >" . __("AN ERROR OCCURRED", SM_LANGUAGE_DOMAIN) . "</a>";
					} else
						echo "<a href='" . admin_url('admin.php?page=sendmachine_settings') . "' class='sm_list_api_not_connected' >" . __("API NOT CONNECTED", SM_LANGUAGE_DOMAIN) . "</a>";
					?>
				</td>
			</tr>
		</table>
		<form method="post">
			<input type="hidden" name="sm_admin_wp_request" value="1" />
			<table class="list_sm_table">
				<tr><td colspan='3' ><h3><?php _e('Subscribe customization', SM_LANGUAGE_DOMAIN); ?></h3></td></tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Hide form after a successful sign-up?", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content'>
						<label>
							<input type='radio' name="update[list][hide_subscribed]" <?php checked($this->app['list']['hide_subscribed'], 1); ?> value='1'/>
							<?php _e("Yes", SM_LANGUAGE_DOMAIN); ?>
						</label>
						<label>
							<input type='radio' name="update[list][hide_subscribed]" <?php checked($this->app['list']['hide_subscribed'], 0); ?> value='0'/>
							<?php _e("No", SM_LANGUAGE_DOMAIN); ?>
						</label>
					</td>
					<td class='sm_row_description'>
						<span><?php _e("Select 'yes' to hide the form fields after a successful sign-up.", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Use captcha", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<?php if($this->reallySimpleCaptcha['is_ok']) : ?>
						<td class='sm_row_content'>
							<label>
								<input type='radio' name="update[list][use_captcha]" <?php checked($this->app['list']['use_captcha'], 1); ?> value='1'/>
								<?php _e("Yes", SM_LANGUAGE_DOMAIN); ?>
							</label>
							<label>
								<input type='radio' name="update[list][use_captcha]" <?php checked($this->app['list']['use_captcha'], 0); ?> value='0'/>
								<?php _e("No", SM_LANGUAGE_DOMAIN); ?>
							</label>
						</td>
						<td class='sm_row_description'>
							<span><?php _e("Select 'yes' to include a captcha image in the subscribe form to protect your list from bots.", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
					<?php else : ?>
						<td colspan="2" class="sm_row_content">
							<a href="https://wordpress.org/plugins/really-simple-captcha/" target="_blank">Really Simple CAPTCHA</a> 
							<span><?php _e("plugin is required in order to use captcha with the subscribe form.", SM_LANGUAGE_DOMAIN); ?></span>
							<?php if($this->reallySimpleCaptcha['needs_install'] && current_user_can('install_plugins')): ?>
								<a href="<?php echo $this->reallySimpleCaptcha['needs_install']; ?>" target="_blank"><?php _e("Install plugin here", SM_LANGUAGE_DOMAIN); ?></a>.
							<?php endif;?>
							<?php if($this->reallySimpleCaptcha['needs_activation'] && current_user_can('activate_plugins')): ?>
								<a href="<?php echo $this->reallySimpleCaptcha['needs_activation']; ?>" target="_blank"><?php _e("Activate plugin here", SM_LANGUAGE_DOMAIN); ?></a>.
							<?php endif;?>
						</td>
					<?php endif; ?>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Redirect to URL after successful sign-up", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content' colspan="2">
						<input type="text" name="update[list][redirect_subscribed]" class="widefat" value="<?php echo $this->app['list']['redirect_subscribed']; ?>" />
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Successful subscribe message", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content' colspan="2">
						<input type="text" name="update[list][message_success_subscribe]" class="widefat" value="<?php echo $this->app['list']['message_success_subscribe']; ?>" />
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Subscriber exists message", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content' colspan="2">
						<input type="text" name="update[list][message_subscriber_exists]" class="widefat" value="<?php echo $this->app['list']['message_subscriber_exists']; ?>" />
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Not subscribed message", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content' colspan="2">
						<input type="text" name="update[list][message_not_subscribed]" class="widefat" value="<?php echo $this->app['list']['message_not_subscribed']; ?>" />
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Invalid email address", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content' colspan="2">
						<input type="text" name="update[list][message_invalid_email]" class="widefat" value="<?php echo $this->app['list']['message_invalid_email']; ?>" />
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Required fields error", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content' colspan="2">
						<input type="text" name="update[list][message_required_field]" class="widefat" value="<?php echo $this->app['list']['message_required_field']; ?>" />
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Submit button label", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content' colspan="2">
						<input type="text" name="update[list][label_submit_button]" class="widefat" value="<?php echo $this->app['list']['label_submit_button']; ?>" />
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Invalid captcha message", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content' colspan="2">
						<input type="text" name="update[list][message_invalid_captcha]" class="widefat" value="<?php echo $this->app['list']['message_invalid_captcha']; ?>" />
					</td>
				</tr>
				<?php
					if(!empty($this->app['list']['fields'])){
					?>
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("Form fields", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td colspan="2">
							<table class="sm_list_fields_settings" cellspacing="0" cellpadding="0">
								<thead>
									<tr>
										<td><?php _e("Label", SM_LANGUAGE_DOMAIN) ?></td>
										<td><?php _e("Name", SM_LANGUAGE_DOMAIN) ?></td>
										<td><?php _e("Visible", SM_LANGUAGE_DOMAIN) ?></td>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach($this->app['list']['fields'] as $k => $f){
									
										if (!isset($f['form_name']))
											$f['form_name'] = ucfirst(strtolower($f['name']));
										echo "<tr>";
										echo "<td>".$f['form_name']."</td>";
										echo "<td>".$f['name']."</td>";
									
										$checked = $f['visible'] ? "checked" : "";
										$disabled = "";
										$extra_text = "";
									
										if($f['required']){
										
											$disabled = "disabled";
											$checked = "checked";
											$extra_text = "*";
										}
										echo "<td>";
										if(!$disabled) echo "<input type='hidden' name='update[list][fields][$k][visible]' value='0'/>";
										echo "<input type='checkbox' name='update[list][fields][$k][visible]' value='1' $disabled $checked/>$extra_text</td>";
									
										echo "</tr>";
									}
									?>
									<tr>
										<td colspan="3" ><?php _e("* field is required, can't be hidden", SM_LANGUAGE_DOMAIN); ?></td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				<?php
				}
				?>
			</table>
			<p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php _e("Save Changes", SM_LANGUAGE_DOMAIN) ?>"></p>
		</form>
		<form method="post">
			<input type="hidden" name="sm_admin_wp_request" value="1" />
			<table class="list_sm_table">
				<tr><td colspan='3' ><h3><?php _e('Checkbox settings', SM_LANGUAGE_DOMAIN); ?></h3></td></tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Add the checkbox to these forms", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content'>
						<label>
							<input name="update[list][checkbox_comment]" <?php echo !empty($this->app['list']['simple_subscribe']) ? "" : "disabled";checked($this->app['list']['checkbox_comment']); ?> type="checkbox" value="1"> <?php _e("Comment form", SM_LANGUAGE_DOMAIN); ?>
						</label><br>
						<label>
							<input name="update[list][checkbox_register]" <?php echo !empty($this->app['list']['simple_subscribe']) ? "" : "disabled";checked($this->app['list']['checkbox_register']); ?> type="checkbox" value="1"> <?php _e("Register form", SM_LANGUAGE_DOMAIN); ?>
						</label>
					</td>
					<td class='sm_row_description'>
						<span><?php _e("Selecting a form will automatically add the sign-up checkbox to it.", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Checkbox label text", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content' colspan="2">
						<input type="text" name="update[list][checkbox_label]" class="widefat" value="<?php echo esc_attr($this->app['list']['checkbox_label']); ?>" />
					</td>
				</tr>
				<tr>
					<td class='sm_row_title'>
						<span><?php _e("Pre-check the checkbox?", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
					<td class='sm_row_content'>
						<label>
							<input type='radio' name="update[list][checkbox_checked]" <?php checked($this->app['list']['checkbox_checked'], 1); ?> value='1'/><?php _e("Yes", SM_LANGUAGE_DOMAIN); ?>
						</label>
						<label>
							<input type='radio' name="update[list][checkbox_checked]" <?php checked($this->app['list']['checkbox_checked'], 0); ?> value='0'/><?php _e("No", SM_LANGUAGE_DOMAIN); ?>
						</label>
					</td>
					<td class='sm_row_description'>
						<span><?php _e("Be careful with this option.If you check 'yes' people might get subscribed by accident.", SM_LANGUAGE_DOMAIN); ?></span>
					</td>
				</tr>
			</table>
			<p class="submit"><input type="submit" id="submit" class="button button-primary" value="<?php _e("Save Changes", SM_LANGUAGE_DOMAIN) ?>"></p>
		</form>
		<table class="list_sm_table" >
			<tr><td colspan="3"><hr></td></tr>
			<tr>
				<td class='sm_row_title'>
					<span><?php _e("Sync WP users", SM_LANGUAGE_DOMAIN); ?></span>
				</td>
				<td class='sm_row_content'>
					<form method="post">
						<input type="hidden" name="sm_subscribe_wp_request" value="1" />
						<input type="hidden" name="sm_action" value="sync_users" />
						<input class="button" type="submit" <?php echo !empty($this->app['list']['simple_subscribe']) ? "" : "disabled"; ?> name="sendmachine_sync_wp_users" value="<?php _e('Sync users', SM_LANGUAGE_DOMAIN); ?>"/>
					</form>
				</td>
				<td class='sm_row_description'>
					<span><?php _e("Subscribe all your existing users to selected contact list.", SM_LANGUAGE_DOMAIN); ?></span>
				</td>
			</tr>
		</table>
	</div>
	</div>
</div>