<?php

class Sm_subscribe_manager {
	
	private $subscribe_notification;
	
	private $form_count = 0;
	private $captcha_prefix = '83921';

	public function __construct($app_config) {

		$this->app = $app_config;
		
		if (!empty($this->app['list']['checkbox_comment'])) add_action('comment_form', array($this, 'build_checkbox'));
		if (!empty($this->app['list']['checkbox_register'])) add_action('register_form', array($this, 'build_checkbox'));

		add_action('init', array($this, 'manage_subscribe_requests'));

		add_shortcode('sm_subscribe_form', array($this, 'build_shortcode_form'));
	}

	public function build_widget_form($args, $instance) {
		
		if(!$this->can_build()) return false;

		$title = $instance['title'];

		$out = $args['before_widget'];

		$out .=$args['before_title'] . $title . $args['after_title'];
		
		$out .= $instance['description'] ? "<p>".$instance['description']."</p>" : "";

		$out .= $this->build_form($args['id']);

		$out .= $args['after_widget'];

		return $out;
	}

	public function build_shortcode_form() {

		if(!$this->can_build()) return false;
		
		return $this->build_form();
	}

	public function build_checkbox() {
		
		if(!$this->can_build()) return false;

		$checked = !empty($this->app['list']['checkbox_checked']) ? "checked" : "";

		echo ''
		. '<input type="hidden" name="sm_subscribe_wp_request" value="1"/>'
		. '<input type="hidden" name="sm_action" value="subscribe_checkbox"/> '
		. '<label>'
		. '<br><input type="checkbox" name="sm_do_subscribe" value="1" ' . $checked . ' /> ' . $this->app['list']['checkbox_label'] . '</label><br><br>';
	}
	
	private function can_build() {
		
		if(empty($this->app['list']['id']))
			return false;
		
		if (!empty($this->app['list']['hide_subscribed']) && is_user_logged_in()) {
			$is_subbed = (bool)get_user_meta(get_current_user_id(), SM_USER_META_NAME, true);
			
			return $is_subbed ? false : true;
		}
		
		return true;
	}

	private function build_form($instance_id = "") {
		
		$this->form_count++;
		$defaults = Sm_defaults::defaults();
		
		if ($this->form_count <= 1) {
			
			wp_enqueue_style('sm-wp-widget-styles', plugins_url('static/css/sm-widget.css', SM_PLUGIN_FILE));
			wp_enqueue_script('sm-wp-widget-script', plugins_url('static/js/sm-widget.js', SM_PLUGIN_FILE));
			wp_localize_script('sm-wp-widget-script', 'SM_JS_DATA', array(
				'loading_img' => plugin_dir_url(SM_PLUGIN_FILE) . 'static/images/loading.gif',
				'redirect' => $this->app['list']['redirect_subscribed'],
				'response_not_arrived' => $this->app['list']['message_not_subscribed'],
				'invalid_email' => isset($this->app['list']['message_invalid_email']) ? $this->app['list']['message_invalid_email'] : $defaults['list']['message_invalid_email']
			));
		}

		$fields = isset($this->app['list']['fields']) ? $this->app['list']['fields'] : NULL;
		if (!$fields) return "";
		
		$list_fields = '';
		$captcha_field = '';
		
		foreach ($fields as $list_field) {

			if ($list_field['visible']) {

				if (!isset($list_field['form_name']))
					$list_field['form_name'] = ucfirst(strtolower($list_field['name']));

				$required = ($list_field['required']) ? "*" : "";
				$element_id = strtolower("form_input_{$list_field['form_name']}_$instance_id");

				$list_fields .= "<div class='sm_wp_input_group'><label for='$element_id' class='sm_wp_form_label'>" . $list_field['form_name'] . " $required</label>";

				if (in_array($list_field['cf_type'], array("text", "number", "email","date","birthday"))) {
					$placeholder = "";
					
					if($list_field['cf_type'] == "date") $placeholder = "yyyy/mm/dd";
					elseif($list_field['cf_type'] == "birthday") $placeholder = "mm/dd";
					
					$list_fields .= "<input id='$element_id' class='sm_wp_form_input_text' placeholder='$placeholder' type='text' name='" . $list_field['name'] . "'/>";
				} 
				elseif ($list_field['cf_type'] == "radiobutton") {

					foreach ($list_field['options'] as $option) {

						$list_fields .= "<label class='sm_wp_form_radio_label'><input class='sm_wp_form_input_radio' type='radio' value='" . $option . "' name='" . $list_field['name'] . "' /> " . $option . "</label><br>";
					}
				} 
				elseif ($list_field['cf_type'] == "dropdown") {

					$list_fields .= "<select class='sm_list_dropdown sm_wp_form_select' name='" . $list_field['name'] . "'>";
					$list_fields .= "<option></option>";

					foreach ($list_field['options'] as $option) {

						$list_fields .= "<option value='" . $option . "'>" . $option . "</option>";
					}

					$list_fields .= "</select>";
				}
				$list_fields .= "</div>";
			}
		}
		
		if(isset($this->app['list']['use_captcha']) && $this->app['list']['use_captcha']) {
			
			if(class_exists('ReallySimpleCaptcha')) {
				
				list($captcha_file, $prefix) = $this->genCaptcha();
				
				$captcha_field .= '<table><tbody><tr>';
				$captcha_field .= "<td><img id='form_captcha_img_{$instance_id}' class='sm_wp_form_captcha_img' src='{$captcha_file}' alt='Captcha'></td>";
				$captcha_field .= '<td>';
				$captcha_field .= "<input id='form_input_captcha_{$instance_id}' class='sm_wp_form_input_text' type='text' name='captcha'/>";
				$captcha_field .= "<input id='form_captcha_prefix_{$instance_id}' type='hidden' name='captcha_prefix' value='{$prefix}'/>";
				$captcha_field .= '</td>';
				$captcha_field .= '</tr></tbody></table>';
			}
		}
		
		$msg = isset($this->subscribe_notification['default']) && $this->subscribe_notification['default'] ? __($this->subscribe_notification['message'], SM_LANGUAGE_DOMAIN) : $this->subscribe_notification['message'];
		$notices = $this->subscribe_notification && $this->subscribe_notification['form_nr'] == $this->form_count ? "<div class='".$this->subscribe_notification['status']."' >".$msg."</div>" : "";
		$submit_action = isset($this->app['list']['reload_subscribe']) && $this->app['list']['reload_subscribe'] ? "" : "subscribe_user(this);return false;" ;
				
		$subscribe_label = isset($this->app['list']['label_submit_button']) ? $this->app['list']['label_submit_button'] :  $defaults['list']['label_submit_button'];
		
		return ''
				. '<div class="sendmachine_wp_subscribe" >'
				. '<form id="sm_subscribe_form_'.$instance_id.'" class="subscribe_form" method="post" onsubmit="'.$submit_action.'" >'
				. '<input type="hidden" name="sm_subscribe_wp_request" value="1"/>'
				. '<input type="hidden" name="sm_action" value="subscribe_form"/> '
				. '<input type="hidden" name="sm_form_nr" value="'.$this->form_count.'"/> '
				. $list_fields 
				. $captcha_field
				. '<div class="sm_wp_form_submit_button_wrapper"><input class="sm_wp_form_submit_button" type="submit" value="'. $subscribe_label .'" /></div>'
				. '<div class="sm_wp_sub_req_resp" >' . $notices . '</div>'
				. '</form>'
				. '</div>';
	}

	public function manage_subscribe_requests() {
		
		if (empty($_REQUEST['sm_subscribe_wp_request'])) return false;
		
		$action = isset($_REQUEST['sm_action']) ? $_REQUEST['sm_action'] : NULL;
		$data = $_POST;
		
		$is_ajax = isset($data['is_sm_ajax_request']) ? $data['is_sm_ajax_request'] : NULL;
		$sm_api = Sm_wp::instance()->sm;
		$defaults = Sm_defaults::defaults();

		if ($action == "subscribe_form") {

			$email = isset($data['EMAIL']) ? $data['EMAIL'] : NULL;
			unset($data['EMAIL']);
			$captcha = isset($data['captcha']) ? $data['captcha'] : NULL;
			unset($data['captcha']);
			$captcha_prefix = isset($data['captcha_prefix']) ? $data['captcha_prefix'] : NULL;
			unset($data['captcha_prefix']);
			
			$needs_captcha = false;
			if (isset($this->app['list']['use_captcha']) && $this->app['list']['use_captcha'] && class_exists('ReallySimpleCaptcha')) {
				$needs_captcha = true;
			}
			
			$respawn_captcha = false;
			if($needs_captcha && $is_ajax) $respawn_captcha = true;
			
			$form_nr = isset($data['sm_form_nr']) ? $data['sm_form_nr'] : 0;
			
			$fields = $this->app['list']['fields'];
			if(!$fields) {
				$msg = array("message" => $this->app['list']['message_not_subscribed'], "status" => "error");
				$this->respond($msg, $is_ajax, $form_nr);
				return false;
			}
			
			if(!$email) {
				$_msg = isset($this->app['list']['message_invalid_email']) ? $this->app['list']['message_invalid_email'] : $defaults['list']['message_invalid_email'];
				$this->respond(array("message" => $_msg, "status" => "error"), $is_ajax, $form_nr);
				return false;
			}
			
			$fn = array();
			$has_error = false;
			foreach($fields as $f) $fn[] = $f['name'];

			foreach($data as $k => $v) {
				
				if(!in_array($k,$fn)) {
					unset($data[$k]);
					continue;
				}
				
				foreach($fields as $field) {
					if($field['name'] === $k) {
						if($field['required'] && !$v) {
							$has_error = true;
						}
						break;
					}
				}
			}
			
			if ($has_error) {
				$_msg = isset($this->app['list']['message_required_field']) ? $this->app['list']['message_required_field'] : $defaults['list']['message_required_field'];
				$this->respond(array("message" => $_msg, "status" => "error"), $is_ajax, $form_nr);
				return false;
			}
				
			if($needs_captcha) {
				
				$correct = false;
				
				if(substr($captcha_prefix, 0, strlen($this->captcha_prefix)) === $this->captcha_prefix) {
					
					$captcha_instance = new ReallySimpleCaptcha();
					$correct = $captcha_instance->check($captcha_prefix, $captcha);
					$captcha_instance->remove($captcha_prefix);
				}
				
				if(!$correct) {
					$_msg = isset($this->app['list']['message_invalid_captcha']) ? $this->app['list']['message_invalid_captcha'] : $defaults['list']['message_invalid_captcha'];
					$this->respond(array("message" => $_msg, "status" => "error"), $is_ajax, $form_nr, $respawn_captcha);
					return false;
				}
			}

			if ($sm_api->get_recipient($this->app['list']['id'], $email)) {
				$msg = array("message" => $this->app['list']['message_subscriber_exists'], "status" => "error");
				$this->respond($msg, $is_ajax, $form_nr, $respawn_captcha);
				return false;
			}
			
			if ($resp = $sm_api->subscribe($this->app['list']['id'], $email, $data)) {

				if (is_user_logged_in()) add_user_meta( get_current_user_id(), SM_USER_META_NAME, 1);
				
				if (trim($this->app['list']['redirect_subscribed']) && !$is_ajax) wp_redirect($this->app['list']['redirect_subscribed']);

				$msg = array("message" => $this->app['list']['message_success_subscribe'], "status" => "success");
				$this->respond($msg, $is_ajax, $form_nr, $respawn_captcha);
				return true;
			}
			
			$msg = array("message" => $this->app['list']['message_not_subscribed'], "status" => "error");
			$this->respond($msg, $is_ajax, $form_nr, $respawn_captcha);
			return false;
		}
		elseif ($action == "subscribe_checkbox") {

			if (empty($data['sm_do_subscribe'])) return false;

			if (is_user_logged_in()) {
				
				$user = wp_get_current_user();
				$email = $user->user_email;
			} 
			else {

				if (!empty($data['email']))
					$email = $data['email'];
				elseif (!empty($data['user_email']))
					$email = $data['user_email'];
				else
					return false;
			}

			$resp = $sm_api->subscribe($this->app['list']['id'], $email);
			
			if (is_user_logged_in() && $resp) add_user_meta( get_current_user_id(), SM_USER_META_NAME, 1);
		}
		elseif ($action == "sync_users") {
			
			if (empty($this->app['list']['id'])) {
				$message = array("smUsersSyncErrorApiLost", "users_sync_api_lost", __("API must be connected and you have to select a list in order to sync your users.", SM_LANGUAGE_DOMAIN), "error");
				Sm_wp::instance()->enqueue_admin_message($message);
				return NULL;
			}

			$ret = NULL;
			
			$users = get_users(array( 'fields' => array( 'user_email' ) ));
			
			$recipients = array();
			foreach($users as $k => $u){
				array_push($recipients, $u->user_email);
			}
			
			$cnt = count($recipients);
			
			$ret = $sm_api->mass_subscribe($this->app['list']['id'], $recipients);
			
			if($ret) $message = array("smUsersSyncSuccess","users_sync success", sprintf(__("Sync complete! %u users were added to your contact list.", SM_LANGUAGE_DOMAIN), $cnt), "updated");
			else $message = array("smUsersSyncError","users_sync_error", __("Something went wrong. Users not synced.", SM_LANGUAGE_DOMAIN), "error");

			Sm_wp::instance()->enqueue_admin_message($message);
		}
	}
	
	private function genCaptcha() {
		
		if(!class_exists('ReallySimpleCaptcha')) {
			return false;
		}
		
		$captcha_instance = new ReallySimpleCaptcha();
		
		// cleanup files older than 30 minutes
		$captcha_instance->cleanup(30);
		
		$word = $captcha_instance->generate_random_word();
		
		$prefix = $this->captcha_prefix . mt_rand();
		$captcha_image = $captcha_instance->generate_image($prefix, $word);
		$captcha_file = rtrim(get_bloginfo('wpurl'), '/') . '/wp-content/plugins/really-simple-captcha/tmp/' . $captcha_image;
		
		return array($captcha_file, $prefix);
	}
	
	private function respond($msg = "", $is_ajax = false, $form_nr = 0, $respawn_captcha = false) {
		
		if($respawn_captcha) {
			list($captcha_file, $prefix) = $this->genCaptcha();
			$msg['respawn_captcha'] = array(
				"img" => $captcha_file,
				"prefix" => $prefix
			);
		}
		
		if ($is_ajax) {
			
			echo json_encode($msg);
			exit();
		} else {
			
			$msg['form_nr']= $form_nr;
			$this->subscribe_notification = $msg;
			return true;
		}
	}

}
