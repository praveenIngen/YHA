<?php

include_once("includes/framework.php");
$data = JRequest::get('post');

$err = "";

if ($data) {
	$isValid = false;
	$whr = '';
	if(IsNull($data['membership_number']))
       {
       	$err = "<li>Please enter membership number</li>";
       } 
	if(!IsNull($data['dob']))
	{
		$isValid = true;
		$data['dob'] = $objMasters->dateFormat($data['dob'],null,"Y-m-d");
		$whr = $whr." AND mp.dob = '".$data['dob']."'";
	}
    if(!IsNull($data['Transaction_ID']))
    {
    	$isValid = true;
    	$data['Transaction_ID'] = $data['Transaction_ID'];
    	$whr = $whr." AND mp.TransactionID = '".$data['Transaction_ID']."'";
    }
    if($isValid == false){
      $err = "<li>Either Date of birth or Transaction id is mandatory</li>"
    }
    //$return_url = "membership-payment-confirmation.php";
	if($isValid  && $err=='')
	{
		
		$memberDetails = $objMembers->getMemberShipNumberadmitInfo($data['membership_number'],$whr);
		if($memberDetails!=null && !empty($memberDetails))
		{
		$objBase->Redirect('membership-payment-confirmation.php?MerchantRefNo='.$objEncryption->safe_b64encode($memberDetails->member_plan_id));	
		}else{
			$err = "<li>Sorry Your detials not valid</li>";
		}
		//$objBase->Redirect('membership-payment-confirmation.php');
	 
	 
	
}
$MSG->ERROR = @$err;
}
?>

<?php include_once(PATH_INCLUDES . "/header.php"); ?>
<div id="body">
   <!-- BODY start -->
    <!--page title Start-->
  <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo STATIC_HEADER_IMG;?>);">
      <ul>
        <li><a href="javascript:;">Incomplete Membership Payment</a></li>
      </ul>
    </div>
    <!--page title end-->
  
   
   <div class="container">
   <div class="row">
      <div class="col-md-3 col-sm-3">
      <?php include_once(PATH_INCLUDES . "/left-side-new.php"); ?>
      </div>
      <div class="col-md-9 col-sm-9" >
          <div class="sorting_places_wrap  list_sorting_view" style="border:1px solid #d3d3d3; margin-top:3.5%; padding:2%;">
          
              <p style="margin-left:2%;">Please Enter Your Membership Number</br>
               (as written in Membership card).</p>
                <!-- Start Message section -->
                <?php include_once(PATH_INCLUDES."/showMsg.php");?>
                <!-- End Message Section -->
                <div class="canceldivStyle container" style="width:95%;">
                <?php

                ?>
          
                  <form name="MembershipPaymentForm" style="margin-top:2%;" id="MembershipPaymentForm" method="post" action="" enctype="multipart/form-data" class="RegisterForm ">
                     <div class="col-lg-12 col-md-12 form-group">
                          <label for="plan_code" class="control-label">Membership Number </label>
                          <input type="text" required="required" name="membership_number" id="membership_number" value="" class="form-control" />
                      </div>

                      <div class="col-lg-12 col-md-12 form-group">
                      </div>
                       <div class="col-lg-12 col-md-12 form-group">
                            <label for="plan_code" class="control-label">Transaction No  </label>
                            <input type="text" required="required" name="Transaction_ID" id="Transaction_ID" value="<?php echo $data['Transaction_ID']; ?>" class="form-control" />
                        </div>
                        <div class="col-lg-12 col-md-12 form-group">
                            <label for="dob"  class="control-label">Date Of Birth</label>
                            <input  type="text" name="dob" required="required" id="abk_dob" value="<?php echo $data['dob']; ?>" readonly="readonly"   class="form-control"/>
                        </div>
                      <div class="col-lg-12 col-md-12 form-group">
                          <input type="submit" name="action" value="Continue" class="addbtn btn btn-yellow btn-travel Width100PC" />
                      </div>
                    
                 </form>
                
                </div>
          </div>
      </div>
      
   </div>
   </div>
    
      <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>
      <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>
      <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" />
      <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>

</div>
<!-- BODY end -->

<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>

