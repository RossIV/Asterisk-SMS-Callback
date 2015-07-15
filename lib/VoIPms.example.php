<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 7/15/15
 * Time: 11:46 PM
 */

$voipms = new VoIPms();

/* Send SMS */
$response = $voipms->sendSMS($SMSsender,$smsnumber,$smsmessage);

/* Get Errors - SMS_failed */
if($response[status]!='success'){
    echo $response[status];
    exit;
}