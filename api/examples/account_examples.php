<?php

require_once '../SendmachineApiClient.php';

$username = "your_username";
$password = "your_password";

try {
	$sc = new SendmachineApiClient($username, $password);
	
	/*
	 * Get details about the current active package of the user
	 */
	$package = $sc->account->package();
	print_r($package);
	
	/*
	 * Get smtp settings
	 * The SMTP user and password are also used for API Auth.
	 */
	$smtp_settings = $sc->account->smtp();
	print_r($smtp_settings);

	/*
	 * Get user details
	 */
	$account_details = $sc->account->details();
	print_r($account_details);

	$rating = $sc->account->rating();
	print_r($rating);
	
} 
catch (Sendmachine_Error $ex) {

	echo $ex->getMessage(); //error details
	echo $ex->getSendmachineStatus(); //error status
}