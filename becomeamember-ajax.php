<?php

include_once("includes/framework.php");
if (JRequest::getVar('email') != "" && JRequest::getVar('fname') != "" && JRequest::getVar('mobile') != "") {
    $data['name'] = JRequest::getVar('fname');
    $data['email'] = JRequest::getVar('email');
    $data['mobile'] = JRequest::getVar('mobile');
    $data['cdate'] = time();
    $sql_count = "SELECT * FROM #__becomeamember  where email='" . $data['email'] . "'";
    $result_count = $objDB->setQuery($sql_count);
    $num_rows = $objDB->num_rows($result_count);
    $json = '['; // start the json array element
    $json_options = array();
    if ($num_rows > 0) {
        $json_options[] = '{"msg": "You already registered for this email address."}';
    } else {
        $id = $objDB->insert_data("#__becomeamember", $data);
        if ($id) {            
            $json_options[] = '{"msg": "Thank you for your interest."}';
        }
    }
    $json .= implode(',', $json_options); // join the objects by commas;
    $json .= ']'; // end the json array element
    echo $json;
}
?>