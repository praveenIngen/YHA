<?php

include_once("includes/framework.php");

$data = JRequest::get();

$objBase->setMetaData("membership-payment-confirmation", "membership-payment-confirmation", "membership-payment-confirmation");

    $data = $_SESSION['transaction_response'];
   
    //unset($_SESSION['transaction_response']);
     /* $data = $_SESSION['transaction_response'];*/
    $MerchantRefNo = $data['MerchantRefNo'];
    $MembershipDetail = $MEMBERSHIP_REPOSITORY->Get($MerchantRefNo);

    

  /*  $MemberDetails = $objMembers->getMerchantInfo(@$MerchantRefNo);*/

    if(!empty($MembershipDetail) && count($MembershipDetail)>0){
    /*  pre($MembershipDetail);*/

?>
            <?php include_once(PATH_INCLUDES . "/header.php"); ?>
  <div id="body">
   <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo SITE_PATH_WEBROOT;?>/Img/ISTSS-Landing-Page-Membership.jpg");">
     <ul>
      <li><a href="javascript:void(0);">Membership Payment Confirmation</a></li>
    </ul>
  </div>
<!-- BODY start -->

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
                              <li class="ui-state-canceled"><a href="#indiv_member_payment_info">Payment Info</a></li>
                              <li class="ui-state-canceled"><a href="#indiv_member_status_info">Canceled</a></li>
                            </ul>
                           </div>
                           <div id="indiv_member_status_info" class="main_content_area hotel_main_content">
                            <div class="inner_container"> 
                              <!-- confirmation message -->
                            
                              <!-- confirmation message End--> 
                              <!--  tab inner three section Start -->
                              <div class="tab_inner_section hotel_inner_section">
                                <div class="heading_tab_inner" style="    padding: 0px 30px; color:#ffffff;">
                               <h3>Membership-Payment-Cancellation</h3>   
                                </div>
                                <!--  tab_inner_body Start-->
                                <div class="tab_inner_body full_width">
                                  <div class="payment_details_main"> 

                                <!-- information_table End -->
                               
                                  <div class="paymentinfo_list">
                                    
                             

                <p style="background: #E6C0C0; border: #E1830C; padding: 10px;">

                    Sorry! Your payment was declined for the following reason:<br /><br />

                    Response Message: Your Transaction is Unsuccessfull . Kindly Contact YHAI Administrator. <!-- <?php echo $data['ResponseMessage']?> --><br /><br />

                    Response Code: <?php echo FAILED ;  ?><br /><br />

                    Transaction ID: <?php echo $data['TransactionID']?><br /><br />

                    Payment ID: <?php echo $data['PaymentID']?><br /><br />

                    <!-- <?php unset($_SESSION['transaction_response']);?> -->

                </p>                
                                    

                    <p>&nbsp;</p>

                    
                                    <!-- Review content main -->
                                    <div class=" col-lg-9 col-md-9 review_content" style="border-top:1px solid #d3d3d3 ; margin-top:1%; width:100%;">
                                      
                                      
                                  
                                  </div>
                           <!-- payment_details_main end --> 
                                   <div class="full_width information_section">
                                <div class="information_title">Transaction Summary</div>
                                <div class="full_width information_table_main">
                                <div class="inludes_hotel_booking prognamewidth" style="border-bottom: 1px solid #d3d3d3;margin-bottom:17px;" >
                                                      <div class="left_lists  col-md-6 col-sm-6">
                                                         <table>
                                                         <tr >
                                                               <td class="label_list">Transaction Status :</td>
                                                  
                                                               <td>FAILED</td>
                                                            </tr>
                                                            <tr>
                                                               <td class="label_list">Transaction Id :</td>
                                                               
                                                               <td><?php echo $data["TransactionID"]; ?></td>
                                                            </tr>
                                                            
                                                         </table>
                                                      </div>

                                      <div class="left_lists  col-md-6 col-sm-6"  >
                                                         <div class="table_bold">
                                                            <table>
                                                               <tr>
                                                                  <td class="label_list">Transaction Amount:</td>
                                                                  
                                                                  <td class=""><?php echo $data["Amount"] ?></td>
                                                               </tr>
                                                               <tr >
                                                                  <td class="label_list">Member Id :</td>
                                                                 
                                                                  <td><?php echo $data['MerchantRefNo']; ?></td>
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
                                          <td><?php echo $MembershipDetail->ServicePayload->Person->FirstName; ?></td>
                                        </tr>
                                        <tr>
                                          <td>Email :</td>
                                          <td><?php echo $MembershipDetail->ServicePayload->Person->EmailAddress; ?></td>
                                        </tr>
                                        <tr>
                                          <td>Contact Number :</td>
                                          <td><?php echo $MembershipDetail->ServicePayload->Person->Contact; ?></td>
                                        </tr>
                                      </table>
                                     
                                      </div>
                                      <!--  Payment Table End --> 
                                    </div>
                                   <div class="col-lg-6 col-md-6 border_right">
                                      <div class="payment_table_package">
                                      
                                        
                                     <table class="table">
                                    <th style="border-top: none;" >Membership Details</th>
                                       <tr>
                                          <td>Membership Type :</td>
                                          <td><?php echo  $MembershipDetail->ServicePayload->MembershipTypeName; ?></td>
                                        </tr>
                                        <tr>
                                          <td>Membership Number:</td>
                                          <td>Canceled</td>
                                        </tr>
                                        <tr>
                                          <td>Total Price :</td>
                                          <td><?php echo $MembershipDetail->ServicePayload->MemberShipType->Amount; ?></td>
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
   <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo SITE_PATH_WEBROOT;?>/Img/ISTSS-Landing-Page-Membership.jpg");">
     <ul>
      <li><a href="javascript:void(0);">Membership Payment Confirmation</a></li>
    </ul>
  </div>
<!-- BODY start -->

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
                              <li class="ui-state-complete" id="iytc_member_payment_info"><a href="#iytc_member_payment_info">Details Not Found</a></li>
                             
                            </ul>
                           </div>
                           <div id="iytc_member_payment_info" class="main_content_area hotel_main_content">
                            <div class="inner_container"> 
                              <!-- confirmation message -->
                            
                              <!-- confirmation message End--> 
                              <!--  tab inner three section Start -->
                              <div class="tab_inner_section hotel_inner_section">
                                <div class="heading_tab_inner" style="    padding: 0px 30px; color:#ffffff;">
                                 <h3>Membership-Payment-Confirmation</h3>

                                </div>
                                <!--  tab_inner_body Start-->
                                <div class="tab_inner_body full_width">
                                  <div class="payment_details_main "> 

                                <!-- information_table End -->
                               
                                  <div class="paymentinfo_list" style="text-align:center;">
                                  
                   
                   
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



