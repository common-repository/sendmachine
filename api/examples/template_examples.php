<?php

require_once '../SendmachineApiClient.php';

$username = "your_username";
$password = "your_password";

try {
	$sc = new SendmachineApiClient($username, $password);
	
	/*
	 * create new template
	 */
	$sc->templates->create("template_name", "template content");

	/*
	 * list templates
	 */
	$template_list = $sc->templates->get(25, 0);//limit, offset
	print_r($template_list);

	/*
	 * get template details (including body)
	 */
	$template_id = $template_list['list'][0]['tpl_id'];
	$details = $sc->templates->details($template_id);
	print_r($details);

	/*
	 * 	update created template's body
	 */
	$sc->templates->update($template_id, "new template content");

	$updated_details = $sc->templates->details($template_id);
	print_r($updated_details);

	/*
	 * delete template
	 */
	$sc->templates->delete($template_id);
	
} 
catch (Sendmachine_Error $ex) {

	echo $ex->getMessage(); //error details
	echo $ex->getSendmachineStatus(); //error status
}
