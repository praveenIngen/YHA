<?php

include_once("includes/framework.php");
include(PATH_CONTROLLERS."/MasterController.php");

$page_id = JRequest::getVar('page_id');

$node = JRequest::getVar('node');

$objBase->setMetaData("iytc-membership-application", "iytc-membership-application", "iytc-membership-application");

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
        if(!IsNull($data['DOB']))
        $data['DOB'] = $objMasters->dateFormat($data['DOB'],null,"Y-m-d");

        if ($data['EmailAddress'] == "" or $data['DOB'] == "" or $data['StateID'] == "" or $data['Contact'] == "" or $data['GenderId'] == "") {

            $err = "The submitted form was invalid. Try submitting again";

        } else {



            if ($err == "") {

                $age = $objMembers->calculate_age($data['DOB']);

                //echo $age;die;
             if ($age > 11 && $age < 30){
                if ($age > 11 && $age < 18) {
                       $data['MEMBERSHIP_TYPE'] = '76';
                   

                    } else {

                    

                    $data['MEMBERSHIP_TYPE'] = '77';
                }

                    if ($data['prooftype'] == "Others") {

                        $data['prooftype'] = $data['proofname'];

                    }

                    /*

                     * Upload Documents/Photograph Begin

                     */



                    if ($_FILES['photograph']['name'] != "") {
                        $data["files"]['photograph']  =  $_FILES['photograph'];
                            } else {

                                $err = "Photograph is required<br />";

                            }


                    if ($_FILES['residence_proof']['name'] != "") {
  
                       $data["files"]['residence_proof']  =  $_FILES['residence_proof'];
                        } else {

                            $err = "residence_proof is required <br />";

                        }
                    if ($_FILES['signature']['name'] != "") {

                        $data["files"]['signature']  =  $_FILES['signature'];
                    }else{

                    }           /*

                     * Upload Documents/Photograph End

                     */



                 }

                        $_SESSION['member_register'] = $data;
                         
                          $response=$MEMBERSHIP_CONTROLLER ->Post($data);
                            if($response->MemberID != "" ){   
                         
                        $objBase->Redirect('iytc-membership-confirmation.php'."?MemberId=".$response->MemberID);
                    /*    ?myNumber=1&myFruit=orange*/

                        }
                     else{
                       $objBase->Redirect('iytc-membership-application.php');
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

$membertype=enum::ParentMembership()->IYTC;

$membership_options = $OBJ_MASTER_CONTROLLER->GetMemberShipType($membertype);

//$membership_prices = $OBJ_MASTER_CONTROLLER->GetMemberShipPrice($data['membershipTypeID'],$membertype);
/*pre($membership_prices);
die;
*/ 
 
$State_options = $OBJ_MASTER_CONTROLLER->GetState(enum::Country()->INDIA);



$city_options = $OBJ_MASTER_CONTROLLER->GetCities($data['CityID'], $data['StateID'], false);


// set error message
$MSG->ERROR = @$err;
?>

<?php include_once(PATH_INCLUDES . "/header.php"); ?>

<div id="content_wrapper"> 
  <!--page title Start-->
  <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo SITE_PATH_WEBROOT;?>/Img/ISTSS-Landing-Page-Membership.jpg");">
     <ul>
      <li><a href="javascript:void(0);">IYTC Membership</a></li>
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
                  <form class="package_booking_form_main"  id="IytcMemberShip" method="post" action="iytc-membership-application.php"  enctype="multipart/form-data" name="RegisterForm">
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
                                    <input type="text" name="EmailAddress" id="EmailAddress" value="<?php echo $data['EmailAddress']; ?>" class="form-control" maxlength="50" />
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="fname"  class="control-label">FirstName</label>
                                    <input type="text" name="FirstName" id="FirstName" value="<?php echo $data['FirstName']; ?>" pattern="[a-zA-Z][a-zA-Z ]{3,}" class="form-control" maxlength="50" />
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="fname"  class="control-label">LastName</label>
                                    <input type="text" name="LastName" id="LastName" value="<?php echo $data['LastName']; ?>" pattern="[a-zA-Z][a-zA-Z ]{2,}" class="form-control" maxlength="25" />
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="phone"  class="control-label">Phone</label>
                                    <input type="text" name="AltContact" id="AltContact" value="<?php echo $data['AltContact']; ?>"  maxlength="15"  class="form-control"/>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="mobile"  class="control-label">Mobile</label>
                                    <input type="text" name="Contact" id="Contact" value="<?php echo $data['Contact']; ?>" maxlength="10"  class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label style="width:100%">Select Gender</label>
                                    <label class="radio-inline">
                                      <input name="GenderId" id="gender_male" type="radio" class="radiobuton" value="1" checked="checked">Male
                                    </label>
                                    <label class="radio-inline">
                                      <input  id="gender_female"  name="GenderId" type="radio"  value="2" <?php echo ($data['GenderId'] == 'Female') ? ' checked="checked"' : '';?>/>Female
                                    </label>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="dob"  class="control-label">Date Of Birth</label>
                                    <input  type="text" name="DOB" id="abk_dob" value="<?php echo $data['DOB']; ?>" readonly="readonly"   class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="address1"  class="control-label">Address Line 1</label>
                                    <input type="text" name="AddressLineOne" id="AddressLineOne" value="<?php echo $data['AddressLineOne']; ?>" maxlength="25"   class="form-control"/>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="address2"  class="control-label">Address Line 2</label>
                                    <input type="text" name="AddressLineTwo" id="AddressLineTwo" value="<?php echo $data['AddressLineTwo']; ?>" maxlength="25"  class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-4 col-md-4 form-group">
                                    <label for="state"  class="control-label">State</label>
                                    <select name="StateID" id="StateID" class="form-control">
                                        <option value="">Select</option>
                                        <?php echo $State_options ?>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 form-group">
                                    <label for="city"  class="control-label">City</label>
                                     <select name="CityID" id="CityID" class="form-control">
                                        <option value="">Select</option>
                                        <?php echo $city_options ?>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 form-group">
                                    <label for="postal_code"  class="control-label">Postal Code</label>
                                    <input type="text" name="PostalCode" id="PostalCode" value="<?php echo $data['PostalCode']; ?>" maxlength="6"  class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12 form-group">
                                <p>Please upload the following documents which are required for membership Card processing, you can also send the same by post to YHAI National Office at Delhi. Photograph is a must for life Membership.</p>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                
                                <div class="col-lg-6 col-md-6 form-group">
                                    <input type="file" name="photograph" id="photograph"  class="filestyle" data-buttonText="Photograph" data-classInput="input-small" accept="image/*" data-classIcon="fa fa-picture-o"/>
                                    <input type="hidden" name="old_photograph" id="old_photograph" value="<?php echo $data['photograph']; ?>" />
                                    <small id="photographHelp" class="form-text text-muted" style="float:left">Please upload a passport size photograph. photo size should be upto 512 KB.</small>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <input type="file" class="filestyle" data-buttonText="Residence Proof"  name="residence_proof" id="residence_proof" accept="image/* application/pdf" data-classInput="input-small" data-classIcon="fa fa-file-text-o"/>
                                    <input type="hidden" name="old_residence_proof" id="old_residence_proof" value="<?php echo $data['residence_proof']; ?>" />
                                </div>

                            </div>
                            <div class="col-lg-12 col-md-12 form-group">
                                <div class="col-lg-6 col-md-6 form-group">
                                <label for="city"  class="control-label">Select Prof. Type</label>
                                     <select name="signatureSelection" id="signatureSelection" class="form-control">
                                        <option value="">Select</option>
                                        <option value="Voter ID Card" <?php if($data['signatureSelection']=="Voter ID Card") echo "selected";?>>Voter ID Card</option>
                                        <option value="Student ID Card" <?php if($data['signatureSelection']=="Student ID Card") echo "selected";?>>Student ID Card</option>
                                        <option value="PAN Card" <?php if($data['signatureSelection']=="PAN Card") echo "selected";?>>PAN Card</option>
                                        <option value="Others" <?php if($data['signatureSelection']=="Others") echo "selected";?>>Others</option>
                                    </select>
                                </div>
                               <!--  <?php
                                $proofnameDisplay = ($data['signatureSelection']=="Others")?"block":"none";
                                ?> -->
                                <div class="col-lg-6 col-md-6">
                                    <!--<div class="col-lg-6 col-md-6 form-group" id="prooftypeotherdiv" style="display:<?php echo $proofnameDisplay;?>">
                                        <label for="proofname"  class="control-label">Enter Prof. Type</label>
                                        <input type="text" required="required=" name="proofname" id="proofname" value="<?php echo $data['proofname']; ?>" maxlength="50"  class="form-control"/>
                                    </div>-->
                                    <div class="col-lg-6 col-md-6 form-group" >
                                        <input type="file" name="signature" id="signature"  class="filestyle" data-buttonText="Prof. of doc" data-classInput="input-small" accept="image/*" data-classIcon="fa fa-picture-o"/>
                                        <input type="hidden" name="old_proofdoc" id="old_photograph" value="<?php echo $data['signature']; ?>" />
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
                                     <label class="control-label" style="float:left;width:100%;">Total Charges (in Rupees) : &nbsp; <i class="fa fa-inr rupeesicon" aria-hidden="true" style=" color: #ffffff;"></i><span class="total_charges_according_to_plans"><?php echo @$data['dataCharges'];?></span> </label>
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
                        <input type="hidden" name="dataCharges"  id="dataCharges" value="">
                        <input type="hidden" name="country" value="" />

                        <input type="hidden" name="MEMBERSHIP_TYPE" id="MEMBERSHIP_TYPE" value="" />

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
        jQuery("select#StateID").change(function () {
            var stateId = $(this).val();
            $.ajax({
            url: 'select_city_ajax.php?id='+stateId,
            type: 'Post',
            dataType: 'json',
            cache: false,
            async: false,
            success: function (response) {
                    $("#CityID").html("<option value=''>Select city</option>");
                    if(!Validation.isNull(response))
                    {
                        if(response.status==STATUS.SUCESS)
                        {
                           $("#CityID").html(response.data); 
                        }else{
                            $("#CityID").html("<option value=''>Select city</option>");
                        }
                    }
                },
                error: function(response)
                {
                    $("#CityID").html("<option value=''>Select city</option>");
                    //AlertMsg(MSG.FAILED,"Ooops! something went wrong!");
                }
            });    
    
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
            
            var birthDate = jQuery('input[name=DOB]').val();
            var birthDateArr = birthDate.split("/");
            var DateJson =  GetDateInJson(birthDate,"DT/MN/YR");
            var age = getAge(DateJson.YR,DateJson.MN, DateJson.DT);
          
            if (age >=11 && age <= 18) {
              
                $.ajax({

            url: 'http://43.224.136.220/Membership/api/api/members/membershiptype/charge/'+76,
            type: 'Get',
            dataType: 'json',
            cache: false,
            async: false,
            success: function (response) {
                    if(!Validation.isNull(response))
                    {
                  
                       // alert(response.ServicePayload.Totalcharges);
                        if(response.StatusCode==200)
                        {
                            $("#MEMBERSHIP_TYPE").val(76);
                           // alert(response.ServicePayload.Totalcharges);
                           $("#dataCharges").val(response.ServicePayload.Totalcharges); 
                            $(".total_charges_according_to_plans").html(response.ServicePayload.Totalcharges); 
                        }
                    }
                },
                error: function(response)
                {
                    $("#Charges").val(0);
                    //AlertMsg(MSG.FAILED,"Ooops! something went wrong!");
                }
            });  
            } else if(age > 18) {
            
                 $.ajax({
            url: 'http://43.224.136.220/Membership/api/api/members/membershiptype/charge/'+77,
            type: 'Get',
            dataType: 'json',
            cache: false,
            async: false,
            success: function (response) {
                    if(!Validation.isNull(response))
                    {
                       // alert(response.ServicePayload.Totalcharges);
                        if(response.StatusCode==200)
                        {
                          
                       $("#MEMBERSHIP_TYPE").val(77)
                            //alert(response.ServicePayload.Totalcharges);
                           $("#dataCharges").val(response.ServicePayload.Totalcharges); 
                            $(".total_charges_according_to_plans").html(response.ServicePayload.Totalcharges); 
                        }
                    }
                },
                error: function(response)
                {
                    $("#Charges").val(0);
                    //AlertMsg(MSG.FAILED,"Ooops! something went wrong!");
                }
            });  
        }
            
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