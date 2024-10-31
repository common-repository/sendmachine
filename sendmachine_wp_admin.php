<?php

class Sm_wp_admin extends Sm_wp {

	private $messages_queue = array();
	
	public function __construct() {

		parent::__construct();
		
		$this->load_admin_hooks();

		register_activation_hook(SM_PLUGIN_FILE, array($this, 'sendmachine_wp_install'));
		register_deactivation_hook(SM_PLUGIN_FILE, array($this, 'sendmachine_wp_uninstall'));
	}

	public function load_admin_hooks() {

		global $pagenow;

		add_action('admin_enqueue_scripts', array($this, 'styles_scripts'));
		
		if ($pagenow == "plugins.php") {

			add_filter('plugin_action_links_' . plugin_basename(SM_PLUGIN_FILE), array($this, 'enable_settings'));
		}

		add_action('admin_init', array($this, 'sendmachine_admin_init'));
		add_action('admin_menu', array($this, 'sendmachine_settings_skeleton'));
	}

	public function sendmachine_admin_init() {

		$this->manage_admin_requests();
		
		if(count($this->messages_queue)){
			
			foreach($this->messages_queue as $message){
				
				call_user_func_array("add_settings_error",$message);
			}
		}
	}
	
	public function enqueue_admin_message($message){
		
		array_push($this->messages_queue, $message);
	}
	
	public function styles_scripts() {

		wp_enqueue_style('sm-wp-admin-styles', plugins_url('static/css/sm-admin.css', SM_PLUGIN_FILE));
		wp_enqueue_script('sm-wp-admin-script', plugins_url('static/js/sm-admin.js', SM_PLUGIN_FILE));
		wp_localize_script('sm-wp-admin-script', 'SM_JS_DATA', array(
			'startcampaign_text' => __("You are about to start a campaign with the following properties:\nCampaign name: %s\nCampaign subject: %s\nContact list: %s\nSender address: %s\n\nAgree?", SM_LANGUAGE_DOMAIN)
		));
	}

	public function enable_settings($links) {

		$settings_link = '<a href="' . admin_url('admin.php?page=sendmachine_settings') . '">' . __('Settings', SM_LANGUAGE_DOMAIN) . '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}

	public function sendmachine_settings_skeleton() {

		add_menu_page('Sendmachine Setup', 'Sendmachine', 'manage_options', 'sendmachine_settings', array($this, 'sendmachine_load_page'), plugins_url('static/images/wp_sendmachine_logo.png', SM_PLUGIN_FILE));

		add_submenu_page('sendmachine_settings', 'Sendmachine -' . ' General', __('General', SM_LANGUAGE_DOMAIN), 'manage_options', 'sendmachine_settings', array($this, 'sendmachine_load_page'));
		add_submenu_page('sendmachine_settings', 'Sendmachine -' . ' Lists', __('Lists', SM_LANGUAGE_DOMAIN), 'manage_options', 'sendmachine_lists', array($this, 'sendmachine_load_page'));
		add_submenu_page('sendmachine_settings', 'Sendmachine -' . ' Feed', __('Feed', SM_LANGUAGE_DOMAIN), 'manage_options', 'sendmachine_feed', array($this, 'sendmachine_load_page'));
		add_submenu_page('sendmachine_settings', 'Sendmachine -' . ' Email Settings', __('Email Settings', SM_LANGUAGE_DOMAIN), 'manage_options', 'sendmachine_email', array($this, 'sendmachine_load_page'));
	}

	public function sendmachine_load_page() {

		$page = $this->get_current_page();
		$defaults = Sm_defaults::defaults();

		if ($page == 'sendmachine_settings') {

			$this->sm_include('wp_sm_general_settings.php');
		} elseif ($page == 'sendmachine_lists') {
			
			if(!isset($this->app['list']['message_invalid_email'])) {
				$this->app['list']['message_invalid_email'] = $defaults['list']['message_invalid_email'];
			}

			if(!isset($this->app['list']['label_submit_button'])) {
				$this->app['list']['label_submit_button'] = $defaults['list']['label_submit_button'];
			}
			
			if(!isset($this->app['list']['message_required_field'])) {
				$this->app['list']['message_required_field'] = $defaults['list']['message_required_field'];
			}
			
			if(!isset($this->app['list']['use_captcha'])) {
				$this->app['list']['use_captcha'] = $defaults['list']['use_captcha'];
			}
			
			if(!isset($this->app['list']['message_invalid_captcha'])) {
				$this->app['list']['message_invalid_captcha'] = $defaults['list']['message_invalid_captcha'];
			}
			
			$this->reallySimpleCaptcha = array(
				'slug' => 'really-simple-captcha',
				'is_ok' => true,
				'needs_install' => NULL,
				'needs_activation' => NULL
			);
			
			if(!class_exists('ReallySimpleCaptcha')) {
				
				$this->reallySimpleCaptcha['is_ok'] = false;
				$dir_exists = is_dir(WP_PLUGIN_DIR . '/' . $this->reallySimpleCaptcha['slug']);
				$plugin_files = get_plugins('/' . $this->reallySimpleCaptcha['slug']);
				
				if($dir_exists && !empty($plugin_files)) {

					$plugin_files_keys = array_keys($plugin_files);
					$plugin_file = $this->reallySimpleCaptcha['slug'] . '/' . $plugin_files_keys[0];
					
					$action_url = self_admin_url('plugins.php?action=activate&plugin=' . $plugin_file);
					$action = 'activate-plugin_' . $plugin_file;
					$this->reallySimpleCaptcha['needs_activation'] = wp_nonce_url($action_url, $action);
				} else {
					
					$action_url = self_admin_url('update.php?action=install-plugin&plugin=' . $this->reallySimpleCaptcha['slug']);
					$action = 'install-plugin_' . $this->reallySimpleCaptcha['slug'];
					$this->reallySimpleCaptcha['needs_install'] = wp_nonce_url($action_url, $action);
				}
			}

			$this->sm_include('wp_sm_list_settings.php');
		}
		elseif ($page == 'sendmachine_email') {

			$this->sm_include('wp_sm_email_settings.php');
		}
		elseif ($page == "sendmachine_feed"){
			
			if(isset($_REQUEST['sm_feed_preview_nl']))
				$this->sm_include('wp_sm_feed_preview.php', false);
			else
				$this->sm_include('wp_sm_feed_settings.php');
		}
	}

	public function sendmachine_wp_install() {

		$this->walk_array($this->app, Sm_defaults::defaults());
		update_option(SM_OPTIONS_APP_NAME, $this->app);
	}

	public function sendmachine_wp_uninstall() {

		delete_option(SM_OPTIONS_APP_NAME);
	}
	
	public function sm_include($path = "", $display_notifications = true) {
		
		if($display_notifications) settings_errors();
		include(SM_PLUGIN_DIR . '/views/' . $path );
	}

	public function get_current_page() {

		return isset($_GET['page']) ? $_GET['page'] : '';
	}
	
	private function manage_admin_requests() {

		if (empty($_REQUEST['sm_admin_wp_request'])) return false;
		
		$action = isset($_REQUEST['sm_action']) ? $_REQUEST['sm_action'] : NULL;
		$data = $_POST;
		
		if (isset($data['update']))  $this->walk_array($this->app, $data['update']);

		if($action) $message = $this->manage_admin_actions($action, $data);
		
		update_option(SM_OPTIONS_APP_NAME, $this->app);
		
		if(!isset($message)) $message = array("smSettingsUpdateSuccess","admin_settings_updated", __("Saved!", SM_LANGUAGE_DOMAIN), "updated");
		call_user_func_array("add_settings_error",$message);
	}
	
	private function manage_admin_actions($action = NULL, $data = array()) {
		
		if(!$this->api_connected) $this->reset_app_data();

		if ($action == "init_sm_data") {

			$this->sm = new Sm_api($this->app['credentials'], SM_WP_DEV_MODE);
			$is_connected = $this->sm->test_credentials();
			
			if($is_connected !== true && $is_connected !== false) {$this->app['not_connect_reason'] = $is_connected;$this->api_connected = false;}
			else { $this->app['not_connect_reason'] = NULL; $this->api_connected = $is_connected; }

			$this->app['api_connected'] = $this->api_connected;
			
			$this->reset_app_data();
			
			if($this->api_connected){
				
				$this->app['list']['data'] = $this->sm->get_lists();
				
				$this->app['email']['senderlist'] = $this->sm->get_from_emails();
				$this->app['email']['provider_settings'] = $this->sm->get_email_settings();
				$this->build_smtp_config();
			}

		}
		elseif ($action == "refresh_list_data") {

			if ($this->api_connected) {

				$this->app['list']['data'] = $this->sm->get_lists();
				
				if($this->app['list']['id']) {
					$this->app['list']['fields'] = $this->sm->list_fields($this->app['list']['id']);
					$this->check_simple_subscribe();
				}

				return array('smUpdateListSuccess', 'settings_updated', __("Contact Lists Updated!", SM_LANGUAGE_DOMAIN), "updated");
			} 
		}
		elseif($action == "update_list_fields"){

			if ($this->api_connected) {
				
				if ($this->app['list']['id']) {

					$this->app['list']['fields'] = $this->sm->list_fields($this->app['list']['id']);
					$this->check_simple_subscribe();

					if ($this->app['list']['fields'])
						return array('smListIdUpdateSuccess', 'list_updated', __("Contact list saved!", SM_LANGUAGE_DOMAIN), "updated");
					else
						return array('smListIdUpdateError', 'settings_error', __("Something went wrong. Please retry", SM_LANGUAGE_DOMAIN), "error");
				}
			}
		}
		elseif($action == "update_email_settings"){
			
			if($this->api_connected){
				
				$this->build_smtp_config();
				$from = $this->app['email']['from_email'];
				if(!trim($from)) return NULL;
				
				$this->app['email']['senderlist'] = $this->sm->get_from_emails();
				$condition = is_array($this->app['email']['senderlist']) && in_array(trim($from), $this->app['email']['senderlist']);
				$this->app['email']['emailconfirmed'] = $condition ? 1 : 0;
				
				if(!$this->app['email']['emailconfirmed']) {
					
					if(isset($data['sm_already_confirmed_wp']))
						return  array('smConfirmationMailLiar', 'confirmationmail_liar', __('Press "I already confirmed my address" only after you confirm your account.', SM_LANGUAGE_DOMAIN), "error");
					
					if(!empty($this->app['email']['enable_service']) && empty($this->app['email']['emailpending'])) {
						
						$this->bypass_emailconfirmation = true;
						
						$this->app['email']['emailpending'] = $from;
						
						if(wp_mail($from, "This is a test email", "This is a test email for confirmation purposes"))
							return array('smConfirmationMailSuccess', 'confirmationmail_success', sprintf(__("A confirmation email has been sent to %s.", SM_LANGUAGE_DOMAIN), $from), "updated");
						else
							return array('smConfirmationMailFail', 'confirmationmail_fail', sprintf(__("Something went wrong.For some reason a confirmation email cannnot be sent to '%s'. Please try again later.", SM_LANGUAGE_DOMAIN), $from), "error");
					}
				}
				else
					$this->app['email']['emailpending'] = 0;
			}
		}
		elseif ($action == "manage_feed") {

			$action = isset($data['sm_feed_send_campaign']) ? "sm_feed_send_campaign" : (isset($data['sm_feed_save_draft']) ? "sm_feed_save_draft" : "");
			$feed = array_merge($this->app['feed'], array("sm_feed_campaign_name" => $data['sm_feed_campaign_name'], "sm_feed_campaign_subject" => $data['sm_feed_campaign_subject']));
			
			return $this->feed_manager->manage_feed($feed, $this->sm, $action);
		}
		elseif($action == "preview_raw_template"){
			
			exit($this->feed_manager->preview_newsletter($this->app['feed']));
		}
	}

	/*
	 * Add/update values to an existing multidimensional array
	 * Used to update Sendmachine app data
	 */
	private function walk_array(&$master_array, $merge_values) {
		
		/*
		 * App keys that need to be html encoded 
		 */
		$keys_to_encode = $this->config["keys_to_encode"];
		
		function do_walk(&$master_array, $merge_values, $keys_to_encode = array(), $encode = false) {
			
			if (is_array($merge_values)) foreach ($merge_values as $k => $v) do_walk($master_array[$k], $v, $keys_to_encode, in_array($k, $keys_to_encode) ? true : false);
			else $master_array = $encode ? htmlspecialchars($merge_values, ENT_QUOTES) : $merge_values;
		}
		
		do_walk($master_array, $merge_values, $keys_to_encode);
	}
	
	private function reset_app_data() {
		
		$to_reset = $this->config["to_reset"];
		
		foreach($to_reset as $v){
					
			$k = explode('|', $v);
			$this->app[$k[0]][$k[1]] = NULL;
		}
		
		$this->app['list']['simple_subscribe'] = 1;
		$this->app['email']['emailconfirmed'] = 1;
	}
	
	private function check_simple_subscribe() {

		if ($this->app['list']['fields']) {
			
			$this->app['list']['simple_subscribe'] = 1;
					
			foreach ($this->app['list']['fields'] as $f) {

				if ($f['required'] && $f['name'] != "EMAIL") {

					$this->app['list']['simple_subscribe'] = 0;
					$this->app['list']['checkbox_comment'] = 0;
					$this->app['list']['checkbox_register'] = 0;
				}
			}
		}
	}
	
	private function build_smtp_config(){
		
		if(isset($this->app['email']['provider_settings']) && is_array($this->app['email']['provider_settings'])) {
			
			$this->app['email']['host'] = $this->app['email']['provider_settings']['hostname'];
			
			switch ($this->app['email']['encryption']) {
				case "no_encryption":
					$this->app['email']['port'] = $this->app['email']['provider_settings']['port'];
					break;
				case "ssl":
					$this->app['email']['port'] = $this->app['email']['provider_settings']['ssl_tls_port'];
					break;
				case "tls":
					$this->app['email']['port'] = $this->app['email']['provider_settings']['starttls_port'];
					break;
			}
		}
	}

}
