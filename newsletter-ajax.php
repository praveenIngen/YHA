<?php
include_once("includes/framework.php");
 $json = "";
if (JRequest::getVar('email') != "") {

    $data['email'] = JRequest::getVar('email');

    $data['cdate'] = time();

    $sql_count = "SELECT * FROM #__newsletter  where email='" . $data['email'] . "'";

    $result_count = $objDB->setQuery($sql_count);

    $num_rows = $objDB->num_rows($result_count);

    if ($num_rows > 0) {

        $json = '{"status":"'.Enum::HttpStatus()->ERROR.'", "msg": "You already registered for this email address."}';

    } else {

        $id = $objDB->insert_data("#__newsletter", $data);        

        if ($id) {            

            $subscriberInfo = $objNewsletter->getubScriberInfo($id);

            $objNewsletter->send_subscribe_email($subscriberInfo);

            $json = '{"status":"'.Enum::HttpStatus()->OK.'","msg": "Thank you for registering email address."}';
        }
        else{
            $json = '{"status":"'.Enum::HttpStatus()->ERROR.'","msg": "Something went wrong."}';                
        }
    }
}
else{
     $json = '{"status":"'.Enum::HttpStatus()->ERROR.'","msg": "PLease provide an email address!."}';
}
echo $json;
exit;     
  
?>