<?php
include_once("includes/framework.php");
 //get membercode
//$start = $id*10;
$id = 0;
$DeletedMemberSql = "select member_plan_id from #__deleted_member_plan_id where id = 1";
$resultMember = $objDB->setQuery($DeletedMemberSql);
if ($objDB->num_rows($resultMember)) {
    $IdSet =  $objDB->loadObject($resultMember);
    $id = $IdSet->member_plan_id;
} else {
    $id =  0;
    echo "HI ia ma ".$id;
    die;
}
$sql = "Select member_plan_id,photograph,residence_proof,signature from #__member_plans WHERE valid_to < '1490207400' and plan_code !='L0' and (photograph!='' Or residence_proof!='' Or signature!='') and member_plan_id > ".$id." order by member_plan_id asc";
  $result = $objDB->setQuery($sql);
        if ($objDB->num_rows($result)) {
            
            $resultData =  $objDB->loadObjectlist($result);
            $datajson = json_encode($resultData);
            DebugData("Data"."\n\n\n\t".$datajson);
            //pre($resultData);
            echo "Ok i am here";
         }   
            


      
               
                      
  


?>

