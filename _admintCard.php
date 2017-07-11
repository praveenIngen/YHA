<?php
include_once("includes/framework.php");
$pbkid = JRequest::getVar('pbkid');
$pbk_id = JRequest::getVar('pbk_id');
if ($pbk_id != "") {

    $HTML = $objProgramme->getAdminCard($pbk_id);
    die($HTML);
}
?>

<iframe class="iframeClass" style="background-color:#ffffff;" height="500"  width="550" src="<?php echo SITE_URL?>/_admintCard.php?pbk_id=<?php echo $pbkid;?>"></iframe>