<?php
/**
 * Twilio SMS Relay and Callback
 * Created by PhpStorm.
 * User: ross
 * Date: 7/12/15
 * Time: 1:36 AM
 */

//Libraries
require_once 'vendor/twilio/sdk/Services/Twilio.php';
require_once "lib/AsteriskManager.php";
require_once "lib/VoIPms.php";
require_once "lib/Anveo.php";
require_once "lib/config.inc.php";

//Make sure we have something useful sent to the script
if(!$_REQUEST['From']) {
    die("Missing Parameter.");
}

//Let's do this!
receiveMessage();

function receiveMessage()
{
    global $users;
    $from = $_REQUEST['From'];
    $to = $_REQUEST['To'];
    $text = $_REQUEST['Body'];

    if (($text[0] == "c" || $text[0] == "C") && in_array($from, $users)) {

        //Remove 'c' from message to get contact name
        $contact = substr($text,2);

        //Dial by Number
        if (is_numeric($contact)) {
            $message = "Callback Requested: " . $contact;
            $fromNum = ($_REQUEST['FromCountry'] == "US") ? $twilio_usa : $twilio_france;
            sendMessage($from, $message, $fromNum);
            callback($from, $contact, $from);

        //Dial by Name
        } else if (array_key_exists($contact, $recipients)) {
            $num = substr($recipients[$contact],1);
            $message = "Callback Requested: " . $num;
            $fromNum = ($_REQUEST['FromCountry'] == "US") ? $twilio_usa : $twilio_france;
            sendMessage($from, $message, $fromNum);
            callback($from, $num, $from);

        //Something's wrong
        } else {
            $fromNum = ($_REQUEST['FromCountry'] == "US") ? $twilio_usa : $twilio_france;
            $message = "Unknown Contact: " . $contact;
            sendMessage($from, $message, $fromNum);
        }

    } else {
        //Shame on them.
        $message = "You are not authorized. Contact Ross for access.";
        $fromNum = ($_REQUEST['FromCountry'] == "US") ? $twilio_usa : $twilio_france;
        sendMessage($from, $message, $fromNum);

        //Let me know of the intrusion
        $message = "Unauthorized Access: " . $from . " Message: " . $text;
        sendMessage($admins['ross'], $message);
    }
}

/**
 * Send SMS Message using Twilio API
 * @param $toNum int SMS Recipient Phone Number (Including +CountryCode)
 * @param $text string Message to send to recipient
 * @param $fromNum int Force the number to send from on Twilio's side
 */
function sendMessage($toNum, $text, $fromNum = 'foo') {
    global $accountSid, $authToken, $twilio_usa, $twilio_france;
    $client = new Services_Twilio($accountSid, $authToken);

    //Select the appropriate source phone number
    //If sending from France to US, should be the US Twilio number.
    //If sending from US to France, should be the France Twilio number.
    if (!$fromNum || $fromNum == "foo") {
        $fromNum = ($_REQUEST['FromCountry'] == "FR") ? $twilio_usa : $twilio_france;
    }

    try {
        $message = $client->account->messages->create(array(
            "From" => $fromNum,
            "To" => $toNum,
            "Body" => $text,
        ));
    } catch (Services_Twilio_RestException $e) {
        echo $e->getMessage();
    }
}

/**
 * Place Callback using Asterisk VoIP Server
 * @param $source int Initiator of Callback (SMS Sender)
 * @param $dest int Recipient of Callback (Number from SMS)
 * @param $cidNum int Caller ID Number (shown on both sides)
 */
function callback($source, $dest, $cidNum) {
    global $ami_server, $ami_port, $ami_user, $ami_pass;
    $params = array('server' => $ami_server, 'port' => $ami_port);

    //Fix Source Number Format
    $source = "Local/" . $source . "@outbound-allroutes";

    /**
     * Instantiate Asterisk object and connect to server
     */
    $ast = new Net_AsteriskManager($params);

    /**
     * Connect to server
     */
    try {
        $ast->connect();
    } catch (PEAR_Exception $e) {
        echo $e;
    }

    /**
     * Login to manager API
     */
    try {
        $ast->login($ami_user, $ami_pass);
    } catch(PEAR_Exception $e) {
        echo $e;
    }

    /**
     * Place the Call
     */
    try {
        $ast->originateCall($dest, $source, 'from-internal', $cidNum, '1', '10000');
    } catch(PEAR_Exception $e) {
        echo $e;
    }
}