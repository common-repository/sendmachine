<?php

require_once '../SendmachineApiClient.php';

$username = "your_username";
$password = "your_password";

try {
	$sc = new SendmachineApiClient($username, $password);
	
	/*
	 * add new sender
	 */
	$sc->sender->add('email@example.com');
	
	/*
	 * get sender list
	 */
	$sender_list = $sc->sender->get('all', 'email', 'none');
	print_r($sender_list);
	
	/*
	 * delete sender
	 */
	$sc->sender->delete('email@example.com');
	
} 
catch(Sendmachine_Error $ex){
	
	echo $ex->getMessage(); //error details
	echo $ex->getSendmachineStatus(); //error status
}