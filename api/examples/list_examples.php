<?php

require_once '../SendmachineApiClient.php';

$username = "your_username";
$password = "your_password";

try {
	$sc = new SendmachineApiClient($username, $password);

	/*
	 * create a new contact list
	 */
	$sc->lists->create(array(
		"website" => "siteexample.com",
		"default_from_name" => "Default Name",
		"default_from_email" => "email@example.com",
		"address" => array(
			"zip_code" => 2353,
			"country" => "US",
			"city" => "sample city",
			"address" => "sample address"
		),
		"phone" => 56756, //optional
		"name" => "sample name",
		"subscription_reminder" => "sample reminder",
		"company" => "sample company name",
		"send_goodbye" => 0,
		"default_subject" => "default subject"//optional
	));

	/*
	 * get all contact lists
	 * available parameters: filter, order_by
	 */
	$contactlists = $sc->lists->get(20, 'email');
	print_r($contactlists);

	/*
	 * get a single contact list
	 */
	$list_id = $contactlists['contactlists'][0]['list_id'];
	$list_details = $sc->lists->details($list_id);
	print_r($list_details);

	/*
	 * update list details
	 */
	$sc->lists->edit($list_id, array('name' => 'new_list_name'));
	$list_details = $sc->lists->details($list_id);
	print_r($list_details);
	/*
	 * add contacts list to a existing list
	 */
	$sc->lists->manage_contacts($list_id, array('email2@example.com', 'email3@example.com'), 'subscribe');
	
	$updated_details = $sc->lists->recipients($list_id);
	print_r($updated_details);

	$contact_details = $sc->lists->contact_details($list_id, 'email2@example.com');
	print_r($contact_details);
	
	$sc->lists->manage_contact($list_id, 'email2@example.com', array('status' => 'unsubscribe'));
	
	/*
	 * get segments
	 */
	$segments = $sc->lists->list_segments($list_id);
	print_r($segments);
	
	/*
	 * delete contact list
	 */
	$sc->lists->delete($list_id);
	
	$contactlists_updated = $sc->lists->get(20, 'email');
	print_r($contactlists_updated);
	
}
catch (Sendmachine_Error $ex) {

	echo $ex->getMessage(); //error details
	echo $ex->getSendmachineStatus(); //error status
}