<?php

class Sm_feed_manager {
	
	public function preview_newsletter($feed){
		
		return $this->build_newsletter($feed);
	}

	public function manage_feed($feed, $sm, $action) {

		if (
				!empty($feed['sm_feed_campaign_name']) &&
				!empty($feed['sm_feed_campaign_subject']) &&
				!empty($feed['post_nr']) &&
				!empty($feed['list_id']) &&
				!empty($feed['sender_email'])
		) {

			$args = array('numberposts' => $feed['post_nr']);

			$body = $this->build_newsletter($feed);

			if (!$body)
				return array('smSendCampaignErrorNoPost', 'campaignsend_err_nopost', __("No post found.Campaign not created!", SM_LANGUAGE_DOMAIN), "error");
				
			$res = $sm->create_campaign(array(
				"name" => $feed['sm_feed_campaign_name'],
				"subject" => $feed['sm_feed_campaign_subject'],
				"contactlist_id" => $feed['list_id'],
				"sender_email" => $feed['sender_email'],
				"body_html" => $body
			));

			if (isset($res['status']) && ($res['status'] == "created")) {
				
				if ($action == 'sm_feed_save_draft') {

					$link = "<a target='_blank' href='" . SM_SITE_APP_URL . "/#/campaigns/" . $res['id'] . "/source_editor' >" . __("here", SM_LANGUAGE_DOMAIN) . "</a>";
					return array('smSendCampaignSaveDraft', 'campaignsend_saved_draft', sprintf(__("Campaign saved as draft in your Sendmachine account. Click %s to access it.", SM_LANGUAGE_DOMAIN), $link), "updated");
				} 
				elseif ($action == 'sm_feed_send_campaign') {

					if (!$tested_cmp = $sm->test_campaign($res['id']))
						return array('smSendCampaignErrorGeneral', 'campaignsend_err_general', __("Something went wrong.Campaign could not be tested.", SM_LANGUAGE_DOMAIN), "error");

					if ($tested_cmp != "ok") {

						$reasons = "<ul>";

						if (!is_array($tested_cmp))
							$reasons .= "<li>general_error</li>";
						else {

							foreach ($tested_cmp as $err_rsn) {

								$reasons .= "<li>" . $err_rsn . "</li>";
							}
						}

						$reasons .= "</ul>";

						return array('smSendCampaignErrorTestFail', 'campaignsend_err_testfail', sprintf(__("Campaign created, but not started. Reported reasons: %s", SM_LANGUAGE_DOMAIN), $reasons), "error");
					}

					if ($sm->start_campaign($res['id'])) {

						$track_url = "<a target='_blank' href='" . SM_SITE_APP_URL . "//#/stats/" . $res['id'] . "' >" . __("here", SM_LANGUAGE_DOMAIN) . "</a>";
						return array('smSendCampaignStartedSuccess', 'campaignsend_started_success', sprintf(__("Campaign started successfully. You can track it by clicking %s.", SM_LANGUAGE_DOMAIN), $track_url), "updated");
					} 
					
					return array('smSendCampaignLaunchErrpr', 'campaignsend_launch_error', __("For some reason campaign was not started.", SM_LANGUAGE_DOMAIN), "error");
				}
			}
			
			return array('smSendCampaignErrorNotCreated', 'campaignsend_err_notcreated', isset($res['status']) ? $res['status'] : $res, "error");
		} 

		return array('smSendCampaignError', 'campaignsend_error', __("All fields are required.", SM_LANGUAGE_DOMAIN), "error");
	}
	
	private function build_newsletter($feed_data = NULL) {

		$args = array('numberposts' => $feed_data['post_nr']);

		$rp = wp_get_recent_posts($args);

		if (!$rp) return false;

		$body = "<html><head></head><body>";
		$body .= "<table style='padding:10px;width:100%;max-width:".$feed_data['template_width']."' width='600' bgcolor='".$feed_data['template_bgcolor']."' cellpadding='0' cellspacing='0' ><tr><td>";
		$body .= "<div id='sm_nl_header' >" . $this->parse_nl_parts($feed_data['header_template']) . "</div>";
		$body .= "<div id='sm_nl_body'>";

		foreach ($rp as $post) {

			$body .= "<div class='sm_nl_postcontent' >" . $this->parse_nl_parts($feed_data['body_template'], $post) . "</div>";
		}

		$body .= "</div>";
		$body .= "<div id='sm_nl_footer' >" . $this->parse_nl_parts($feed_data['footer_template']) . "</div>";
		$body .= "</td></tr></table>";
		$body .= "</body></html>";

		return $body;
	}

	private function parse_nl_parts($tpl, $args = NULL) {

		$keywords = $this->keywords('values', $args);
		$delimiter = Sm_wp::instance()->config["feed_delimiter"];
		$content = stripslashes(html_entity_decode($tpl));

		foreach ($keywords as $k => $v) {

			$content = preg_replace("/(".  preg_quote(sprintf($delimiter, $k)).")/", $v, $content);
		}
		return $content;
	}
	
	public function keywords($action, $args = array()) {

		$keywords = array(
			"SITENAME" => array(
				"value" => get_bloginfo('name'),
				"description" => __("Your blog's title", SM_LANGUAGE_DOMAIN)
			),
			"SITEDESCRIPTION" => array(
				"value" => get_option('blogdescription'),
				"description" => __("Blog's description", SM_LANGUAGE_DOMAIN)
			),
			"ADMINEMAIL" => array(
				"value" => get_option('admin_email'),
				"description" => __("Administrator's email address", SM_LANGUAGE_DOMAIN)
			),
			"SITEURL" => array(
				"value" => get_option('siteurl'),
				"description" => __("Blog URL.", SM_LANGUAGE_DOMAIN)
			),
			"POSTTITLE" => array(
				"value" => isset($args['post_title']) ? $args['post_title'] : "",
				"description" => __("Display post title.Works only in template body", SM_LANGUAGE_DOMAIN)
			),
			"POSTURL" => array(
				"value" => isset($args['guid']) ? $args['guid'] : "",
				"description" => __("Post url. Works only in template body", SM_LANGUAGE_DOMAIN)
			),
			"POSTCONTENTSUMMARY" => array(
				"value" => isset($args['post_content']) ? substr($args['post_content'], 0, 300) . " [...]" : "",
				"description" => __("Post's content summary.Display first 300 characters of content. Works only in template body", SM_LANGUAGE_DOMAIN)
			),
			"POSTCONTENTFULL" => array(
				"value" => isset($args['post_content']) ? $args['post_content'] : "",
				"description" => __("Post's content (full content). Works only in template body", SM_LANGUAGE_DOMAIN)
			),
			"POSTAUTHOR" => array(
				"value" => isset($args['post_author']) ? get_user_by('id', $args['post_author'])->data->user_nicename : "",
				"description" => __("Who wrote post. Works only in template body", SM_LANGUAGE_DOMAIN)
			),
			"POSTDATE" => array(
				"value" => isset($args['post_date']) ? $args['post_date'] : "",
				"description" => __("Post publish date. Works only in template body", SM_LANGUAGE_DOMAIN)
			)
		);

		$ret_arr = array();
		
		foreach ($keywords as $keyword => $data) {

			if ($action == "values") $ret_arr[$keyword] = $data['value'];
			elseif ($action == "description") $ret_arr[$keyword] = $data['description'];
		}
		
		return $ret_arr;
	}

}
