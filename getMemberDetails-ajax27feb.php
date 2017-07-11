<?php
include_once("includes/framework.php");
include_once(PATH_CONTROLLERS."/MemberShipController.php");
$memberCode = JRequest::getVar('memberCode');
if($memberCode!=null)
{
	$ApiResponse = $MEMBERSHIP_CONTROLLER->Get($memberCode);
	if(@$ApiResponse->status)
	{
		if($ApiResponse->data->Person->DateOfBirth!="")
		{
			$ApiResponse->data->Person->DateOfBirth = date("Y-m-d",strtotime($ApiResponse->data->Person->DateOfBirth));
		}	
	}
	echo json_encode($ApiResponse);
}