<?php
/**
 * Created by PhpStorm.
 * User: ross
 * Date: 7/15/15
 * Time: 11:52 PM
 */

//VoIP SMS Provider Selection
//'1' is Twilio, '2' is Anveo, '3' is VoIPms
$provider = "";

//Twilio API Credentials
$twilio_accountSid = "";
$twilio_authToken  = "";

//Anveo API Credentials
$anveo_api_key = "";

//VoIPms API Credentials
$voipms_api_user = "";
$voipms_api_pass = "";

//Asterisk Manager Interface Credentials
$ami_server = ''; //IP Address or Hostname
$ami_port   = '5038';
$ami_user   = '';
$ami_pass   = '';

//Users allowed to request callbacks
$users = array (
    'Ward'      =>  '+18430000000',
    'Lorne'     =>  '+18430000001',
    'Joe'       =>  '+16780000002',
    'Tom'       =>  '+17030000003',
    'Allison'   =>  '+18430000004'
);

//SMS-Enabled DID Number
$sms_did = "+18430000000";