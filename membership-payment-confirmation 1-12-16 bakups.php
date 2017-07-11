<?php

include_once("includes/framework.php");

$data = JRequest::get();
$MerchantRefNo = JRequest::getVar("MerchantRefNo");
$isError = false;
$objBase->setMetaData("membership-payment-confirmation", "membership-payment-confirmation", "membership-payment-confirmation");

if (isset($data['paymentstatus'])) {

    require('Rc43.php');

    $DR = preg_replace("/\s/", "+", $_GET['DR']);

    $rc4 = new Crypt_RC4($objEBSPay->secret_key);

    $QueryString = base64_decode($DR);

    $rc4->decrypt($QueryString);

    $QueryString = explode('&', $QueryString);

    $response = array();

    // foreach ($QueryString as $param) {

    //     $param = explode('=', $param);

    //     $response[$param[0]] = urldecode($param[1]);

    // }

    $response['TransactionID'] = $_GET["txnid"] ;

    $response['PaymentID'] = $response['TransactionID'];

    $response['MerchantRefNo'] = $_GET["bookingid"] ;

    $response['Amount'] = $_GET["amount"] ;

    $response['ResponseMessage'] = $_GET['status'];

    if (is_array($response) && count($response) > 0) {

        $_SESSION['transaction_response'] = $response;
        //to check canclation
        //$objBase->Redirect('membership-payment-canceled.php');

        $data['transaction_response'] = serialize($response);

        $data['cdate'] = time();

        $data['transaction_number'] = $response['TransactionID'];

        $data['member_id'] = $response['MerchantRefNo'];

        $data['amount'] = $response['Amount'];

        $payment_history_id = $objDB->insert_data("#__member_payment_history", $data);

        if ($response['ResponseMessage'] == Enum::HttpStatus()->OK && $response['MerchantRefNo'] > 0 && $response['Amount'] > 0) {

            //to cross check payment details
            if ($objMembers->verify_payment($response['MerchantRefNo'], $response['Amount'])) {

                $affected_rows = $objMembers->approve_member($response['MerchantRefNo'], $response);

                unset($_SESSION['member_register']);

                $_SESSION['membership_TransactionID'] = $response['TransactionID'];

                $_SESSION['membership_card_link'] = $objMembers->print_membership_card($response['MerchantRefNo']);

                $_SESSION['membership_web_message'] = $objMembers->get_membership_web_message($response['MerchantRefNo'], $_SESSION['membership_card_link']);

                $objBase->Redirect('membership-payment-confirmation.php?MerchantRefNo='.$objEncryption->safe_b64encode($response['MerchantRefNo']));

            } else {

                $objBase->Redirect('membership-payment-canceled.php');

            }

        } else {

            $objBase->Redirect('membership-payment-canceled.php');
          }
        } 
        //if no one condition exists
        $isError = true;
    }
    else if(!IsNull($MerchantRefNo)){
        $MerchantRefNo = $objEncryption->safe_b64decode($MerchantRefNo);
        $MemberDetails = $objMembers->getMerchantInfo(@$MerchantRefNo);
       
        if(!empty($MemberDetails) && count($MemberDetails)>0){
        ?>
            <?php include_once(PATH_INCLUDES . "/header.php"); ?>
  <div id="body">
<!-- BODY start -->
 <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo SITE_PATH_WEBROOT;?>/Img/ISTSS-Landing-Page-Membership.jpg");">
     <ul>
      <li><a href="javascript:void(0);">Membership Payment Confirmation</a></li>
    </ul>
  </div>

<div class="container">
   <div class="row">
      <div class="rightCol col-md-9 col-sm-9">
         <div class="full_width destinaion_sorting_section">
            <div class="container">
               <div class="row ">
                  <!-- left sidebar start --> 
                  <!-- left sidebar end --> 
                  <!-- right main start -->
                  <div class="col-lg-12">
                     <div class="tour_package_booking_section">
                        <!-- package tabs start -->
                        <div id="tour_booking_tabs">
                           <!-- tabs start -->
                           <div class="tour_booking_tabs">
                             <ul>
                              <li class="ui-state-complete"><a href="#indiv_member_personal_info">Personal Info</a></li>
                              <li class="ui-state-complete"><a href="#indiv_member_payment_info">Payment Info</a></li>
                              <li class="ui-state-current"><a href="#indiv_member_status_info">Confirmation</a></li>
                            </ul>
                           </div>
                           <div id="indiv_member_status_info" class="main_content_area hotel_main_content">
                            <div class="inner_container"> 
                              <!-- confirmation message -->
                              <div class="full_width confirmation_msg"> <span>Thank you for becoming a member with YHAI.</span> </div>
                              <!-- confirmation message End--> 
                              <!--  tab inner three section Start -->
                              <div class="tab_inner_section hotel_inner_section">
                                <div class="heading_tab_inner">
                               
                                </div>
                                <!--  tab_inner_body Start-->
                                <div class="tab_inner_body full_width">
                                  <div class="payment_details_main"> 

                                <!-- information_table End -->
                               
                                  <div class="paymentinfo_list">
                                     <h3>Membership-Payment-Confirmation</h3>

                            <?php echo  $objMembers->get_membership_web_message($MemberDetails->member_plan_id);?>
                                    

                    <p>&nbsp;</p>

                    
                                    <!-- Review content main -->
                                    <div class=" col-lg-9 col-md-9 review_content" style="border-top:1px solid #d3d3d3 ; margin-top:1%; width:100%;">
                                      
                                      
                                  
                                  </div>
                           <!-- payment_details_main end --> 
                                   <div class="full_width information_section">
                                <div class="information_title">Transaction Summary</div>
                                <div class="full_width information_table_main">
                                <div class="inludes_hotel_booking prognamewidth" style="border:1px solid #d3d3d3; margin-bottom:2%;">
                                                      <div class="left_lists  col-md-6 col-sm-6">
                                                         <table>
                                                            <tr>
                                                               <td class="label_list">Transaction Id :</td>
                                                               
                                                               <td><?php echo $MemberDetails->TransactionID; ?></td>
                                                            </tr>
                                                            <tr >
                                                               <td class="label_list">Transaction Status :</td>
                                                  
                                                               <td>SUCCESS</td>
                                                            </tr>
                                                         </table>
                                                      </div>

                                      <div class="left_lists  col-md-6 col-sm-6"  >
                                                         <div class="table_bold">
                                                            <table>
                                                               <tr>
                                                                  <td class="label_list">Total Transaction :</td>
                                                                  
                                                                  <td class=""><?php echo $MemberDetails->total_amount; ?></td>
                                                               </tr>
                                                               <tr >
                                                                  <td class="label_list">Member Id :</td>
                                                                 
                                                                  <td><?php echo $MemberDetails->member_id; ?></td>
                                                               </tr>
                                                            </table>
                                                         </div>
                                                      </div>
                                                      </div>
                                  
                                </div>
                                <!-- information_table End --> 
                              </div>
                                  <!-- table section main start-->
                                  <div class="full_width package_table_section">

                                    <div class="col-lg-6 col-md-6 border_right" style="border-left: 1px solid #d3d3d3;">
                                  
                 
                                      <div class="payment_table_package">
                                     <table class="table">
                                      <th style="border-top: none;">Personal Details</th>
                                       <tr>
                                          <td>Name :</td>
                                          <td><?php
                                            if(trim($MemberDetails->plan_category)=="Institutional")
                                            {
                                              echo $MemberDetails->organisation_head;
                                            }
                                            else{
                                             echo $MemberDetails->fname;  
                                            }
                                           ?></td>
                                        </tr>
                                        <tr>
                                          <td>Email :</td>
                                          <td><?php echo $MemberDetails->email; ?></td>
                                        </tr>
                                       
                                        <tr>
                                          <td> Date of birth :</td>
                                          <td><?php echo $MemberDetails->dob;  ?></td>
                                        </tr>
                                        <tr>
                                          <td>Address :</td>
                                          <td><?php echo $MemberDetails->address1; ?></td>
                                        </tr>
                                        <tr>
                                          <td>City :</td>
                                          <td><?php echo $objMasters->getCityName($MemberDetails->city); ?></td>
                                        </tr><tr>
                                          <td>State :</td>
                                          <td><?php echo $objMasters->getStateName($MemberDetails->state); ?></td>
                                        </tr>
                                        <tr>
                                          <td>Contact Number :</td>
                                          <td><?php echo $MemberDetails->mobile; ?></td>
                                        </tr>
                                      </table>
                                     
                                      </div>
                                      <!--  Payment Table End --> 
                                    </div>
                                   <div class="col-lg-6 col-md-6 border_right">
                                      <div class="payment_table_package">
                                       <div class="top_head_bar  col-md-6 col-sm-6" style=" float: right;">
                                   <figure  style=" float: right;"> 
                                   <a href="javascript:void(0)?>" class="zoom-item" title="Portfolio Item Title"> 
                                   <img style=" float: right; height:85px;" src="<?php echo WWW_UPLOAD_PATH . '/members/'.($MemberDetails->photograph); ?>" alt=""> </a>
                                   </figure>
                                </div>  
                                     <table class="table">
                                    <th style="border-top: none;" >Membership Details</th>
                                       <tr>
                                          <td>Membership Type :</td>
                                          <td><?php echo $MemberDetails->plan_category; ?></td>
                                        </tr>
                                        <tr>
                                          <td>Membership Number:</td>
                                          <td><?php echo $MemberDetails->membership_number; ?></td>
                                        </tr>
                                       
                                        <tr>
                                          <td> Membership Valid from</td>
                                          <td><?php echo $MemberDetails->valid_from>0? $objMasters->dateFormat($MemberDetails->valid_from,null,"d-M-Y"): "Membership Not Activated" ?> </td>
                                        </tr>
                                        <tr>
                                          <td>Membership Valid to :</td>
                                          <td><?php echo $MemberDetails->valid_to>0?$objMasters->dateFormat($MemberDetails->valid_to,null,"d-M-Y"):"Membership Not Activated" ?></td>
                                        </tr>
                                        <tr>
                                          <td>Total Price :</td>
                                          <td><?php echo $MemberDetails->total_amount; ?></td>
                                        </tr>
                                      </table>
                                      </div>
                                      <!--  Payment Table End --> 
                                    </div>
                                  </div>
                                  <!-- table section main end-->
                                
                                </div>
                                <!--  tab_inner_body end --> 
                              </div>
                              <!--  tab inner three section End --> 
                              <!-- information_section start -->
                             
                              <!-- information_section End --> 
                              
                              <!-- information_section start -->
                             
                                <!-- information_table End --> 
                                <!-- information_table End -->
                               
                              </div>
                              <!-- information_section End --> 
                            </div>
                            <!--  inner container --> 
                          </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div>
</div>
 <!-- BODY end -->
<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>

        <?php }else{?>

                 <?php include_once(PATH_INCLUDES . "/header.php"); ?>
  <div id="body">
<!-- BODY start -->
 <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo SITE_PATH_WEBROOT;?>/Img/ISTSS-Landing-Page-Membership.jpg");">
     <ul>
      <li><a href="javascript:void(0);">Membership Payment Confirmation</a></li>
    </ul>
  </div>
<div class="container">
   <div class="row">
      <div class="rightCol col-md-9 col-sm-9">
         <div class="full_width destinaion_sorting_section">
            <div class="container">
               <div class="row ">
                  <!-- left sidebar start --> 
                  <!-- left sidebar end --> 
                  <!-- right main start -->
                  <div class="col-lg-12">
                     <div class="tour_package_booking_section">
                        <!-- package tabs start -->
                        <div id="tour_booking_tabs">
                           <!-- tabs start -->
                           <div class="tour_booking_tabs">
                             <ul>
                              <li class="ui-state-complete"><a href="#indiv_member_personal_info">Personal Info</a></li>
                              <li class="ui-state-complete"><a href="#indiv_member_payment_info">Payment Info</a></li>
                              <li class="ui-state-current"><a href="#indiv_member_status_info">Confirmation</a></li>
                            </ul>
                           </div>
                           <div id="indiv_member_status_info" class="main_content_area hotel_main_content">
                            <div class="inner_container"> 
                              <!-- confirmation message -->
                            
                              <!-- confirmation message End--> 
                              <!--  tab inner three section Start -->
                              <div class="tab_inner_section hotel_inner_section">
                                <div class="heading_tab_inner">
                               
                                </div>
                                <!--  tab_inner_body Start-->
                                <div class="tab_inner_body full_width">
                                  <div class="payment_details_main "> 

                                <!-- information_table End -->
                               
                                  <div class="paymentinfo_list" style="text-align:center;">
                                    <h3>Membership-Payment-Confirmation</h3>

                   
                   
                                     <h4> No Details Exists.</h4>
                    
                                  
                                    

                    <p>&nbsp;</p>

                    <p><strong>Best Regards,<br />YHAI Team</strong></p>
                    </div>
                     <div class="col-md-12 col-sm-12">
                    <div class="buttonDiv travel_form_element  pull_left responsebtn col-md-6 col-sm-6" style="margin-top: 1%;  width: 20%; float:left;">
               <a href="<?php echo SITE_URL?>/index.php" style="color: #ffffff;"><button class="btn btn-yellow btn-travel Width100PC first " type="button" style=" font-size: 19px;  font-weight: bold;">Go to Home </button></a>
            </div>
            <div class="buttonDiv travel_form_element  pull_left responsebtn col-md-6 col-sm-6" style="margin-top: 1%;float:right;  width: 20%;">
               <a href="<?php echo SITE_URL?>/individual-membership-application.php" style="color: #ffffff;"><button class="btn btn-yellow btn-travel Width100PC " type="button"   style=" font-size: 19px; font-weight: bold;  ">Membership</button></a>
            </div>
            </div>
                   
                                    <!-- Review content main -->
                                    <div class=" col-lg-9 col-md-9 review_content" style="border-top:1px solid #d3d3d3 ; margin-top:1%; width:100%;">
                                     
                                      
                                  
                                  </div>
                             

                                  </div>
                                  </div> 
                                  </div>
                              <!-- information_section End --> 
                            </div>
                            <!--  inner container --> 
                          </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div>
</div>
 <!-- BODY end -->
<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>

        <?php } 
    }
    else{
        $isError = true;
    }
    if($isError){?>

        <?php include_once(PATH_INCLUDES . "/header.php"); ?>
  <div id="body">
<!-- BODY start -->
 <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo SITE_PATH_WEBROOT;?>/Img/ISTSS-Landing-Page-Membership.jpg");">
     <ul>
      <li><a href="javascript:void(0);">Membership Payment Confirmation</a></li>
    </ul>
  </div>

<div class="container">
   <div class="row">
      <div class="rightCol col-md-9 col-sm-9">
         <div class="full_width destinaion_sorting_section">
            <div class="container">
               <div class="row ">
                  <!-- left sidebar start --> 
                  <!-- left sidebar end --> 
                  <!-- right main start -->
                  <div class="col-lg-12">
                     <div class="tour_package_booking_section">
                        <!-- package tabs start -->
                        <div id="tour_booking_tabs">
                           <!-- tabs start -->
                           <div class="tour_booking_tabs">
                             <ul>
                              <li class="ui-state-complete"><a href="#indiv_member_personal_info">Personal Info</a></li>
                              <li class="ui-state-complete"><a href="#indiv_member_payment_info">Payment Info</a></li>
                              <li class="ui-state-current"><a href="#indiv_member_status_info">Confirmation</a></li>
                            </ul>
                           </div>
                           <div id="indiv_member_status_info" class="main_content_area hotel_main_content">
                            <div class="inner_container"> 
                              <!-- confirmation message -->
                           
                              <!-- confirmation message End--> 
                              <!--  tab inner three section Start -->
                              <div class="tab_inner_section hotel_inner_section">
                                <div class="heading_tab_inner">
                               
                                </div>
                                <!--  tab_inner_body Start-->
                                <div class="tab_inner_body full_width">
                                  <div class="payment_details_main"> 

                                <!-- information_table End -->
                               
                                  <div class="paymentinfo_list">
                                   <h3>Membership-Payment-Confirmation</h3>

                    <p> Oops!</p>

                    <p> It seems an error has occured, the page you are trying to reach is not accessible.</p>

                    <p>Error!</p>

                    <p> This error has occured for one of the following reasons :<br />

                        <br />

                        (a) You have used Back/Forward/Refresh button of your Browser.<br />

                        (b) You are accessing some links from History after logging out from the system.<br/>

                        (c) Either you don't have cookies support in your browser or cookies not set.<br />

                        (d) You have exceeded the session time out.

                    </p>
                                    

                    <p>&nbsp;</p>

                    <p><strong>Best Regards,<br />YHAI Team</strong></p>
                                    <!-- Review content main -->
                                    <div class=" col-lg-9 col-md-9 review_content" style="border-top:1px solid #d3d3d3 ; margin-top:1%; width:100%;">
                                      
                                  
                                  </div>
                                  </div>
                                  </div>
                                  </div> 
                                  </div>                           <!--  inner container --> 
                          </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div>
</div>
 <!-- BODY end -->
<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>

    <?php }
    
    ?>

   
   <!-- include js -->
<script type="text/javascript" src="<?php echo SITE_PATH_THEME_JS; ?>/bootstrap-filestyle.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_PATH_WEBROOT; ?>/scripts/programs.js"/></script>
 <script type="text/javascript">
 //add class to file type
 $(":file").filestyle({classInput: "input-small"});
 $( "#tour_booking_tabs" ).tabs({
    disabled: [ 0, 1],
    active: 2
  });
</script>



