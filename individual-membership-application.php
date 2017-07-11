<?php

include_once("includes/framework.php");
include(PATH_CONTROLLERS."/MasterController.php");

$page_id = JRequest::getVar('page_id');

$node = JRequest::getVar('node');

if (!$node) {

    $node = $objMenu->get_page_node_id();

}

$data = JRequest::get('post');

$err = "";
/*$data =  json_decode();*/



if ($data) {
/*pre($data);
die;*/

    if ($data['action'] == 'register') {

        //change according to old code as per db table
        if(!IsNull($data['DOB']))
        $data['DOB'] = $objMasters->dateFormat($data['DOB'],null,"Y-m-d");

        if ($data['MEMBERSHIP_TYPE'] == "" or $data['EmailAddress'] == "" or $data['DOB'] == "" or $data['StateID'] == "" or $data['Contact'] == "") {

            $err = "The submitted form was invalid. Try submitting again";

        } else {

            $age = $objMembers->calculate_age($data['DOB']);

            if ($age < 18 && $data['MEMBERSHIP_TYPE'] != '1') {

                $err = "Sorry! Your age limit is Invalid";

            } else {
                  
                if (($age < 11 or $age > 18) and $data['MEMBERSHIP_TYPE'] == '1') {

                    $err = "Sorry! Your age limit is Invalid";

                }
                else if($data['MEMBERSHIP_TYPE'] == '71' && ($_FILES['signature']['name'] == "" || empty($_FILES['signature']))){

                       $err = "For lifetime membership signature is required";
                       
                }
                else {
                    $data["files"] = array();
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
                    
                        
                    if ($_FILES['signature']['name'] != "" ) {
                        $data["files"]['signature']  =  $_FILES['signature'];
                    }else{

                    }  
                        

                    }
                   
                    if ($err == "") {

                        $_SESSION['member_register'] = $data;
                       $_SESSION['member_register']['MEMBERSHIP_TYPE']=$data['MEMBERSHIP_TYPE'];
                   
                 
                          $response=$MEMBERSHIP_CONTROLLER ->Post($data);
                       if($response->MemberID != "" ){   
                         
                        $objBase->Redirect('individual-membership-confirmation.php'."?MemberId=".$response->MemberID);
                    /*    ?myNumber=1&myFruit=orange*/

                        }
                     else{
                       $objBase->Redirect('individual-membership-application.php');
                       }


                 }
                 
                }

            

        }
        
        //change according to old code as per db table
        if(!IsNull(@$data['DOB']))
        {
            $data['DOB'] = $objMasters->dateFormat($data['DOB'],"YR-MN-DT","d/m/Y");
        }

    } else {

        //

    }
   // if (!preg_match("/^[a-zA-Z'-]+$/", $First)) { die ("invalid first name");}
// set error message
$MSG->ERROR = @$err;
}

/*$States = $MEMBERSHIP_REPOSITORY->GetMemberMasterData();*/

$page_info = $objPage->get_page_info($page_id);

$node_info = $objMenu->getInfo($node);

$objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);
/*$membership_options = $OBJ_MASTER_CONTROLLER -> MembershipMasterData();*/

$membertype=enum::ParentMembership()->INDIVIDUAL;

$membership_options = $OBJ_MASTER_CONTROLLER->GetMemberShipType($membertype);


/*$membership_prices = $OBJ_MASTER_CONTROLLER->GetMemberShipPrice($data['MEMBERSHIP_TYPE']);
pre($membership_prices);
die;*/

 
$State_options = $OBJ_MASTER_CONTROLLER->GetState(enum::Country()->INDIA);



$city_options = $OBJ_MASTER_CONTROLLER->GetCities($data['CityID'], $data['StateID'], false);


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
                                    <label for="membershipTypeID" class="control-label">Membership Type</label>
                                    <select name="MEMBERSHIP_TYPE" id="MEMBERSHIP_TYPE" class="form-control" >
                                    <option value="">Select</option>
                                    <?php echo $membership_options ?>
                                    
                                    </select>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                     <label class="control-label" style="float:left;width:100%;">Total Charges (in Rupees) : &nbsp; <i class="fa fa-inr rupeesicon" aria-hidden="true"  style=" color: #ffffff;"></i><span class="total_charges_according_to_plans" ><?php echo @$data['Charges'];?> </span> </label>
                                    
                                    <small class="form-text text-muted" style="float:left;">(Charges include: Handling + Membership Fee)</small>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                              <div class="col-lg-6 col-md-6 form-group">
                                    <label for="fname"  class="control-label">Email</label>
                                    <input type="text" name="EmailAddress" id="EmailAddress"value="<?php echo $data['EmailAddress']; ?>" class="form-control" maxlength="50" />
                                </div>
                            </div>

                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="fname"  class="control-label">First Name</label>
                                    <input type="text" name="FirstName" id="FirstName" value="<?php echo $data['FirstName']; ?>" pattern="[a-zA-Z][a-zA-Z ]{2,}" class="form-control" maxlength="50" />
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="fname"  class="control-label">Last Name</label>
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
                                      <input name="GenderId" id="gender_male" type="radio" class="radiobuton" value="1"<?php echo ($data['GenderId'] == Enum::Gender()->MALE)?> checked="checked">Male
                                    </label>
                                    <label class="radio-inline">
                                      <input  id="gender_female"  name="GenderId" type="radio"  value="2" <?php echo ($data['GenderId'] == Enum::Gender()->FEMALE) ? ' checked="checked"' : '';?>/>Female
                                    </label>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="dob"  class="control-label">Date Of Birth</label>
                                    <input  type="text" name="DOB" id="DOB" value="<?php echo $data['DOB']; ?>" readonly="readonly"   class="form-control"/>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="address1"  class="control-label">Address Line 1</label>
                                    <input type="text" name="AddressLineOne" id="AddressLineOne" value="<?php echo $data['address1']; ?>" maxlength="25"   class="form-control"/>
                                </div>
                                <div class="col-lg-6 col-md-6 form-group">
                                    <label for="AddressLineOne"  class="control-label">Address Line 2</label>
                                    <input type="text" name="AddressLineTwo" id="AddressLineTwo" value="<?php echo $data['AddressLineTwo']; ?>" maxlength="25"  class="form-control"/>
                                </div>
                            </div>
                           
                            <div class="col-lg-12 col-md-12">
                                <div class="col-lg-4 col-md-4 form-group">
                                    <label for="state"  class="control-label">State</label>
                                    <select name="StateID" id="StateID" required="required"   class="form-control" >
                                        <option value="">Select</option>
                                        <?php echo $State_options; ?>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 form-group">
                                    <label for="city"  class="control-label">City</label>
                                     <select name="CityID"  required="required"  id="CityID" class="form-control">
                                        <option value="">Select</option>
                                           <?php echo $city_options; ?>
                                    </select>
                                </div>
                                <div class="col-lg-4 col-md-4 form-group">
                                    <label for="PostalCode"  class="control-label">Postal Code</label>
                                    <input type="text" name="PostalCode" id="PostalCode" value="<?php echo $data['PostalCode']; ?>" maxlength="6"  class="form-control"/>
                                </div>
                            </div>
                            <p></p>
                            <div class="col-lg-12 col-md-12 form-group">
                                <p>Please upload the following documents which are required for membership Card processing, you can also send the same by post to YHAI National Office at Delhi. Photograph is a must for life Membership.</p>
                            </div>
                            <div class="col-lg-12 col-md-12">
                               <div class="col-lg-4 col-md-4 form-group">
                              <!--  <a href="_uploadMetaData.php" class="dialogBox"> -->
                               
                               
                                 <input type="file"  name="photograph" id="photograph"  class=" filestyle" data-buttonText="Photograph" data-classInput="input-small" data-toggle="popover" title="Popover title" value="photographID" data-content="Default popover" accept="image/*" data-classIcon="fa fa-picture-o"/>
                                    
                                    <input type="hidden" name="old_photograph" id="old_photograph" value="<?php echo $data['photograph']; ?>" />
                              
                                    <small id="photographHelp" data-toggle="popover" data-content="1.asdsad" class="form-text text-muted" style="float:left">Please upload a passport size photograph. photo size should be upto 512 KB.</small>
                                   
                            <!--  </a> -->
                                </div>
                                  <!--<div class="col-lg-4 col-md-4 form-group">
                                    <input type="file" class="filestyle" data-buttonText="Signature"  name="signature" id="signature" accept="image/* application/pdf" data-classInput="input-small" data-classIcon="fa fa-file-text-o"/>
                                    <input type="hidden" name="old_signature" id="old_signature" value="<?php echo $data['signature']; ?>" />
                                </div>-->
                                   
                                <div class="col-lg-4 col-md-4 form-group">
                                    <input type="file" class="filestyle" data-buttonText="Residence Proof"  name="residence_proof" id="residence_proof"  data-classInput="input-small" data-classIcon="fa fa-file-text-o"/>
                                    <input type="hidden" name="old_residence_proof" id="old_residence_proof" value="<?php echo $data['residence_proof']; ?>" />
                                </div>
                                <div class="col-lg-4 col-md-4  form-group" style="margin-right:-6%;">
                                 <input type="file" name="signature" id="signature" data-buttonText="Signature" data-classInput="input-small" class="browse filestyle" />
                                 <span class="form-field-info" title="Scanned Signature Image"><img src="images/icon_info_16x16.png" border="0" />
                                 <small id="photographHelp" class="form-text text-muted" style="float:left">Please upload a Signature size photograph. photo size should be upto 512 KB</small>
                                 </span>
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
                        <input type="hidden" name="dataCharges"  id="dataCharges" value="">
                        <input type="hidden" name="country" value="IND" />
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
       $selectedDate = $data['DOB']!=null && !empty($data['DOB'])?$data['DOB']:$maxDate;
?>

<script type="text/javascript">
    
    $(function() {
  $('#btnLaunch').click(function() {
    $('#myModal').modal('show');
  });

  $('#btnSave').click(function() {
    var value = $('input').val();
    $('h1').html(value);
    $('#myModal').modal('hide');
  });
});





      /*images upload js ends*/


  /* ]]> */
    //SET DATEOF BIRTH CALENDAR 
    setCalDate("DOB","<?php echo $selectedDate;?>",null,"<?php echo $maxDate;?>");
     /* <![CDATA[ */
    var membership_prices = Array();
    <?php foreach ($membership_prices as $row_price_key => $row_price_val) { ?>
        membership_prices["<?php echo $row_price_key; ?>"] = <?php echo $row_price_val; ?>;

    <?php } ?>

   /* function load_membership_fee(membership) {

        jQuery("#Charges").val(membership_prices[membership]);
        jQuery(".total_charges_according_to_plans").text("");
        jQuery(".total_charges_according_to_plans").text(membership_prices[membership]);
        
    }*/

    /* ]]> */
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
                    AlertMsg(MSG.FAILED,"Ooops! something went wrong!");
                }
            });    
        });
        


        //on change of plan code 
        jQuery("select#MEMBERSHIP_TYPE").change(function () {
            
            $(".total_charges_according_to_plans").html("");
            var MEMBERSHIP_TYPE = jQuery(this).val();
//alert(MEMBERSHIP_TYPE);
             $.ajax({
            url: 'http://43.224.136.220/Membership/api/api/members/membershiptype/charge/'+MEMBERSHIP_TYPE,
            type: 'Get',
            dataType: 'json',
            cache: false,
            async: false,
            success: function (response) {
                    if(!Validation.isNull(response))
                    {
                        //alert(response.ServicePayload.Totalcharges);
                        if(response.StatusCode==200)
                        {
                            //alert(response.ServicePayload.Totalcharges);
                           $("#dataCharges").val(response.ServicePayload.Totalcharges); 
                            $(".total_charges_according_to_plans").html(response.ServicePayload.Totalcharges); 
                        }
                    }
                },
                error: function(response)
                {
                    $("#Charges").val(0);
                    AlertMsg(MSG.FAILED,"Ooops! something went wrong!");
                }
            });  
            if(MEMBERSHIP_TYPE.trim()=="71"){
  
                jQuery("#signature").attr('required','required');

            } else {

                jQuery("#signature").removeAttr("required");

            }
           /* if(MEMBERSHIP_TYPE.trim()=="IYTC")
            {
               var redirectTo = SITE.URL+"iytc-membership-application.php";
                window.location.href  = redirectTo;
            }
             if(MEMBERSHIP_TYPE.trim()=="ISIC")
            {
               var redirectTo = SITE.URL+"isic-membership-application.php";
                window.location.href  = redirectTo;
            }*/
             
        });
         

    });

</script>



