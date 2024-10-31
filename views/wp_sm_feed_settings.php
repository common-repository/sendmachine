<?php
$sm_feed_editor_settings = array(
	'editor_height' => 150,
	'wpautop' => false
);
?>
<div class="wrap">
	<div class='sm_wrapper'>
		<div class="sm_header" >
			<h2>
				<img height="22" src="<?php echo plugins_url('static/images/wp_sendmachine_logo_big.png', SM_PLUGIN_FILE); ?>">
				<?php _e('Sendmachine Feed', SM_LANGUAGE_DOMAIN); ?>
			</h2>
			<p><?php _e('Send your subscribers a campaign with your latest blog posts.', SM_LANGUAGE_DOMAIN); ?></p>
		</div>
		<div class="sm_content">
			<form method="POST">
				<input type="hidden" name="sm_admin_wp_request" value="1" />
				<input type="hidden" name="sm_action" value="manage_feed" />
				<table class="list_sm_table" >
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("Campaign Name", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content_merged' colspan='2'>
							<input id="sm_wp_feed_campaign_name" type="text" name='sm_feed_campaign_name' class="widefat"/>
						</td>
					</tr>
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("Campaign Subject", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content_merged' colspan='2'>
							<input id="sm_wp_feed_campaign_subject" type="text" name='sm_feed_campaign_subject' class="widefat"/>
						</td>
					</tr>
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("Contact list", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content_merged' colspan='2'>
							<select id="sm_wp_feed_contactlist_name" class='sm_full' name="update[feed][list_id]" >
								<?php
								if ($this->api_connected && isset($this->app['list']['data']) && count($this->app['list']['data'])) {

									$cnt = 0;
									$opt = "";
									foreach ($this->app['list']['data'] as $list) {
										if(!$list['subscribed']) continue;
										$cnt++;
										$selected = ($this->app['feed']['list_id'] == $list['list_id']) ? "selected" : "";
										$opt .= "<option value='" . $list['list_id'] . "' $selected>" . $list['name'] . "</option>";
									}
									
									if (!$cnt || !$this->app['feed']['list_id']) echo "<option></option>";
									echo $opt;
								}
								else echo "<option></option>";
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("Sender address", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content_merged' colspan='2'>
							<select id="sm_wp_feed_sender_address" class='sm_full' name="update[feed][sender_email]" >
								<?php
								if (empty($this->app['feed']['sender_email']))
									echo "<option></option>";

								if ($this->api_connected && isset($this->app['email']['senderlist']) && count($this->app['email']['senderlist'])) {

									foreach ($this->app['email']['senderlist'] as $sender) {

										$selected = ($this->app['feed']['sender_email'] == $sender) ? "selected" : "";
										echo "<option value='" . $sender . "' $selected>" . $sender . "</option>";
									}
								}
								?>
							</select>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input style='margin-right:10px;' onclick="return confirm_campaign();" type="submit" class="button button-primary" name='sm_feed_send_campaign' value="<?php _e("Send Campaign", SM_LANGUAGE_DOMAIN) ?>"/>
					<input type="submit" class="button" name='sm_feed_save_draft' value="<?php _e("Save Draft To Sendmachine", SM_LANGUAGE_DOMAIN) ?>"/>
				</p>
			</form>
			<form method="POST">
				<input type="hidden" name="sm_admin_wp_request" value="1" />
				<table class="list_sm_table" >
					<tr><td colspan='3' ><h3><?php _e('Template customization', SM_LANGUAGE_DOMAIN); ?></h3></td></tr>
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("Number of posts", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content'>
							<select class='sm_full' name="update[feed][post_nr]">
								<?php
								$nr = array('5', '10', '15', '20');

								foreach ($nr as $k => $v) {

									$selected = ($this->app['feed']['post_nr'] == $v) ? "selected" : "";
									echo "<option value=$v $selected>$v</option>";
								}
								?>
							</select>
						</td>
						<td class='sm_row_description' >
							<span><?php _e("Number of posts (descending order) to build campaign's newsletter.", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("Template width", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content'>
							<input type="text" name="update[feed][template_width]" value='<?php echo isset($this->app['feed']['template_width']) ? $this->app['feed']['template_width'] : '' ?>'/>
						</td>
						<td class='sm_row_description' >
							<span><?php _e("Define template's width.Defaults to 600 px.", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td class='sm_row_title'>
							<span><?php _e("Template background color", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
						<td class='sm_row_content'>
							<input type="text" name="update[feed][template_bgcolor]" value='<?php echo isset($this->app['feed']['template_bgcolor']) ? $this->app['feed']['template_bgcolor'] : '' ?>'/>
						</td>
						<td class='sm_row_description' >
							<span><?php _e("Define template's background color.Defaults to '#fff' (white).", SM_LANGUAGE_DOMAIN); ?></span>
						</td>
					</tr>
					<tr>
						<td colspan='3' class='sm_row_content_master' >
							<p class='sm_feed_header'><?php _e("Template header", SM_LANGUAGE_DOMAIN); ?></p>
							<p class='sm_feed_description'><?php _e("Customize template's header. A logo is recommended, but really, there are no limits.", SM_LANGUAGE_DOMAIN); ?></p>
							<?php
							$content = stripslashes(html_entity_decode($this->app['feed']['header_template'], ENT_QUOTES));
							$editor_id = 'sm_feed_header';
							$sm_feed_editor_settings['textarea_name'] = "update[feed][header_template]";
							
							wp_editor($content, $editor_id, $sm_feed_editor_settings);
							?>
						</td>
					</tr>
					<tr>
						<td colspan='3' class='sm_row_content_master'>
							<p class='sm_feed_header'><?php _e("Template body", SM_LANGUAGE_DOMAIN); ?></p>
							<p class='sm_feed_description'><?php printf(__("Customize template's body. Include at least %s and %s keywords in your body to build the actual content. (styles WILL be inherited).", SM_LANGUAGE_DOMAIN),sprintf($this->config["feed_delimiter"], "POSTTITLE"), sprintf($this->config["feed_delimiter"], "POSTCONTENTSUMMARY")); ?></p>
							<?php
							$content = stripslashes(html_entity_decode($this->app['feed']['body_template']));
							$editor_id = 'sm_feed_body';
							$sm_feed_editor_settings['textarea_name'] = "update[feed][body_template]";

							wp_editor($content, $editor_id, $sm_feed_editor_settings);
							?>
						</td>
					</tr>
					<tr>
						<td colspan='3' class='sm_row_content_master'>
							<p class='sm_feed_header'><?php _e("Template footer", SM_LANGUAGE_DOMAIN); ?></p>
							<p class='sm_feed_description'><?php echo esc_attr(__("Customize template's footer. An unsubscribe link is mandatory e.g. <a href='[[UNSUB_LINK]]'>unsubscribe</a>", SM_LANGUAGE_DOMAIN)); ?></p>
							<?php
							$content = stripslashes(html_entity_decode($this->app['feed']['footer_template']));
							$editor_id = 'sm_feed_footer';
							$sm_feed_editor_settings['textarea_name'] = "update[feed][footer_template]";

							wp_editor($content, $editor_id, $sm_feed_editor_settings);
							?>
						</td>
					</tr>
					<tr>
						<td colspan="3" class="sm_row_content_master">
							<div>
								<h4 class="sm_keywords_title" ><?php _e("Keyword dictionary: ", SM_LANGUAGE_DOMAIN); ?></h4>
								<ul class="sm_keywords_ul" >
									<?php
									$keywords = $this->feed_manager->keywords('description');

									foreach ($keywords as $keyword => $description) {
										echo "<li><code>" . sprintf($this->config["feed_delimiter"], $keyword) . "</code> - $description</li>";
									}
									?>
								</ul>
								<p>
									<?php
									$blog_url = "<a target='_blank' href='http://blog.sendmachine.ro/index.php/macro-email-marketing/' >". __("here", SM_LANGUAGE_DOMAIN) ."</a>";
									printf(__("For a list of available Sendmachine macros click %s.", SM_LANGUAGE_DOMAIN), $blog_url);
									?>
								</p>
							</div>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input style='margin-right:10px;' type="submit" class="button button-primary" value="<?php _e("Save", SM_LANGUAGE_DOMAIN) ?>">
					<input type="submit" class="button" name="sm_feed_preview_nl" value="<?php _e("Preview", SM_LANGUAGE_DOMAIN) ?>">
				</p>
			</form>
		</div>
	</div>
</div>