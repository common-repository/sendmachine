<?php

require_once '../SendmachineApiClient.php';

$username = "your_username";
$password = "your_password";

try {
    $sc = new SendmachineApiClient($username, $password);

    $details = array(
        "message" => array(
            "from" => array(
                "email" => "confirmed@email-address.com",
                "name" => "" // optional
            ),
            "reply_to" => array( // optional
                "email" => "",
                "name" => ""
            ),
            "subject" => "subject",
            "template_id" => "", // optional template id. If this key is provided, body_text and body_html will be replaced
            "body_html" => '<div style="color:red">I am some random body html [[MACRO1]] [[MACRO2]]</div>', // required if template_id was not provided
            "body_text" => "I am some random body text", // optional
            "headers" => array() // optional mail headers
        ),
        "personalization" => array(
            array(
                "to" => array(
                    array(
                        "email" => "example@recipient.com",
                        "name" => "" // optional
                    )
                ),
                "cc" => array( // optional
                    array(
                        "email" => "",
                        "name" => ""
                    )
                ),
                "bcc" => array( // optional
                    array(
                        "email" => "",
                        "name" => ""
                    )
                ),
                "subject" => "Personalization subject", // optional (replaces global subject if provided)
                "headers" => array(), // optional (replaces global headers if provided)
                "macros" => array( // optional
                    'MACRO1' => 'macro value 1',
                    'MACRO2' => 'macro value 2'
                ),
                "metadata" => array() // optional
            )
        )
    );

    /*
     * send email
     */
    $response = $sc->mail->send($details);
    print_r($response);
} catch (Sendmachine_Error $ex) {

    echo $ex->getMessage(); //error details
    echo $ex->getSendmachineStatus(); //error status
}