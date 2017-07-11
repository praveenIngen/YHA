
                            
<?php

                            

                            
function sendsmsGET($mobileNumber,$senderId,$route,$message,$serverUrl,$authKey)

                            
{

                            

                            
    $getData = 'authkey=' .$authKey.'&mobileNos='.$mobileNumber.'&message='.urlencode($message).'&senderId='.$senderId.'&route='.$route;

                            

                            
    //API URL

                            
    $url="http://".$serverUrl."/rest/services/sendSMS/sendGroupSms?AUTH_KEY=".$authKey."&"3.$getData;

                            

                            
    // init the resource

                            
    $ch = curl_init();

                            
    curl_setopt_array($ch, array(

                            
        CURLOPT_URL => $url,

                            
        CURLOPT_RETURNTRANSFER => true,

                            
        CURLOPT_SSL_VERIFYHOST => 0,

                            
        CURLOPT_SSL_VERIFYPEER => 0

                            

                            
    ));

                            

                            
    //get response

                            
    $output = curl_exec($ch);

                            

                            
    //Print error if any

                            
    if(curl_errno($ch))

                            
    {

                            
        echo 'error:' . curl_error($ch);

                            
    }

                            

                            
    curl_close($ch);

                            

                            
    return $output;

                            
}

                            

                            
function sendsmsPOST($mobileNumber,$senderId,$route,$message,$serverUrl,$authKey)

                            
{

                            

                            
    //Prepare you post parameters

                            
    $postData = array(

                            

                            
        'AUTH_KEY' => $authKey,

                            
        'MOBILE' => $mobileNumber,

                            
        "groupId" => "0",

                            
        'smsContent' => $message,

                            
        'SENDER_ID' => $senderId,

                            
        'ROUTE' => 1,

                            
        "smsContentType" =>'english'

                            
    );

                            

                            
    $data_json = json_encode($postData);

                            

                            
    $url="http://".$serverUrl."/rest/services/sendSMS/sendGroupSms?AUTH_KEY=".$authKey;

                            

                            
    // init the resource

                            
    $ch = curl_init();

                            

                            
    curl_setopt_array($ch, array(

                            
        CURLOPT_URL => $url,

                            
        CURLOPT_HTTPHEADER => array('Content-Type: application/json','Content-Length: ' . strlen($data_json)),

                            
        CURLOPT_RETURNTRANSFER => true,

                            
        CURLOPT_POST => true,

                            
        CURLOPT_POSTFIELDS => $data_json,

                            
        CURLOPT_SSL_VERIFYHOST => 0,

                            
        CURLOPT_SSL_VERIFYPEER => 0

                            
    ));

                            

                            
    //get response

                            
    $output = curl_exec($ch);

                            

                            
    //Print error if any

                            
    if(curl_errno($ch))

                            
    {

                            
        echo 'error:' . curl_error($ch);

                            
    }

                            
    curl_close($ch);

                            
    return $output;

                            
}

                            

  