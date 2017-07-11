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
    if ($data['action'] == 'submit_enquiry') {
        if ($data['fname'] == "" or $data['lname'] == "" or $data['email'] == "" or $data['city'] == "" or $data['mobile'] == "") {
            $err = "Invalid Form Submitted, Try Again.";
        } else {
            if (false){//$data['security_code'] != $_SESSION['security_code']) {
                $err = "Invalid security code provided, Try Again.";
            } else {
                $data['cdate'] = time();
                $enquiry_id = $objDB->insert_data("#__enquiry_form", $data);
                $objBase->send_enquiry_acknowledgement($data);
                $objBase->send_enquiry_alert($data);
                $MSG->SUCESS = "Thanks for contacting YHAI. We will revert at the earliest";
                //$objBase->Redirect('enquiry-form.php?action=submitted', "Thanks for contacting YHAI. We will revert at the earliest", 0);
            }
        }
    }
}
$page_info = $objPage->get_page_info($page_id);
$node_info = $objMenu->getInfo($node);
$objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);
$MSG->ERROR = $err;
?>
<?php include_once(PATH_INCLUDES . "/header.php"); ?>
<div id="body">
   <!-- BODY start -->
    <!--page title Start-->
  <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo STATIC_HEADER_IMG;?>);">
      <ul>
        <li><a href="javascript:;">Enquiry</a></li>
      </ul>
    </div>
    <!--page title end-->
  
   
   <div class="container">
   <div class="row">
      <div class="col-md-3 col-sm-3">
    <?php include_once(PATH_INCLUDES . "/left-side-new.php"); ?>
      </div>
      <div class="col-md-9 col-sm-9" style="margin-top:29px; border:1px solid #ff8000; padding:2%;">
          <!-- Start Message section -->
          <?php include_once(PATH_INCLUDES."/showMsg.php");?>
          <div class="col-md-12 col-sm-12">
                <h3>Enquiry Form</h3>                
                <p align="right"> Call for Memberships at 011-45999014/15/16<br /> 
                    Adventure Programs at 011-45999013/23/26<br />  
                    Hostel Bookings at 011-45999021/16<br />                    
                    Member Support at 011-45999000                
                </p>
          </div>
          <!-- End Message Section -->
          <form name="EnquiryForm" id="EnquiryForm" method="post" action="" class="RegisterForm">
          <div class="col-md-12 col-sm-12">
              <div class="col-md-6 col-sm-6 form-group">
                <label for="First Name" class="control-label">First Name</label>
                <input type="text" name="fname" id="fname" value="<?php echo $data['fname']; ?>" class="form-control"/>
              </div>
               <div class="form-group col-md-6 col-sm-6 ">
                <label for="Last Name" class="control-label">Last Name</label>
                <input type="text" name="lname" id="lname" value="<?php echo $data['lname']; ?>" class="form-control"/>
               </div>
               </div>
                <div class="col-md-12 col-sm-12">
              <div class="form-group col-md-6 col-sm-6 ">
                <label for="Designation" class="control-label">Designation</label>
                <input type="text" name="designation" id="designation" value="<?php echo $data['designation']; ?>"class="form-control"/>
              </div>
               <div class="form-group col-md-6 col-sm-6 ">
                <label for="Organization" class="control-label">Organization</label>
                <input type="text" name="organization" id="organization" value="<?php echo $data['organization']; ?>"class="form-control"/>
               </div>
               </div>
                <div class="col-md-12 col-sm-12">
              <div class="form-group col-md-6 col-sm-6 ">
                <label for="city" class="control-label">city</label>
              <input type="text" name="city" id="city" value="<?php echo $data['city']; ?>"class="form-control"/>
              </div>
               <div class="form-group col-md-6 col-sm-6 ">
                 <label for="Landline No- (STD+ Phone)" class="control-label">Landline No- (STD+ Phone)</label>
               <input type="text" name="phone" id="phone" value="<?php echo $data['phone']; ?>" class="form-control"/>
               </div>
               </div>
                <div class="col-md-12 col-sm-12">
              <div class="form-group col-md-6 col-sm-6 ">
               <label for="mobile" class="control-label">mobile</label>
              <input type="text" name="mobile" id="mobile" value="<?php echo $data['mobile']; ?>" class="form-control"/> 
              </div>
               <div class="form-group col-md-6 col-sm-6 ">
               <label for="email" class="control-label">email</label>
               <input type="text" name="email" id="email" value="<?php echo $data['email']; ?>" class="form-control"/>
               </div>
               </div>
                <div class="col-md-12 col-sm-12">
              <div class="form-group col-md-6 col-sm-6 ">
 <label for="Query_type" class="control-label">Query_type</label>
              <select name="query_type" id="query_type" class="selectbox form-control">                                                                        
                                    <option value="Membership&Discounts">Membership & Discounts</option>
                                    <option value="Adventure&Trekkingprogrammes">Adventure & Trekking programmes</option>
                                    <option value="HostelBookings&Cancellations">Hostel Bookings & Cancellations</option>
                                    <option value="MarketingwithYHAI">Marketing with YHAI</option>
                                    <option value="AffiliatewithYHAI">Affiliate with YHAI</option>
                                    <option value="TravelAgents-Consultants">Travel Agents/Consultants</option>
                                </select>
              </div>
               <div class="form-group col-md-6 col-sm-6 ">
 <label for="query" class="control-label">query</label>
 <textarea name="Query" id="Query" class="form-control" maxlength="150"><?php echo $data['query']; ?></textarea>

               </div>
               </div>
                <!-- <div class="col-md-12 col-sm-12">
              <div class="form-group col-md-6 col-sm-6 ">
<label for="Security_code" class="control-label">Security_code</label>
<input type="text" name="security_code" id="security_code" value="" class="form-control"/>
              </div>
               <div class="form-group col-md-6 col-sm-6 ">
<label for="IMG" class="control-label">IMG</label>
<img src="<?php //echo SITE_URL ?>CaptchaSecurityImages.php"class="form-control" />
               </div>
               </div>  -->
                <div class="form-group col-md-12 col-sm-12">
              
<label for="Botton" class="control-label" style="width:100%;"></lable>
      <div class="Inner-buttonDiv"style="width:100%;"><input type="submit" value="Submit Enquiry" class="btn_green proceed_buttton btns"style="width:100%" />
      </div>          
               </div>
               <input type="hidden" name="action" value="submit_enquiry" />    
               </form>   
      </div>
      <!--<div class="col-md-1 col-sm-1"></div>-->
   </div>
   </div>
   
      <!--left side start-->
      <!-- desc icons Start-->
     
                  <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>
              
            <!-- TAB CONTENT end -->
            <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>
        
      <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" />
    
      <?php echo $objBanner->getBottomBanner(); ?>
      <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>
   
</div>
<!-- BODY end -->

<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>