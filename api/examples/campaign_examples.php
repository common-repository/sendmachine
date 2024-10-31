<?php

require_once '../SendmachineApiClient.php';

$username = "your_username";
$password = "your_password";

try {
	$sc = new SendmachineApiClient($username, $password);

	/*
	 * create new campaign
	 */
	$campaign1 = $sc->campaigns->create(array(
		'name' => 'Campaign Name',
		'subject' => 'Campaign Subject',
		'contactlist_id' => 1,
		'sender_email' => 'sender_email@example.com'
	));

	$campaign_id1 = $campaign1['id'];

	/*
	 * list campaigns
	 */
	$campaigns_list = $sc->campaigns->get();
	print_r($campaigns_list);

	/*
	 * get campaign details
	 */
	$campaign_details = $sc->campaigns->details($campaign_id1);
	print_r($campaign_details);

	/*
	 * update created campaign, add body html
	 */
	$sc->campaigns->update($campaign_id1, array(
		'body_html' => '<div style="color:red">I am some random body html</div><a href="[[UNSUB_LINK]]" >unsubscribe here</a>'
	));

	/*
	 * get campaign content
	 */
	$campaign_body = $sc->campaigns->content($campaign_id1);
	print_r($campaign_body);

	/*
	 * duplicate campaign
	 */
	$campaign2 = $sc->campaigns->duplicate($campaign_id1);
	$campaign_id2 = $campaign2['new_id'];

	/*
	 * archive campaign
	 */
	$sc->campaigns->archive($campaign_id1);

	/*
	 * campaign ready for sending
	 */
	if ($sc->campaigns->ready($campaign_id2)) {

		/*
		 * send test email 
		 */
		$sc->campaigns->test($campaign_id2, "email1@example.com,email2@example.com");
	}

	/*
	 * unarchive campaign
	 */
	$sc->campaigns->unarchive($campaign_id1);

	/*
	 * schedule campaign
	 */
	$sc->campaigns->schedule($campaign_id1, '2015-01-30T07:00');

	/*
	 * send campaign
	 */
	$sc->campaigns->send($campaign_id2);

	/*
	 * unschedule campaign
	 */
	$sc->campaigns->unschedule($campaign_id1);
	
} 
catch (Sendmachine_Error $ex) {

	echo $ex->getMessage(); //error details
	echo $ex->getSendmachineStatus(); //error status
}