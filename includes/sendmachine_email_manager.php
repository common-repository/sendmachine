<?php

class Sm_email_manager {
	
	private $phpmailer_headers;

	public function __construct() {

		add_action('init', array($this, 'manage_email_requests'));

		add_action('comment_post', array($this, 'add_sm_headers'));
		add_action('register_post', array($this, 'add_sm_headers'));

		add_action('phpmailer_init', array($this, 'configure_smtp'));
	}

	public function add_sm_headers() {
		
		$app = Sm_wp::instance()->app;
		$key = current_filter();
		
		if (!empty($app['email'][$key]) && !empty($app['email'][$key . "_label"]))
			$this->phpmailer_headers = current_filter();
	}

	public function configure_smtp($phpmailer) {
		
		$sm = Sm_wp::instance();
		
		if (empty($sm->app['email']['enable_service']) || (empty($sm->app['email']['emailconfirmed']) && empty($sm->bypass_emailconfirmation))) return false;
		
		$required_items = array('encryption','host','port','from_email');
		
		foreach($required_items as $k => $v) {
			
			if(!isset($sm->app['email'][$v]) || !trim($sm->app['email'][$v])) return false;
		}

		$phpmailer->isSMTP();

		$phpmailer->SMTPSecure = $sm->app['email']['encryption'] == "no_encryption" ? "" : $sm->app['email']['encryption'];

		$phpmailer->Host = $sm->app['email']['host'];
		$phpmailer->Port = $sm->app['email']['port'];
		
		$phpmailer->From = $sm->app['email']['from_email'];
		$phpmailer->FromName = isset($sm->app['email']['from_name']) ? $sm->app['email']['from_name'] : NULL;
		
		$phpmailer->SMTPAuth = true;
		$phpmailer->Username = $sm->app['credentials']['api_username'];
		$phpmailer->Password = $sm->app['credentials']['api_password'];

		$phpmailer = apply_filters('sm_mail_custom_options', $phpmailer);
		
		if ($this->phpmailer_headers) {

			$e = new Exception();
			$trace = method_exists($e, 'getTrace') ? $e->getTrace() : NULL;
			$caller_func = NULL;

			if (is_array($trace)) {

				foreach ($trace as $k => $v) {

					if (isset($v['function']) && $v['function'] == "wp_mail") {
						$caller_func = $v;
					}
				}
			}

			$recipient = isset($caller_func['args'][0]) ? $caller_func['args'][0] : NULL;

			if ($this->phpmailer_headers == "register_post" && $recipient == get_option('admin_email'))
				return false;
			
			if(SM_SITE_APP_URL) $phpmailer->SMTPDebug  = 1;
			else $phpmailer->SMTPDebug = 0;
			
			$phpmailer->AddCustomHeader(SM_CAMPAIGN_HEADER . ": " . $sm->app['email'][$this->phpmailer_headers . "_label"]);
		}
	}

	public function manage_email_requests() {

		if (empty($_REQUEST['sm_email_wp_request'])) return false;

		$action = isset($_REQUEST['sm_action']) ? $_REQUEST['sm_action'] : NULL;
		$data = $_POST;

		if ($action == "send_test_email") {
			
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$ret = NULL;

			if(isset($data['email']) && trim($data['email']))
			$ret = wp_mail($data['email'], 'Test email -'.$blogname, 'This is a test email from your wordpress site');

			if($ret) $message = array("smEmailSentSuccess","email_sent_success", sprintf(__("Email to %s sent successfully!", SM_LANGUAGE_DOMAIN), $data['email']), "updated");
			else $message = array("smEmailSentError","email_sent_error", __("Something went wrong. Email not sent.", SM_LANGUAGE_DOMAIN), "error");
			
			Sm_wp::instance()->enqueue_admin_message($message);
		}
	}

}
