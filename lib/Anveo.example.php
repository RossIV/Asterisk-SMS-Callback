<?php
/**
 * Anveo SMS API script v1.0
 *
 * PHP Script for sending SMS thru Anveo.com http gateway
 *
 * PHP versions 4 and 5 compiled with curl and https support
 * LICENSE: FREE
 *
 * @author     Anveo.com
 */

/**
 * USAGE
 * SendSMS(<to_number>,<from_number>,<message>)
 *
 */
function SendSMS($to_number,$from_number,$message){
    $apikey="- YOUR API KEY -"; //CHANGE ME

    echo "Sending sms ...\n";
    // need curl with https if using https://
    $ch = curl_init ("https://www.anveo.com/api/v1.asp?apikey=".$apikey."&action=sms&destination=".$to_number."&from=".$from_number."&message=".urlencode($message));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result=curl_exec ($ch);
    curl_close ($ch);

    /*parse the result*/
    $records_array=explode('^',$result);
    foreach($records_array as $record){
        $field_array=explode('=',$record);
        if (is_array($field_array)){
            $map[$field_array[0]]=$field_array[1];
        }
    }
    echo "result:".$map["result"]."\n\n";
    echo "error text:".$map["error"]."\n";
    echo "parts:".$map["parts"]."\n";
    echo "fee:".$map["fee"]."\n";
}
?>
	