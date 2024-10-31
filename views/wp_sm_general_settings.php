<div class="wrap">
	<div class='sm_wrapper'>
		<div class="sm_header" >
			<h2>
				<img height="22" src="<?php echo plugins_url('static/images/wp_sendmachine_logo_big.png', SM_PLUGIN_FILE); ?>">
				<?php _e('Sendmachine For WordPress', SM_LANGUAGE_DOMAIN); ?>
			</h2>
			<?php if(!empty($this->app['not_connect_reason'])) echo"<div class='error'> <p>".$this->app['not_connect_reason']."</p></div>";?>
		</div>

		<div class="sm_login" >
			<p><?php _e('To start using the Sendmachine plugin, we first need to connect your Sendmachine account. Click login below to connect.', SM_LANGUAGE_DOMAIN); ?></p>

			<p>
				<?php _e("Don't have a Sendmachine account?", SM_LANGUAGE_DOMAIN); ?>
				<a target="_blank" href="https://www.sendmachine.com/admin/#/register"> <?php _e('Create one for free!', SM_LANGUAGE_DOMAIN); ?></a>
			</p><br>

			<div class="sm_api_status_wrapper">
				<span class="sm_api_status_description" ><?php _e("API status", SM_LANGUAGE_DOMAIN) ?>:</span>
				<?php
				if ($this->api_connected) {
					$class = "connected";
					$text = __("CONNECTED", SM_LANGUAGE_DOMAIN);
				} else {
					$class = "offline";
					$text = __("NOT CONNECTED", SM_LANGUAGE_DOMAIN);
				}

				echo "<span class='sm_api_status $class'>";
				echo $text;
				echo "</span>";
				?>
			</div>

			<p>
				<b>
					<?php _e("Get your API credentials from", SM_LANGUAGE_DOMAIN) ?>
					<a target="_blank" href="https://www.sendmachine.com/admin/#/myaccount/smtp_settings"><?php _e("here", SM_LANGUAGE_DOMAIN) ?></a>
				</b>
			</p>

			<form class="sendmachine_settings" method="post">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php _e("API USERNAME", SM_LANGUAGE_DOMAIN) ?></th>
							<td>
								<input type="hidden" name="sm_admin_wp_request" value="1" />
								<input type="hidden" name="sm_action" value="init_sm_data" />
								<input id="sm_api_username" type="text" class="api_credentials" name="update[credentials][api_username]" value="<?php echo isset($this->app['credentials']['api_username']) ? trim($this->app['credentials']['api_username']) : '' ?>">
								<button class="button" id="button_sm_api_username" onclick="smToggleCredentialsVisibility('sm_api_username');return false;" >show</button>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e("API PASSWORD", SM_LANGUAGE_DOMAIN) ?></th>
							<td>
								<input id="sm_api_password" type="text" class="api_credentials" name="update[credentials][api_password]" value="<?php echo isset($this->app['credentials']['api_password']) ? trim($this->app['credentials']['api_password']) : '' ?>">
								<button class="button" id="button_sm_api_password" onclick="smToggleCredentialsVisibility('sm_api_password');return false;" >show</button>
							</td>
						</tr>
					</tbody>
				</table>
				<input type="submit" class="button button-primary" value="<?php _e("Save Changes", SM_LANGUAGE_DOMAIN) ?>" />
			</form>

		</div>

	</div>
</div>
<script>smInitCredentialsBlur();</script>