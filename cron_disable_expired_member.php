<?php
include_once("includes/framework.php");
$expired_member_plans = $objMembers->disable_expired_member_plans();
if($expired_member_plans){
	$f = @fopen("logs/".date('Ymd')."_expired_member_plans.log", 'a+');
	foreach($expired_member_plans as $expired_member_plan){		
		if ($f) {
			@fputs($f, date("m.d.Y g:ia") . "  expired member plans " . $expired_member_plan->member_plan_id . "\n");
			@fclose($f);
		}
	}
}
?>