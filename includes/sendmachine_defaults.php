<?php

class Sm_defaults {

	public static function config() {

		return array(
			"keys_to_encode" => array("header_template", "body_template", "footer_template"),
			"to_reset" => array('list|id', 'list|data', 'list|fields', 'email|senderlist', 'email|provider_settings', 'email|host', 'email|port', 'email|from_email', 'feed|sender_email', 'feed|list_id'),
			"feed_delimiter" => "||%s||"
		);
	}

	public static function defaults() {

		return array(
			"list" => array(
				"hide_subscribed" => 0,
				"use_captcha" => 0,
				"redirect_subscribed" => "",
				"message_success_subscribe" => __('You have been successfully subscribed! Thank you!', SM_LANGUAGE_DOMAIN),
				"message_subscriber_exists" => __('You are already subscribed.Thanks anyway.', SM_LANGUAGE_DOMAIN),
				"message_not_subscribed" => __('Something went wrong, you were not subscribed.', SM_LANGUAGE_DOMAIN),
				"message_invalid_email" => __('Please provide a valid email address.', SM_LANGUAGE_DOMAIN),
				"message_invalid_captcha" => __('Invalid captcha code. Please try again.', SM_LANGUAGE_DOMAIN),
				"label_submit_button" => __('Subscribe', SM_LANGUAGE_DOMAIN),
				"message_required_field" => __('Please provide all the required field data.', SM_LANGUAGE_DOMAIN),
				"checkbox_register" => 1,
				"checkbox_comment" => 0,
				"checkbox_label" => __('Sign me up for the newsletter!', SM_LANGUAGE_DOMAIN),
				"checkbox_checked" => 0,
				"simple_subscribe" => 1
			),
			"email" => array(
				"enable_service" => 1,
				"encryption" => "no_encryption",
				"register_post" => 1,
				"comment_post" => 0,
				"register_post_label" => "Register",
				"comment_post_label" => "Comment"
			),
			"feed" => array(
				"post_nr" => 10,
				"header_template" => "<h1>||SITENAME||</h1>",
				"body_template" => "<h3><a href=\"||POSTURL||\">||POSTTITLE||</a></h3><p>||POSTCONTENTSUMMARY||</p><p><em>Author: ||POSTAUTHOR|| Posted on ||POSTDATE||</em></p>",
				"footer_template" => "<p><em>If you don't want to receive this messages anymore, unsubscribe by clicking <a href=\"[[UNSUB_LINK]]\">here</a>!</em></p>",
				"template_width" => "600px",
				"template_bgcolor" => "#fff"
			)
		);
	}

}