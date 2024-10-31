<?php

class Sm_api {
	
	private $api;
	private $credentials;
	
	private $debug;

	public function __construct($credentials = NULL, $debug = false) {
		
		$this->credentials = $credentials;
		
		$this->debug = $debug;

		require_once SM_PLUGIN_DIR.'api/SendmachineApiClient.php';

		if (!empty($credentials['api_username']) && !empty($credentials['api_password'])) {

			try {
				$this->api = new SendmachineApiClient($credentials['api_username'], $credentials['api_password']);
			} 
			catch (Sendmachine_Error $e) {$this->print_exceptions($e);}
			catch (Http_Error $e) {$this->print_exceptions($e);}
		}
		
	}
	
	public function test_credentials(){

		if (empty($this->credentials['api_username']) || empty($this->credentials['api_password'])) return "You must provide a username and password";
		
		try{
			$res = $this->api->account->details();
		}
		catch (Sendmachine_Error $e) {$this->print_exceptions($e); return $e->getMessage();}
		catch (Http_Error $e) {$this->print_exceptions($e); return false;}

		if(isset($res['user'])) return true;
		
		$this->print_exceptions($res);
		return false;
	}

	public function get_lists() {

		try {
			$res = $this->api->lists->get();
			
		}
		catch (Sendmachine_Error $e) {$this->print_exceptions($e); return NULL;}
		catch (Http_Error $e) {$this->print_exceptions($e); return NULL;}
		
		if(isset($res['contactlists']) && count($res['contactlists'])) return $res['contactlists'];
		
		$this->print_exceptions($res);
		return false;
	}
	
	public function subscribe($list_id = NULL, $email_address = NULL, $data = NULL) {

		if(!$list_id || !$email_address) return NULL;
		
		try{
			$res = $this->api->lists->manage_contact($list_id, $email_address, $data);
		}
		catch (Sendmachine_Error $e) {$this->print_exceptions($e); return NULL;}
		catch (Http_Error $e) {$this->print_exceptions($e); return NULL;}
		
		if(isset($res['status']) && $res['status'] == "saved") return true;
		
		$this->print_exceptions($res);
		return false;
	}
	
	public function mass_subscribe($list_id = NULL, $recipients = ""){
		
		if(!$list_id || !$recipients || !count($recipients)) return NULL;
		
		try{
			$res = $this->api->lists->manage_contacts($list_id, $recipients);
		}
		catch (Sendmachine_Error $e) {$this->print_exceptions($e); return NULL;}
		catch (Http_Error $e) {$this->print_exceptions($e); return NULL;}
		
		if(isset($res['status']) && $res['status'] == "saved") return true;
		
		$this->print_exceptions($res);
		return false;
	}
	
	public function get_recipient($list_id = NULL, $recipient = ""){
		
		if(!$list_id) return NULL;
		
		try {
			$res = $this->api->lists->contact_details($list_id, $recipient);
		}
		catch (Sendmachine_Error $e) {$this->print_exceptions($e); return NULL;}
		catch (Http_Error $e) {$this->print_exceptions($e); return NULL;}
		
		if(isset($res['contacts'])) return $res['contacts'];
		
		$this->print_exceptions($res);
		return false;
	}
	
	public function list_fields($list_id = ""){
		
		try {
			$res = $this->api->lists->custom_fields($list_id);
			
		}
		catch (Sendmachine_Error $e) {$this->print_exceptions($e); return NULL;}
		catch (Http_Error $e) {$this->print_exceptions($e); return NULL;}
		
		if(isset($res['custom_fields']) && count($res['custom_fields'])) return $res['custom_fields'];
		
		$this->print_exceptions($res);
		return false;
	}
	
	public function get_from_emails() {
		
		try {
			$res = $this->api->sender->get();
		} 
		catch (Sendmachine_Error $e) {$this->print_exceptions($e);return NULL;} 
		catch (Http_Error $e) {$this->print_exceptions($e);return NULL;}

		if(isset($res['senderlist']) && count($res['senderlist'])) return array_column($res['senderlist'], 'email');
		
		$this->print_exceptions($res);
		return false;
	}
	
	public function get_email_settings(){
		
		try {
			$res = $this->api->account->smtp();
		} 
		catch (Sendmachine_Error $e) {$this->print_exceptions($e);return NULL;} 
		catch (Http_Error $e) {$this->print_exceptions($e);return NULL;}

		if(isset($res['smtp']) && count($res['smtp'])) return $res['smtp'];
		
		$this->print_exceptions($res);
		return false;
	}
	
	public function create_campaign($params = array()){
		
		if(!$params) return NULL;
		
		try {
			$res = $this->api->campaigns->create($params);
		} 
		catch (Sendmachine_Error $e) {$this->print_exceptions($e);return $e->getMessage();} 
		catch (Http_Error $e) {$this->print_exceptions($e);return NULL;}

		return $res;
	}
	
	public function test_campaign($campaign_id = NULL){
		
		if(!$campaign_id) return NULL;
		
		try {
			$res = $this->api->campaigns->ready($campaign_id);
		} 
		catch (Sendmachine_Error $e) {$this->print_exceptions($e);return NULL;} 
		catch (Http_Error $e) {$this->print_exceptions($e);return NULL;}

		if(isset($res['status'])) return $res["status"];
		
		$this->print_exceptions($res);
		return false;
	}
	
	public function start_campaign($campaign_id = NULL){
		
		if(!$campaign_id) return NULL;
		
		try {
			$res = $this->api->campaigns->send($campaign_id);
		} 
		catch (Sendmachine_Error $e) {$this->print_exceptions($e);return NULL;} 
		catch (Http_Error $e) {$this->print_exceptions($e);return NULL;}

		if(isset($res['status']) && $res['status'] == "launched") return true;
		
		$this->print_exceptions($res);
		return false;
	}

	public function print_exceptions($ex){

		if ($this->debug) {
			$msg = is_object($ex) && method_exists($ex, "getMessage") ? $ex->getMessage() : $ex;
			echo "<div class='sm_wp_display_exceptions' >New exception occurred: " . $msg . "</div>";
		}
	}

}
