<?php

include_once("includes/framework.php");

$page_id = JRequest::getVar('page_id');

$node = JRequest::getVar('node');

if (!$node) {

    $node = $objMenu->get_page_node_id();

}

$data = JRequest::get('post');

$err = "";

if ($data) {
    
    if ($data['action'] == 'register') {

        //change according to old code as per db table
        if(!IsNull($data['dob']))
        $data['dob'] = $objMasters->dateFormat($data['dob'],null,"Y-m-d");

        if ($data['plan_code'] == "" or $data['email'] == "" or $data['dob'] == "" or $data['state'] == "" or $data['mobile'] == "") {

            $err = "The submitted form was invalid. Try submitting again";

        } else {

            $age = $objMembers->calculate_age($data['dob']);

            if ($age < 18 && $data['plan_code'] != 'J1') {

                $err = "Sorry! Your age limit is Invalid";

            } else {
                  
                if (($age < 11 or $age > 18) and $data['plan_code'] == 'J1') {

                    $err = "Sorry! Your age limit is Invalid";

                }
                else if($data['plan_code'] == 'L0' && ($_FILES['signature']['name'] == "" || empty($_FILES['signature']))){

                       $err = "For lifetime membership signature is required";
                       
                }
                else {

                    if ($_FILES['photograph']['name'] != "") {

                        $handle = new upload($_FILES['photograph']);

                        if ($handle->uploaded) {

                            //$handle->file_new_name_body   = 'image_resized';

                            //$handle->image_resize = true;

                            //$handle->image_x = 200;

                            //$handle->image_ratio_y = true;

                            $handle->file_safe_name = true;

                            $handle->file_max_size = '524288';  // 512 KB Max

                            $allowed_types = $Config->allowed_image_types;

                            array_push($allowed_types, 'application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/vnd.pdf', 'text/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

                            $handle->allowed = $allowed_types;

                            $handle->process(UPLOAD_PATH . "/members/");

                            if ($handle->processed) {

                                //echo 'image resized';

                                $data['photograph'] = $handle->file_dst_name;

                                @unlink(UPLOAD_PATH . '/members/' . $_POST["old_photograph"]);

                                $handle->clean();

                            } else {

                                $err = "Photograph " . $handle->error . "<br />";

                            }

                        }

                    } 
                        
                    if ($_FILES['signature']['name'] != "" ) {

                        $handle = new upload($_FILES['signature']);

                        if ($handle->uploaded) {

                            //$handle->file_new_name_body   = 'image_resized';

                            //$handle->image_resize = true;

                            //$handle->image_x = 200;

                            //$handle->image_ratio_y = true;

                            $handle->file_safe_name = true;

                            $handle->file_max_size = '524288';  // 512 KB Max

                            $allowed_types = $Config->allowed_image_types;

                            array_push($allowed_types, 'application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/vnd.pdf', 'text/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

                            $handle->allowed = $allowed_types;

                            $handle->process(UPLOAD_PATH . "/signature/");

                            if ($handle->processed) {

                                //echo 'image resized';

                                $data['signature'] = $handle->file_dst_name;

                                @unlink(UPLOAD_PATH . '/signature/' . $_POST["old_signature"]);

                                $handle->clean();

                            } else {

                                $err = "signature " . $handle->error . "<br />";

                            }
                          
                    
                    }
                    }  
                                      

                    if ($_FILES['residence_proof']['name'] != "") {

                        $handle = new upload($_FILES['residence_proof']);

                        if ($handle->uploaded) {

                            //$handle->file_new_name_body   = 'image_resized';

                            //$handle->image_resize = true;

                            //$handle->image_x = 200;

                            //$handle->image_ratio_y = true;

                            $handle->file_safe_name = true;

                            $handle->file_max_size = '524288';  // 512 KB Max

                            $allowed_types = $Config->allowed_image_types;

                            array_push($allowed_types, 'application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/vnd.pdf', 'text/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

                            $handle->allowed = $allowed_types;

                            $handle->process(UPLOAD_PATH . "/residence_proof/");

                            if ($handle->processed) {

                                //echo 'image resized';

                                $data['residence_proof'] = $handle->file_dst_name;

                                @unlink(UPLOAD_PATH . '/residence_proof/' . $_POST["old_residence_proof"]);

                                $handle->clean();

                            } else {

                                $err = "residence_proof " . $handle->error . "<br />";

                            }


                        }

                    }
                   
                    if ($err == "") {

                        $_SESSION['member_register'] = $data;

                        $objBase->Redirect('individual-membership-confirmation.php');

                    }

                }

            }

        }
        
        //change according to old code as per db table
        if(!IsNull(@$data['dob']))
        {
            $data['dob'] = $objMasters->dateFormat($data['dob'],"YR-MN-DT","d/m/Y");
        }

    } else {

        //

    }
// set error message
$MSG->ERROR = @$err;
}

$page_info = $objPage->get_page_info($page_id);

$node_info = $objMenu->getInfo($node);

$objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);

$membership_options = $objMembers->list_membership_options($data['plan_code'],'Individual');

$membership_prices = $objMembers->list_membership_prices('Individual');

$state_options = $objMasters->list_state_options($data['state']);

$city_options = $objMasters->list_city_options($data['city'], $data['state'], false);

?>



<?php include_once(PATH_INCLUDES . "/header.php"); ?>


<div id="content_wrapper"> 
  <!--page title Start-->
  <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo SITE_PATH_WEBROOT;?>/Img/ISTSS-Landing-Page-Membership.jpg");">
    <ul>
      <li><a href="javascript:;">Individual Membership </a></li>
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
                  <li class="ui-state-current"><a href="#indiv_member_personal_info">Personal Info</a></li>
                  <li class="ui-state-pending"><a href="#indiv_member_payment_info">Payment Info</a></li>
                  <li class="ui-state-pending"><a href="#indiv_member_status_info">Confirmation</a></li>
                </ul>
              </div>
              <!-- tabs end --> 
              <!-- personal_info Start -->
              <div id="indiv_member_personal_info" class="main_content_area"> 
                <!--  tab inner section three Start -->
                <div class="inner_container">
                    <!-- Start Message section -->
                  <?php include_once(PATH_INCLUDES."/showMsg.php");?>
                  <!-- End Message Section -->
                  <form class="package_booking_form_main"  id="IndivisulMemberShipForm" method="post" action="individual-membership-application.php"  enctype="multipart/form-data" name="RegisterForm">
                    <div class="tab_inner_section inner_section_2">
                      <div class="tab_inner_body full_width"> 
                        <!--  package_booking_form start -->
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="plan_code" class="control-label">Membership Type</label>
                                    <select name="plan_code" id="plan_code" class="form-control" onchange="load_membership_fee(this.value);">
                                    <option value="">Select</option>
                                    <?php echo $membership_options ?>
                                    
                                    </select>
                                </div>
                                 <div class="col-lg-6 col-md-6 form-group">
                                     <label class="control-label" style="float:left;width:100%;">Total Charges (in Rupees) : &nbsp; <i class="fa fa-inr rupeesicon" aria-hidden="true" style=" color: #ffffff;"></i><span class="total_charges_according_to_plans"><?php echo @$data['Charges'];?></span> </label>
                                    
                                    <small class="form-text text-muted" style="float:left;">(Charges include: Handling + Membership Fee)</small>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="fname"  class="control-label">Email</label>
                                    <input type="text" name="email" id="email"value="<?php echo $data['email']; ?>" class="form-control" maxlength="50" />
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="fname"  class="control-label">Name</label>
                                    <input type="text" name="fname" id="fname" value="<?php echo $data['fname']; ?>" class="form-control" maxlength="25" />
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="phone"  class="control-label">Phone</label>
                                    <input type="text" name="phone" id="phone" value="<?php echo $data['phone']; ?>"  maxlength="15"  class="form-control"/>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="mobile"  class="control-label">Mobile</label>
                                    <input type="text" name="mobile" id="mobile" value="<?php echo $data['mobile']; ?>" maxlength="10"  class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label style="width:100%">Select Gender</label>
                                    <label class="radio-inline">
                                      <input name="gender" id="gender_male" type="radio" class="radiobuton" value="Male" checked="checked">Male
                                    </label>
                                    <label class="radio-inline">
                                      <input  id="gender_female"  name="gender" type="radio"  value="Female" <?php echo ($data['gender'] == 'Female') ? ' checked="checked"' : '';?>/>Female
                                    </label>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="dob"  class="control-label">Date Of Birth</label>
                                    <input  type="text" name="dob" id="abk_dob" value="<?php echo $data['dob']; ?>" readonly="readonly"   class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="address1"  class="control-label">Address Line 1</label>
                                    <input type="text" name="address1" id="address1" value="<?php echo $data['address1']; ?>" maxlength="25"   class="form-control"/>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="address2"  class="control-label">Address Line 2</label>
                                    <input type="text" name="address2" id="address2" value="<?php echo $data['address2']; ?>" maxlength="25"  class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-4 col-md-4 form-group">
                                    <label for="state"  class="control-label">State</label>
                                    <select name="state" id="state" class="form-control">
                                        <option value="">Select</option>
                                        <?php echo $state_options ?>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 form-group">
                                    <label for="city"  class="control-label">City</label>
                                     <select name="city" id="city" class="form-control">
                                        <option value="">Select</option>
                                        <?php echo $city_options ?>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 form-group">
                                    <label for="postal_code"  class="control-label">Postal Code</label>
                                    <input type="text" name="postal_code" id="postal_code" value="<?php echo $data['postal_code']; ?>" maxlength="6"  class="form-control"/>
                                </div>
                            </div>
                            <p></p>
                            <div class="col-lg-12 col-md-12 form-group">
                                <p>Please upload the following documents which are required for membership Card processing, you can also send the same by post to YHAI National Office at Delhi. Photograph is a must for life Membership.</p>
                            </div>
                            
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-4 col-md-4 form-group">
                                    <input type="file"  name="photograph" id="photograph"  class=" filestyle" data-buttonText="Photograph" data-classInput="input-small" data-toggle="popover" title="Popover title" data-content="Default popover" accept="image/*" data-classIcon="fa fa-picture-o"/>
                                    
                                    <input type="hidden" name="old_photograph" id="old_photograph" value="<?php echo $data['photograph']; ?>" />
                              
                                    <small id="photographHelp" data-toggle="popover" data-content="1.asdsad" class="form-text text-muted" style="float:left">Please upload a passport size photograph. photo size should be upto 512 KB.</small>
                                </div>
                                <div> <input type="hidden" name="metadata" id="metadata" /></div>
                                  <!--<div class="col-lg-4 col-md-4 form-group">
                                    <input type="file" class="filestyle" data-buttonText="Signature"  name="signature" id="signature" accept="image/* application/pdf" data-classInput="input-small" data-classIcon="fa fa-file-text-o"/>
                                    <input type="hidden" name="old_signature" id="old_signature" value="<?php echo $data['signature']; ?>" />
                                </div>-->
                                   <div class="col-lg-4 col-md-4  form-group" style="margin-right:-6%;">
                                 <input type="file" name="signature" id="signature" data-buttonText="Signature" data-classInput="input-small" class="browse filestyle" />
                                 <span class="form-field-info" title="Scanned Signature Image"><img src="images/icon_info_16x16.png" border="0" />
                                 <small id="photographHelp" class="form-text text-muted" style="float:left">Please upload a Signature size photograph. photo size should be upto 512 KB</small>
                                 </span>
                                 </div>
                                <div class="col-lg-4 col-md-4 form-group">
                                    <input type="file" class="filestyle" data-buttonText="Residence Proof"  name="residence_proof" id="residence_proof"  data-classInput="input-small" data-classIcon="fa fa-file-text-o"/>
                                    <input type="hidden" name="old_residence_proof" id="old_residence_proof" value="<?php echo $data['residence_proof']; ?>" />
                                </div>
                            </div>
                            <div class=" col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label class="checkbox" style="float:left">
                                      <input type="checkbox" required="required" name="terms" id="terms"/> I Accept
                                    </label>
                                    <label class="control-label" style="float:left;height:100%;padding-top:2%;">
                                        <a href="_terms-membership.php" loader-msg="Please Wait While Getting Term & Conditions....." style="color: blue;" class="hover dialogBox"> &nbsp;Terms & conditions</a>
                                    </label>
                                </div>
                                 <div class="col-lg-6 col-md-6 form-group">
                                     <label class="control-label" style="float:left;width:100%;">Total Charges (in Rupees) : &nbsp; <i class="fa fa-inr rupeesicon" aria-hidden="true" style=" color: #ffffff;"></i><span class="total_charges_according_to_plans"><?php echo @$data['Charges'];?></span> </label>
                                    <small class="form-text text-muted" style="float:left;">(Charges include: Handling + Membership Fee)</small>
                                </div>
                            </div>
                        </div>
                        <!--  package_booking_form END --> 
                      </div>
                      <!--  tab_inner_body end --> 
                    </div>
                    <!--  tab inner three section End --> 
                    <!-- proceed button -->
                    <div class="full_width ">
                        <input type="hidden" name="Charges"  id="Charges" value="<?php echo @$data['Charges']?>">
                        <input type="hidden" name="country" value="IND" />
                        <input type="hidden" name="plan_category" value="Individual" />
                        <input type="hidden" name="action" value="register" />

                      <button type="submit" style="width:100%; margin-top: 2%;" value="proceed to next step" class="btn_green proceed_buttton btns">proceed to next step</button>
                    </div>
                    <!-- proceed button -->
                  </form>
                </div>
                <!--  inner container end --> 
              </div>
              <!-- personal_info End --> 
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
<script type="text/javascript" src="<?php echo SITE_PATH_WEBROOT ?>/Scripts/programs.js"/></script>
<?php $maxDate = $objBase->addDayToDate($objMasters->Today(),DATE_FORMAT,null,null,-10);
       $selectedDate = $data['dob']!=null && !empty($data['dob'])?$data['dob']:$maxDate;
?>
<script type="text/javascript">
  /* ]]> */
    //SET DATEOF BIRTH CALENDAR 
    setCalDate("abk_dob","<?php echo $selectedDate;?>",null,"<?php echo $maxDate;?>");
     /* <![CDATA[ */
    var membership_prices = Array();
    <?php foreach ($membership_prices as $row_price_key => $row_price_val) { ?>
        membership_prices["<?php echo $row_price_key; ?>"] = <?php echo $row_price_val; ?>;

    <?php } ?>

    function load_membership_fee(membership) {

        jQuery("#Charges").val(membership_prices[membership]);
        jQuery(".total_charges_according_to_plans").text("");
        jQuery(".total_charges_according_to_plans").text(membership_prices[membership]);
        
    }

    /* ]]> */
    //add class to file type
    
    $( "#tour_booking_tabs" ).tabs({
        disabled: [ 1, 2 ]
      });
    //as old code
    jQuery(function () {
        $(":file").filestyle({classInput: "input-small"});
        jQuery("select#state").change(function () {

            jQuery.getJSON("select_city_ajax.php", {id: jQuery(this).val()}, function (res_data) {

                var city_options = '';

                for (var i = 0; i < res_data.length; i++) {

                    city_options += '<option value="' + res_data[i].id + '">' + res_data[i].name + '</option>';

                }

                jQuery("#city").html(city_options);

                jQuery('#city option:first').attr('selected', 'selected');

                var city = jQuery("#city").val();

                if (city == 'others') {

                    jQuery("#city_other_row").show();

                } else {

                    jQuery("#city_other").val('');

                    jQuery("#city_other_row").hide();

                }

            })

        })

        jQuery("select#city").change(function () {

            var city = jQuery(this).val();

            if (city == 'others') {

                jQuery("#city_other_row").show();

            } else {

                jQuery("#city_other").val('');

                jQuery("#city_other_row").hide();

            }

        });

        //on change of plan code 
        jQuery("select#plan_code").change(function () {
            
            var plan_code = jQuery(this).val();
          
            if(plan_code.trim()=="L0"){
  
                jQuery("#signature").attr('required','required');

            } else {

                jQuery("#signature").removeAttr("required");

            }
            if(plan_code.trim()=="IYTC")
            {
               var redirectTo = SITE.URL+"iytc-membership-application.php";
                window.location.href  = redirectTo;
            }
             if(plan_code.trim()=="ISIC")
            {
               var redirectTo = SITE.URL+"isic-membership-application.php";
                window.location.href  = redirectTo;
            }
             
        });
         

    });

</script>
<script type="text/javascript">

$(document).ready(function(){

    $('[data-toggle="popover"]').popover({

        placement : 'bottom',

        trigger : 'hover'

    });

});

</script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
