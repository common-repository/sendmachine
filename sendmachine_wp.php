<?php

/*
  Plugin Name: Sendmachine for WordPress
  Plugin URI: https://www.sendmachine.com
  Description: The official Sendmachine plugin featuring subscribe forms, users sync, news feed, email sending and transactional campaigns.
  Version: 1.0.19
  Author: Sendmachine team
  Author URI: http://developers.sendmachine.com/
  Text Domain: wp-sendmachine
  Domain Path: /languages/
 */

define('SM_PLUGIN_DIR', dirname(__FILE__) . '/');
define('SM_PLUGIN_FILE', __FILE__);
define('SM_LANGUAGE_DOMAIN', 'wp-sendmachine');
define('SM_OPTIONS_APP_NAME', 'sendmachine_application');
define('SM_USER_META_NAME', 'sm_subscribed');
define('SM_CAMPAIGN_HEADER', 'X-Sendmachine-Campaign');
define('SM_SITE_APP_URL', 'https://www.sendmachine.com/admin');
if (!defined("SM_WP_DEV_MODE")) {
	define('SM_WP_DEV_MODE', false);
}

require_once SM_PLUGIN_DIR . 'includes/utils.php';
require_once SM_PLUGIN_DIR . 'sendmachine_widget.php';
require_once SM_PLUGIN_DIR . 'sendmachine_wp_admin.php';
require_once SM_PLUGIN_DIR . 'includes/sendmachine_api.php';
require_once SM_PLUGIN_DIR . 'includes/sendmachine_subscribe_manager.php';
require_once SM_PLUGIN_DIR . 'includes/sendmachine_email_manager.php';
require_once SM_PLUGIN_DIR . 'includes/sendmachine_feed_manager.php';

Sm_wp::init();

class Sm_wp {

	private static $instance;

	function __construct() {

		require_once SM_PLUGIN_DIR . 'includes/sendmachine_defaults.php';
		$this->sm_defaults = Sm_defaults::defaults();
		$this->config = Sm_defaults::config();

		$this->app = get_option(SM_OPTIONS_APP_NAME);

		$this->email_manager = new Sm_email_manager();
		$this->list_manager = new Sm_subscribe_manager($this->app);
		$this->feed_manager = new Sm_feed_manager();
		$this->sm = isset($this->app['credentials']) ? new Sm_api($this->app['credentials'], SM_WP_DEV_MODE) : NULL;

		$this->api_connected = !empty($this->app['api_connected']) ? true : false;

		add_action('init', array($this, 'load_translations'));

		if ($this->api_connected) {

			add_action('widgets_init', array($this, 'load_widgets'));
		}
	}

	public static function init() {

		if (!self::$instance) {

			if (is_admin()) self::$instance = new Sm_wp_admin();
			else self::$instance = new sm_wp();
		}
	}

	public static function instance() {

		return self::$instance;
	}

	public function load_widgets() {

		register_widget('Sm_subscribe_widget');
	}

	public function load_translations() {

		load_plugin_textdomain(SM_LANGUAGE_DOMAIN, false, basename(SM_PLUGIN_DIR) . '/languages');
	}
}
