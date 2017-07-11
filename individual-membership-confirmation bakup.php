<?php

include_once("includes/framework.php");

$page_id = JRequest::getVar('page_id');

$node = JRequest::getVar('node');

if(!$node){

    $node = $objMenu->get_page_node_id();

}

$data = JRequest::get('post');

if ($data) {
    if ($data['action'] == 'register_confirmed') {

        unset($_SESSION['member_register']['member_plan_id']);

        $plan_amount = $objMembers->get_membership_plan_amount($_SESSION['member_register']['plan_code']);

        $_SESSION['member_register']['total_amount'] = $plan_amount['total_amount']; // inclusive Service tax

        $_SESSION['member_register']['plan_amount'] = $plan_amount['plan_amount']; // exclusive Service tax

        $_SESSION['member_register']['service_tax'] = $plan_amount['service_tax'];

        $_SESSION['member_register']['misc_charges'] = $plan_amount['misc_charges'];

        

        $_SESSION['member_register']['pvc_card_charges'] = $plan_amount['pvc_card_charges'];

        $_SESSION['member_register']['postage_charges'] = $plan_amount['postage_charges'];

        $_SESSION['member_register']['transaction_charges'] = $plan_amount['transaction_charges'];

        

        $_SESSION['member_register']['plan_breakup'] = $objMembershipPlans->get_serialized_paln_breakup($_SESSION['member_register']['plan_code']);

        if($_SESSION['member_register']['city_other']!="" && $_SESSION['member_register']['city']=="others"){

            $_SESSION['member_register']['city'] = $objMasters->saveOtherCityOption($_SESSION['member_register']['city_other'],$_SESSION['member_register']['state']);

        }

        $member_plan_id = $objMembers->save_register($_SESSION['member_register']);

        if ($member_plan_id) {

            $_SESSION['member_register']['member_plan_id'] = $member_plan_id; // Temporary use

            $objEBSPay->secure_form_post($_SESSION['member_register']);            

            unset($_SESSION['member_register']);

            exit;

        }

    }

}

$page_info = $objPage->get_page_info($page_id);

$node_info = $objMenu->getInfo($node);

$objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);

?>
<?php include_once(PATH_INCLUDES . "/header.php"); ?>

<div id="content_wrapper"> 
  <!--page title Start-->
  <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo SITE_PATH_WEBROOT;?>/Img/ISTSS-Landing-Page-Membership.jpg");">
    <ul>
      <li><a href="javascript:;">Individual MemberShip</a></li>
    </ul>
  </div>
  <!--page title end-->
  <div class="clearfix"></div>
  <div class="full_width destinaion_sorting_section">
    <div class="container">
      <div class="row"> 
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
                  <li class="ui-state-current"><a href="#indiv_member_payment_info">Payment Info</a></li>
                  <li class="ui-state-pending"><a href="#indiv_member_status_info">Confirmation</a></li>
                </ul>
              </div>
              <!-- tabs end --> 
              <!-- payment_info Start -->
              <div id="indiv_member_payment_info" class="main_content_area"> 
                <!-- inner_container Start -->
                <div class="inner_container"> 
                    <!-- Start Message section -->
                    <?php include_once(PATH_INCLUDES."/showMsg.php");?>
                    <!-- End Message Section -->
                  <!--  tab inner three section Start -->
                  <div class="tab_inner_section">
                    <div class="heading_tab_inner">
                      <h5 style="float:left;">payment Details</h5>
                      <h5 style="float:right;"><i class="fa fa-inr rupeesicon" aria-hidden="true" ></i>&nbsp; <?php echo $_SESSION['member_register']['Charges']; ?></h5>
                    </div>
                    <!--  tab_inner_body Start-->
                    <div class="tab_inner_body full_width">
                      <div class="payment_details_main"> 
                        <div class="col-lg-12 col-md-12 col-sm-12">
                          <div class="tour_packages_right_section left_space_40">
                             <div class="tour_packages_details_top row">
                               <div class="col-md-6 col-sm-6 prognamewidth">
                                <table class="table">
                                    <tr>
                                      <td>Membership Type :</td>
                                      <td>Individual Membership</td>
                                    </tr>
                                    <tr>
                                      <td>Name :</td>
                                      <td><?php echo $_SESSION['member_register']['fname'];?></td>
                                    </tr>
                                    </table>
                                 
                                </div>
                                 <div class="top_head_bar  col-md-6 col-sm-6" style=" float: right;">
                                   <figure  style="margin-bottom: 15px; float: right;"> 
                                   <a href="javascript:void(0)?>" class="zoom-item" title="Portfolio Item Title"> 
                                   <img style=" float: right; height:115px;" src="<?php echo WWW_UPLOAD_PATH . '/members/'.$_SESSION['member_register']['photograph']; ?>" alt=""> </a>
                                   </figure>
                                </div>
                                
                                <!-- total row Start-->
                             </div>
                          </div>
                       </div>
                        <!-- information_section start -->
                          <div class="full_width information_section">
                            <div class="full_width information_table_main">
                              <div class="col-lg-6 col-md-6 border_right">
                                <div class="payment_table_package">
                                  <table class="table">
                                
                                   
                                       <tr>
                                        <td>Gender :</td>
                                        <td><?php echo $_SESSION['member_register']['gender'];?></td>
                                    </tr>
                                    <tr>
                                      <td>Mobile :</td>
                                      <td><?php echo $_SESSION['member_register']['mobile'];?></td>
                                    </tr>
                                    <tr>
                                      <td>Date of Birth :</td>
                                      <td><?php echo  $objMasters->dateFormat($_SESSION['member_register']['dob'],"YR-MN-DT","d-m-Y");?></td>
                                    </tr>
                                  </table>
                                </div>
                                <!--  Payment Table End --> 
                              </div>
                              <div class="col-lg-6 col-md-6 border_right">
                                <div class="payment_table_package">
                                  <table class="table">
                                    <tr>
                                      <td>Email :</td>
                                      <td><?php echo $_SESSION['member_register']['email'];?></td>
                                    </tr>
                                    <tr>
                                      <td>Address :</td>
                                      <td><?php echo $_SESSION['member_register']['address1'];?></td>
                                    </tr>
                                    <tr>
                                      <td>Postal Code : </td>
                                      <td><?php echo $_SESSION['member_register']['postal_code'];?></td>
                                    </tr>
                                  </table>
                                </div> 
                                <!--  Payment Table End --> 
                              </div>
                            </div>
                            <!-- information_table End --> 
                          </div>
                          </div>
                          <!-- information_section End -->
                          </div>
                          <form name="frm_register" id="frm_register" method="post" action="" enctype="multipart/form-data">  
                            <input type="hidden" name="action" value="register_confirmed" />
                            <!-- proceed button -->
                            <div class="full_width ">

                                <input type="hidden" name="country" value="IND" />
                                <input type="hidden" name="plan_category" value="Individual" />
                                <input type="hidden" name="action" value="register_confirmed" />

                              <button type="submit" style="width:100%; font-size:175%; margin-top: 2%;" value="proceed to next step" class="btn_green proceed_buttton btns">proceed to pay <i class="fa fa-inr rupeesicon" aria-hidden="true" ></i>&nbsp; <?php echo $_SESSION['member_register']['Charges'];?></button>
                            </div>
                            <!-- proceed button -->
                        </form>  
                      
                      <!-- payment_details_main end --> 
                    
                    <!--  tab_inner_body end --> 
                  </div>
                  <!--  tab inner three section End --> 
                </div>
                <!-- inner_container end --> 
              </div>
              <!-- payment info End --> 
            </div>
            <!-- package tabs End --> 
          </div>
          <!-- right main start --> 
        </div>
        <!-- col-lg-9-end --> 
      </div>
      <!--  row main --> 
    </div>
    <!-- container --> 
  </div>
</div>
<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>
<!-- include js -->
<script type="text/javascript" src="<?php echo SITE_PATH_THEME_JS ?>/bootstrap-filestyle.min.js"/></script>
<script type="text/javascript" src="<?php echo SITE_PATH_WEBROOT ?>/scripts/programs.js"/></script>
<script type="text/javascript">
     $(":file").filestyle({classInput: "input-small"});
    $( "#tour_booking_tabs" ).tabs({
        disabled: [ 0, 2 ],
        active: 1
      });
</script>