<?php

include_once("includes/framework.php");

$page_id = JRequest::getVar('page_id');

$node = JRequest::getVar('node');

$objBase->setMetaData("isic-membership-application", "isic-membership-application", "isic-membership-application");

if (!$node) {

    $node = $objMenu->get_page_node_id();

}

$data = JRequest::get('post');

$err = "";

if ($data) {

    //echo "<pre>"; print_r($data);print_r($_FILES);die;

    if ($data['action'] == 'register') {

        //$data['plan_code'] == "" or 
        //change according to old code as per db table
        if(!IsNull($data['dob']))
        $data['dob'] = $objMasters->dateFormat($data['dob'],null,"Y-m-d");

        if ($data['email'] == "" or $data['dob'] == "" or $data['state'] == "" or $data['mobile'] == "" or $data['gender'] == "") {

            $err = "The submitted form was invalid. Try submitting again";

        } else {



            if ($err == "") {

                $age = $objMembers->calculate_age($data['dob']);

                //echo $age;die;

                if ($age > 11 && $data['prooftype']=="Student ID Card" ) {
                       if ($age  > 12 || $data['prooftype']=="Student ID Card") {

                        $data['plan_code'] = 'ISIC';

                    } 
                   
                    $data['plan_category'] = 'Individual';

                    if ($data['prooftype'] == "Others") {

                        $data['prooftype'] = $data['proofname'];

                    }

                    /*

                     * Upload Documents/Photograph Begin

                     */



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

                    if ($_FILES['proofdoc']['name'] != "") {

                        $handle = new upload($_FILES['proofdoc']);

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

                            $handle->process(UPLOAD_PATH . "/proof/");

                            if ($handle->processed) {

                                //echo 'image resized';

                                $data['proofdoc'] = $handle->file_dst_name;

                                @unlink(UPLOAD_PATH . '/proof/' . $_POST["old_proofdoc"]);

                                $handle->clean();

                            } else {

                                $err .= "Age proof document " . $handle->error . "<br />";

                            }

                        }

                    }

                    /*

                     * Upload Documents/Photograph End

                     */



                    if ($err == "") {

                        $_SESSION['member_register'] = $data;

                        $objBase->Redirect('isic-membership-confirmation.php');

                    }

                } else {

                    $err = "Sorry! Your age limit is Invalid";

                }

            }

        }

        //change according to old code as per db table
        if(!IsNull(@$data['dob']))
        {
            $data['dob'] = $objMasters->dateFormat($data['dob'],"YR-MN-DT","d/m/Y");
        }
        //print "<pre>";

        //print_r($data);

        //print "</pre>";

    }
   
}

$page_info = $objPage->get_page_info($page_id);

$node_info = $objMenu->getInfo($node);

$objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);

$membership_options = $objMembers->list_membership_options($data['plan_code'], 'Individual');

$membership_prices = $objMembers->list_membership_prices('Individual');


//echo "<pre>"; print_r($membership_prices);die;

$state_options = $objMasters->list_state_options($data['state']);

$city_options = $objMasters->list_city_options($data['city'], $data['state'], false);
// set error message
$MSG->ERROR = @$err;
?>

<?php include_once(PATH_INCLUDES . "/header.php"); ?>

<div id="content_wrapper"> 
  <!--page title Start-->
  <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo SITE_PATH_WEBROOT;?>/Img/ISTSS-Landing-Page-Membership.jpg");">
     <ul>
      <li><a href="javascript:void(0);">ISIC Membership</a></li>
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
                  <form class="package_booking_form_main"  id="IytcMemberShip" method="post" action="isic-membership-application.php"  enctype="multipart/form-data" name="RegisterForm">
                    <div class="tab_inner_section inner_section_2">
                      <div class="tab_inner_body full_width"> 
                        <!--  package_booking_form start -->
                            <div class="col-lg-12 col-md-12">
                                <p>Note: All Indian National between 11years to 30 years can apply for this membership.</p>
                            </div>
                           <!--  <div class="col-lg-12 col-md-12">
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
                            </div> -->
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
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="address3"  class="control-label">Landmark</label>
                                    <input type="text" name="address3" id="address3" value="<?php echo $data['address3']; ?>" maxlength="25"   class="form-control"/>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="postal_code"  class="control-label">Postal Code</label>
                                    <input type="text" name="postal_code" id="postal_code" value="<?php echo $data['postal_code']; ?>" maxlength="6"  class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="state"  class="control-label">State</label>
                                    <select name="state" id="state" class="form-control">
                                        <option value="">Select</option>
                                        <?php echo $state_options ?>
                                    </select>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="city"  class="control-label">City</label>
                                     <select name="city" id="city" class="form-control">
                                        <option value="">Select</option>
                                        <?php echo $city_options ?>
                                    </select>
                                </div>
                                
                            </div>
                            <div class="col-lg-12 col-md-12 form-group">
                                <p>Please upload the following documents which are required for membership Card processing, you can also send the same by post to YHAI National Office at Delhi. Photograph is a must for life Membership.</p>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <input type="file" class="filestyle" data-buttonText="Residence Proof"  name="residence_proof" id="residence_proof" accept="image/* application/pdf" data-classInput="input-small" data-classIcon="fa fa-file-text-o"/>
                                    <input type="hidden" name="old_residence_proof" id="old_residence_proof" value="<?php echo $data['residence_proof']; ?>" />
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <input type="file" name="photograph" id="photograph"  class="filestyle" data-buttonText="Photograph" data-classInput="input-small" accept="image/*" data-classIcon="fa fa-picture-o"/>
                                    <input type="hidden" name="old_photograph" id="old_photograph" value="<?php echo $data['photograph']; ?>" />
                                    <small id="photographHelp" class="form-text text-muted" style="float:left">Please upload a passport size photograph. photo size should be upto 512 KB.</small>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 form-group">
                                <div class="col-lg-6 col-md-6 form-group">
                                <label for="city"  class="control-label">Select Prof. Type</label>
                                     <select name="prooftype" id="prooftype" class="form-control">
                                        <option value="">Select</option>
                                        <option value="Voter ID Card" <?php if($data['prooftype']=="Voter ID Card") echo "selected";?>>Voter ID Card</option>
                                        <option value="Student ID Card" <?php if($data['prooftype']=="Student ID Card") echo "selected";?>>Student ID Card</option>
                                        <option value="PAN Card" <?php if($data['prooftype']=="PAN Card") echo "selected";?>>PAN Card</option>
                                        <option value="Others" <?php if($data['prooftype']=="Others") echo "selected";?>>Others</option>
                                    </select>
                                </div>
                                <?php
                                $proofnameDisplay = ($data['prooftype']=="Others")?"block":"none";
                                ?>
                                <div class="col-lg-6 col-md-6">
                                    <div class="col-lg-6 col-md-6 form-group" id="prooftypeotherdiv" style="display:<?php echo $proofnameDisplay;?>">
                                        <label for="proofname"  class="control-label">Enter Prof. Type</label>
                                        <input type="text" required="required=" name="proofname" id="proofname" value="<?php echo $data['proofname']; ?>" maxlength="50"  class="form-control"/>
                                    </div>
                                    <div class="col-lg-6 col-md-6 form-group" >
                                        <input type="file" name="proofdoc" id="proofdoc"  class="filestyle" data-buttonText="Prof. of doc" data-classInput="input-small" accept="image/*" data-classIcon="fa fa-picture-o"/>
                                        <input type="hidden" name="old_proofdoc" id="old_photograph" value="<?php echo $data['proofdoc']; ?>" />
                                        <small id="photographHelp" class="form-text text-muted" style="float:left">Please upload a passport size photograph. photo size should be upto 512 KB.</small>
                                    </div>
                                </div>
                            </div>
                            <div class=" col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label class="checkbox" style="float:left">
                                      <input type="checkbox" required="required" name="terms" id="terms"/> I Accept
                                    </label>
                                    <label class="control-label" style="float:left;height:100%;padding-top:2%;">
                                        <a href="_iytc-terms-membership.php" loader-msg="Please Wait While Getting Term & Conditions....." style="color: blue;" class="hover dialogBox"> &nbsp;Terms & conditions</a>
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
<script type="text/javascript" src="<?php echo SITE_PATH_WEBROOT ?>/scripts/programs.js"/></script>
<?php $maxDate = $objBase->addDayToDate($objMasters->Today(),DATE_FORMAT,null,null,-10);
       $selectedDate = $data['dob']!=null && !empty($data['dob'])?$data['dob']:$maxDate;
?>
<script type="text/javascript">

    /* ]]> */
    //SET DATEOF BIRTH CALENDAR 
    setCalDate("abk_dob",'<?php echo $selectedDate;?>',null,'<?php echo $maxDate;?>');
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

        $(document).on("change","#prooftype",function(){
            if($(this).val()=="Others")
            {
                $("#prooftypeotherdiv").show();
            }else{
                $("#prooftypeotherdiv").hide();
            }
            
        });

        $(document).on("change","#abk_dob",function(){
            var birthDate = jQuery('input[name=dob]').val();
            var birthDateArr = birthDate.split("/");
            var DateJson =  GetDateInJson(birthDate,"DT/MN/YR");
            var age = getAge(DateJson.YR,DateJson.MN, DateJson.DT);
            if (age > 12) {
                var membership_price = <?php echo $membership_prices['ISIC']; ?>;
            } 
            jQuery("#Charges").val(membership_price);
            jQuery(".total_charges_according_to_plans").text("");
            jQuery(".total_charges_according_to_plans").text(membership_price);
        });

        function getAge(year, month, date) {
            var today = new Date();
            var dob = new Date();
            dob.setFullYear(year);
            dob.setMonth(month - 1);
            dob.setDate(date);
            var timeDiff = today.valueOf() - dob.valueOf();
            var milliInDay = 24 * 60 * 60 * 1000;
            var noOfDays = timeDiff / milliInDay;
            var daysInYear = 365.242;
            return  parseInt(noOfDays / daysInYear);
        }
    });

</script>