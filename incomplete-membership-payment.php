<?php

include_once("includes/framework.php");
$data = JRequest::get('post');

$err = "";

if ($data) {
    
    $account_id = "5912";

    $secret_key = "285502663d98b50411efe74b771dfd1a";

    $mode = "LIVE";

    $return_url = "membership-payment-confirmation.php";

  if($data['membership_number']!=null && $data['membership_number'] != "" &&  !empty($data['membership_number']) && $data['action']=="Continue")
  {
    $membership_number = $data['membership_number'];
    $memberDetails = $objMembers->getMemberShipNumberInfo($membership_number,null);
    if($memberDetails!=null && !empty($memberDetails))
    {
      if($memberDetails->TransactionID=='asdsd' || $memberDetails->TransactionID=='')
      {
        $data = (array)$memberDetails;
        $return_url = SITE_URL . "incomplete-membership-payment-confirmation.php";

        $hash = $secret_key . "|" . $account_id . "|" . $data['amount'] . "|" . $data['member_plan_id'] . "|" . $return_url . "|" . $mode;

        $secure_hash = md5($hash);
        echo("<script>console.log('PHP: ". $data['organisation_head']."');</script>");
        ?>

        <p align="center">Redirecting to Payment Gateway.... Please wait!!! Do not Press BackButton / Refresh Please note that the online payment made is not from the YHAI server and that you are going to a non-YHAI site. YHAI is not responsible for, shall have no liability for and disclaims all warranties whatsoever, expressed or implied, related to the site, including without limitation any warranties related to performance, security, stability, or non-infringement of title of the site (including site content) or any controls downloaded from the site. </p>
        <?php echo("<script>console.log('PHP: ". $data['organisation_head']."');</script>");?>

        <form  method="post" action="<?php echo Enum::SubmitForms()->EBS_POST; ?>" name="frmTransaction" id="frmTransaction" onSubmit="return validate()">                                                         
 
       <!--  <input name="organisation_head " type="hidden" value="<?php echo $data['organisation_head']?>"/>-->
            <!--<input name="account_id" type="hidden" value="<?php echo $account_id; ?>" />-->

            <input name="returnUrl" type="hidden" size="60" value="<?php echo $return_url; ?>" />

            <!--<input name="mode" type="hidden" size="60" value="<?php echo $mode; ?>" />-->

            <input name="bookingId" type="hidden" value="<?php echo $data['member_plan_id'] ?>" />

            <input name="bookingType" type="hidden" value="M"/>

            <input name="amount" type="hidden" value="<?php echo $data['total_amount'] ?>"/>

            <?php 
            if(isset($data['organisation_head'])){?>
                <input name="firstName" type="hidden" value="<?php echo $data['fname'] ?>"/>
                <input name="lastName" type="hidden" value="<?php echo $data['fname'] ?>"/>
            <?php } else { ?>
                <input name="firstName" type="hidden" value="<?php echo $data['fname'] ?>"/>
                <input name="lastName" type="hidden" value="<?php echo $data['fname'] ?>"/>
            <?php }
            ?>
            <input name="description" type="hidden" value="<?php echo $data['plan_code'] ?>" /> 
            

<!--<input name="name" type="hidden" maxlength="255" value="<?php echo ($data['fname']) ? $data['fname'] : $data['organisation'] ?>" />

     <!--  <input name="address" type="hidden" maxlength="255" value="<?php echo $data['address1'] ?>" />

        <input name="city" type="hidden" maxlength="255" value="<?php echo $data['city'] ?>" />

            <input name="state" type="hidden" maxlength="255" value="<?php echo $data['state'] ?>" />

            <input name="postal_code" type="hidden" maxlength="255" value="<?php echo $data['postal_code'] ?>" />

            <input name="country" type="hidden" maxlength="255" value="<?php echo $data['country'] ?>" />-->

            <input name="contactNumber" type="hidden" maxlength="255" value="<?php echo $data['mobile'] ?>" />
            <input name="sourceid" type="hidden" value="9"/>
            <input name="email" type="hidden" size="60" value="<?php echo $data['email'] ?>" />

            <!--<input name="secure_hash" type="hidden" size="60" value="<?php echo $secure_hash; ?>" />-->

            <input name="submitted" value="Submit" type="hidden" />

        </form>

        <script type="text/javascript">

            /* <![CDATA[ */

              document.frmTransaction.submit();

            /* <![CDATA[ */

        </script>

        <?php

    





      }else if($memberDetails->status==1 && $memberDetails->transactionId!='asdsd'){
        $err = "Your Payment have already done.";  
      }
      else{
       $err = "Your Payment have already done. Member not activated";   
      }
    }else{
      $err = "MemberShip Number not found.";  
    }
  }else{
    $err = "The submitted form was invalid. Try submitting again.";
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
          
                  <form name="MembershipPaymentForm" style="margin-top:2%;" id="MembershipPaymentForm" method="post" action="incomplete-membership-payment.php" enctype="multipart/form-data" class="RegisterForm ">
                     <div class="col-lg-12 col-md-12 form-group">
                          <label for="plan_code" class="control-label">Membership Number </label>
                          <input type="text" required="required" name="membership_number" id="member_id" value="" class="form-control" />
                      </div>
                      <div class="col-lg-12 col-md-12 form-group">
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

<html><head></head><body></body></html>