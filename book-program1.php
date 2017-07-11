

<?php
   include_once("includes/framework.php");
   include(PATH_CONTROLLERS."/MasterController.php");
  
   
   $page_id = JRequest::getVar('page_id');
   
   $node = JRequest::getVar('node');
       
   if (!$node) {
       $node = $objMenu->get_page_node_id();
   }
   $data = JRequest::get('post');
   $id = JRequest::getVar('id');
   $ty = JRequest::getVar('ty');
   $prog = $objProgramme->getInfo($id, 1);
   if (!is_object($prog)) {
       $objBase->Redirect('index.php');
   }
   $proGal = $objProgramme->getGalList($id);
   $objBase->setMetaData($prog->metaTitle, $prog->metaKeyword, $prog->metaDescription);
   
   if (!$node) {
   
       $node = $objMenu->get_page_node_id();
   
   }
   
   $data = JRequest::get('post');
   
   $err = "";
   
   $id = JRequest::getVar('id');
   
   $dt = JRequest::getVar('dt');
   
   //$objMembers->CheckLogin();  /// Check Login
   
   $ProgramDetail = $objProgramme->getProgramDetail($id); /// Validate Programme
   
   if (!$ProgramDetail) {
   
       $objBase->Redirect('index.php');
   
   }
   
   if ($dt != "") {
   
       $data['abk_reporting_date'] = date('Y-m-d', $dt);
   
   }
   
   if ($data) {
   
       if ($data['action'] == 'register') {
   
           $today = date("Y-m-d");
   
           $age = $objMembers->calculate_age($data['abk_dob']);
   
           if ($age < $ProgramDetail->adp_min_age or $age > $ProgramDetail->adp_max_age) {
   
               $err .= "<li>Your age  limit should be between " . $ProgramDetail->adp_min_age . " and " . $ProgramDetail->adp_max_age . "</li>";
   
           }
   
           if ($data['abk_reporting_date'] == "") {
   
               $err .= "<li>Reporting date is missing.</li>";
   
           }
   
           if ($data['abk_reporting_date'] < $today) {
   
               $err .= "<li>Reporting date can be less then today.</li>";
   
           }
   
           if ($data['abk_name'] == "") {
   
               $err .= "<li>Name field is required.</li>";
   
           }
   
           if ($data['abk_email'] == "") {
   
               $err .= "<li>Email field is required.</li>";
   
           }
   
           if ($data['abk_dob'] == "") {
   
               $err .= "<li>DOB field is required.</li>";
   
           }
   
           if ($data['abk_state'] == "") {
   
               $err .= "<li>State field is required.</li>";
   
           }
   
           if ($data['abk_father_name'] == "") {
   
               $err .= "<li>Father/Husband/Spouse field is required.</li>";
   
           }
   
           if ($err != "") {
   
               $err = "<ul><li>The submitted form was invalid. Try submitting again.</li>" . $err . "</ul>";
   
           } else {
   
               $membership_info = $objMembers->get_member_info_by_memberhip_code($data['abk_mem_code']);
   
               if (!$membership_info or $membership_info->status != 1) {
   
                   //$err = 'Sorry this membership code is either invalid or expired.'; // Disabled on 23July2015 as per client instructions
   
               }
   
               if (!$err) {
   
                   $retVal = $objProgramme->checkBookingSeat($id, $data['abk_reporting_date']);
   
                   if ($retVal == 'A' || $retVal == 'W') {
   
                       /*
   
                        * File Upload Begin
   
                        */
   
                       if ($_FILES['abk_photo']['name'] != "") {
   
                           $handle = new upload($_FILES['abk_photo']);
   
                           if ($handle->uploaded) {
   
                               $handle->file_safe_name = true;
   
                               $handle->file_max_size = '524288';  // 512 KB Max
   
                               $allowed_types = $Config->allowed_image_types;
   
                               array_push($allowed_types, 'application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/vnd.pdf', 'text/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
   
                               $handle->allowed = $allowed_types;
   
                               $handle->process(UPLOAD_PATH . "/programme/photograph/");
   
                               if ($handle->processed) {
   
                                   //echo 'image resized';
   
                                   $data['abk_photo'] = $handle->file_dst_name;
   
                                   @unlink(UPLOAD_PATH . '/programme/photograph/' . $data["old_$file"]);
   
                                   $handle->clean();
   
                               } else {
   
                                   $err .= "<li>" . $handle->error . "</li>";
   
                               }
   
                           }
   
                       }
   
   
   
                       if ($_FILES['abk_residence_proof']['name'] != "") {
   
                           $handle = new upload($_FILES['abk_residence_proof']);
   
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
   
                               $handle->process(UPLOAD_PATH . "/programme/residence_proof/");
   
                               if ($handle->processed) {
   
                                   //echo 'image resized';
   
                                   $data['abk_residence_proof'] = $handle->file_dst_name;
   
                                   @unlink(UPLOAD_PATH . '/programme/residence_proof/' . $_POST["old_abk_residence_proof"]);
   
                                   $handle->clean();
   
                               } else {
   
                                   $err .= "<li>" . $handle->error . "</li>";
   
                               }
   
                           }
   
                       }
   
   
   
                       if ($_FILES['abk_signature']['name'] != "") {
   
                           $handle = new upload($_FILES['abk_signature']);
   
                           if ($handle->uploaded) {
   
                               $handle->file_safe_name = true;
   
                               $handle->file_max_size = '524288';  // 512 KB Max
   
                               $allowed_types = $Config->allowed_image_types;
   
                               array_push($allowed_types, 'application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/vnd.pdf', 'text/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
   
                               $handle->allowed = $allowed_types;
   
                               $handle->process(UPLOAD_PATH . "/programme/signature/");
   
                               if ($handle->processed) {
   
                                   //echo 'image resized';
   
                                   $data['abk_signature'] = $handle->file_dst_name;
   
                                   @unlink(UPLOAD_PATH . '/programme/signature/' . $data["old_abk_signature"]);
   
                                   $handle->clean();
   
                               } else {
   
                                   $err .= "<li>" . $handle->error . "</li>";
   
                               }
   
                           }
   
                       }
   
                       /*
   
                        * Upload  handicap certificate
   
                        */
   
                       if ($_FILES['abk_handicap_certificate']['name'] != "") {
   
                           $handle = new upload($_FILES['abk_handicap_certificate']);
   
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
   
                               $handle->process(UPLOAD_PATH . "/programme/handicap_certificate/");
   
                               if ($handle->processed) {
   
                                   //echo 'image resized';
   
                                   $data['abk_handicap_certificate'] = $handle->file_dst_name;
   
                                   @unlink(UPLOAD_PATH . '/programme/handicap_certificate/' . $_POST["old_abk_handicap_certificate"]);
   
                                   $handle->clean();
   
                               } else {
   
                                   $err .= "<li>" . $handle->error . "</li>";
   
                               }
   
                           }
   
                       }
   
   
   
                       if ($_FILES['abk_handicap_photo']['name'] != "") {
   
                           $handle = new upload($_FILES['abk_handicap_photo']);
   
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
   
                               $handle->process(UPLOAD_PATH . "/programme/handicap_photo/");
   
                               if ($handle->processed) {
   
                                   //echo 'image resized';
   
                                   $data['abk_handicap_photo'] = $handle->file_dst_name;
   
                                   @unlink(UPLOAD_PATH . '/programme/handicap_photo/' . $_POST["old_abk_handicap_photo"]);
   
                                   $handle->clean();
   
                               } else {
   
                                   $err .= "<li>" . $handle->error . "</li>";
   
                               }
   
                           }
   
                       }
   
   
   
                       if ($data['abk_residence_proof'] == "" or $data['abk_photo'] == "") {
   
                           $err = "<ul><li>The submitted form was invalid. Try submitting again.</li>" . $err . "</ul>";
   
                       }
   
   
   
                       if ($data['abk_city'] == "others") {
   
                           $data['abk_city'] = $objMasters->saveOtherCityOption($data['other_city'], $data['abk_state']);
   
                       }
   
                       $cityRow = $objMasters->list_city(" and city_id ='" . $data['abk_city'] . "'");
   
                       $data['city_name'] = $cityRow[0]->city_name;
   
                       /*
   
                        * Process Handicap Discount
   
                        */
   
                       if(isset($data['abk_handicap'])){
   
                           $booking_amount = $ProgramDetail->adp_handicap_price;
   
                       }else{
   
                           $booking_amount = $ProgramDetail->adp_price;
   
                       }
   
                       
   
                       $data['abk_id'] = $objProgramme->generateSeatOnline($id, $data, $booking_amount);
   
                       $data['abk_amount'] = $booking_amount;
   
                       if ((int) $booking_amount > 0) {
   
                           $objEBSPay->secure_form_post_adv_program($data);
   
                           die;
   
                       } else {
   
                           /*
   
                            * Execute the volunteer programme booking , where booking amount will be zero.
   
                            */
   
                           $bkDetail = $objProgramme->getBookingDetail($data['abk_id'], 'I');
   
                           if ($bkDetail) {
   
                               $st = $objProgramme->checkBookingSeat($bkDetail->abk_prog_id, date('Y-m-d', $bkDetail->abk_reporting_date));
   
                               if ($st != 'N') {
   
                                   $response = array("abk_transaction_id" => "", "abk_PaymentID" => "");
   
                                   $objProgramme->updateBookingOnline($data['abk_id'], $response, $st);
   
                                   $objProgramme->updateOnlineBookingNumbers($data['abk_id']);
   
                                   $_SESSION['program_TransactionID'] = 'Nil';
   
                                   $_SESSION['transaction_response']['bookingid'] = $data['abk_id'];
   
                                   $objProgramme->send_programme_booking_email($bkDetail);
   
                                   $objBase->Redirect('program-payment-confirmation.php');
   
                               } else {
   
                                   $objBase->Redirect('program-payment-canceled.php');
   
                               }
   
                           }
   
                       }
   
                   } else {
   
                       $err = "Sorry! Seat not available.";
   
                   }
   
               }
   
           }
   
       }
   
   }
   
   $page_info = $objPage->get_page_info($page_id);
   
   $node_info = $objMenu->getInfo($node);
   
   $objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);
   
   $membership_options = $objMembers->list_membership_options($data['plan_code'], 'Individual');
   
   $state_options = $objMasters->list_state_options($data['abk_state']);
   
   $city_options = $objMasters->list_city_options($data['abk_city'], $data['abk_state']);
   
   ?>
<?php include_once(PATH_INCLUDES . "/header.php"); ?>
<div id="body">
<!-- BODY start -->
<div class="page_title home_subscribe_section" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="opacity: 0.75;" >
   <ul>
      <li><a href="javascript:;">Tour Destination</a></li>
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
                                 <li><a href="#program_booking_details">Booking Details</a></li>
                                 <li><a href="#program_personal_info">Personal Info</a></li>
                                 <li><a href="#program_payment_info">Payment Info</a></li>
                                 <li><a href="#program_status_info">Confirmation</a></li>
                              </ul>
                           </div>
                           <div id="program_booking_details" class="main_content_area hotel_main_content">
                              <div class="inner_container">
                                 <!--  tab inner section two Start -->
                                 <div class="tab_inner_section hotel_inner_section">
                                    <div class="heading_tab_inner" >
                                       <h5 style="float: left;">Program Details</h5>
                                       <span style="float: right; background-color: #fdb714">change Program</span>
                                    </div>
                                    <div class="tab_inner_body full_width">
                                       <div class="col-lg-9 col-md-9 col-sm-12">
                                          <div class="tour_packages_right_section left_space_40">
                                             <div class="tour_packages_details_top row">
                                                <div class="top_head_bar  col-md-4 col-sm-4" style=" margin-bottom: 2%;margin-top: 2%; float: left;">
                                                   <figure  style="margin-bottom: 0%; float: left;"> <a href="<?php echo SITE_URL ?>adventure-programme.php?id=<?php echo $prog->adp_id ?>#gal" class="zoom-item" title="Portfolio Item Title"> <img style=" float: left;" src="<?php echo SITE_URL ?>uploads/programme/photo/<?php echo $prog->adp_photo; ?>" alt=""> </a></figure>
                                                </div>
                                                <div class="col-md-4 col-sm-4 prognamewidth">
                                                   <h4 style=" width: 220%;" class="prognamewidth"><?php echo $prog->adp_name ?></h4>
                                                   <div class="inludes_hotel_booking prognamewidth" style="width: 252%;">
                                                      <div class="left_lists  col-md-6 col-sm-6">
                                                         <table>
                                                            <tr>
                                                               <td class="label_list">Program Type</td>
                                                               <td>-</td>
                                                               <td><?php echo $objProgramme->GetProgramType($prog->adp_type);?></td>
                                                            </tr>
                                                            <tr>
                                                               <td class="label_list">Program Category</td>
                                                               <td>-</td>
                                                               <td><?php echo $objProgramme->GetProgramCategory($prog->adp_category); ?></td>
                                                            </tr>
                                                         </table>
                                                      </div>
                                                      <div class="left_lists  col-md-6 col-sm-6">
                                                         <div class="table_bold">
                                                            <table>
                                                               <tr>
                                                                  <td class="label_list">Per Person Price</td>
                                                                  <td>-</td>
                                                                  <td class="bold"><i class="fa fa-inr rupeesicon" aria-hidden="true" style=" font-size: 19px !important; color: #ffffff;"></i><?php echo $prog->adp_price ?></td>
                                                               </tr>
                                                               <tr>
                                                                  <td class="label_list">Duration</td>
                                                                  <td>-</td>
                                                                  <td><?php echo $prog->adp_period ?></td>
                                                               </tr>
                                                            </table>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                                <div class="right_includes_hotel col-lg-4 col-md-4 checkinmargin " style="float: right;margin-top: 8%; margin-right: -36%;">
                                                   <?php 
                                                      $startDateArray = $objMasters->getDateInJson($objMasters->dateFormat($prog->adp_from_date,null,"d-M-Y"),"DD/MM/Y");
                                                      ?>
                                                   <div class="check_in_out_wrap">
                                                      <div class="check_in marginzero" style="float: left;margin-left: 39%; margin-top: -21%;">
                                                         <label>Start Date</label>
                                                         <div class="check_in_box">
                                                            <span class="day"><?php echo $startDateArray->Y; ?></span>
                                                            <span class="date"><?php echo $startDateArray->DD; ?></span> 
                                                            <span class="month"><?php echo $startDateArray->MM; ?></span>
                                                         </div>
                                                      </div>
                                                      <?php 
                                                         $endDateArray = $objMasters->getDateInJson($objMasters->dateFormat($prog->adp_to_date,null,"d-M-Y"),"DD/MM/Y");
                                                         ?>
                                                      <div class="check_in marginzero" style="float: right; margin-right: -16%;margin-top: -21%;">
                                                         <label>End Date</label>
                                                         <div class="check_in_box">
                                                            <span class="day"><?php echo $endDateArray->Y; ?></span>
                                                            <span class="date"><?php echo $endDateArray->DD; ?></span> 
                                                            <span class="month"><?php echo $endDateArray->MM; ?></span> 
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                                <!-- tab include area Start -->
                                                <div class="full_width package_highlight_section response_width " style="width: 141%; border-top: 1px solid #d3d3d3; ">
                                                   <div class="row marginlftlist" style="margin-left: 0%;">
                                                      <div class="col-lg-9 col-md-9 col-sm-12">
                                                         <div class="cost_include_exclude">
                                                            <ul>
                                                               <li class="listalign"><i class="fa fa-location-arrow listreporticon"  aria-hidden="true"></i>
                                                                  <span class="starting_text" ><?php echo $prog->adp_report_point ?> - <?php echo $objProgramme->showProgramDuration($prog->adp_duration, $prog->adp_from_date, $prog->adp_to_date); ?>
                                                                  </span>
                                                                  <br/>
                                                               </li>
                                                               <li class="listalign"> <i class="fa fa-calendar listreporticon" aria-hidden="true"></i> 
                                                                  <span class="time_date"><span ><?php echo $prog->adp_period ?></span>
                                                                  </span>
                                                               </li>
                                                               <li class="listalign"><i class="fa fa-briefcase listreporticon" aria-hidden="true"></i>                    
                                                                  <span class="includes_text" >includes:<?php echo $prog->adp_cover_services ?>
                                                                  </span>
                                                               </li>
                                                            </ul>
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                                <!-- tab include area End --> 
                                                <!-- total row Start-->
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <!--  review area start -->
                                 </div>
                                 <div class="full_width total_price_row" style="float:left;">
                                    <h2>
                                       <i class="fa fa-inr rupeesicon" aria-hidden="true" style=" font-size: 31px !important; color: #ffffff;"></i><?php echo $prog->adp_price ?>
                                    </h2>
                                    <!-- total row End--> 
                                    <!-- proceed button -->
                                    <button type="submit" value="proceed to next step" style="float:right; margin-top: 2%; width:370px; font-size:135%;" class="btn_green proceed_buttton btns Width100res">proceed to next step <i class="fa fa-chevron-right" style="margin-left:10%;" aria-hidden="true"></i>
                                    </button>
                                 </div>
                                 <!-- proceed button -->
                              </div>
                           </div>
                           <!--------personal information  block start------------>
                           <div id="program_personal_info" class="main_content_area hotel_main_content">
                              <!--  tab inner section three Start -->
                              <div class="inner_container">
                                 <form class="package_booking_main">
                                    <div class="tab_inner_section inner_section_2 hotel_inner_section">
                                       <div class="col-lg-12 col-md-12">
                                          <h4>
                                             Program Detail : <?php echo $ProgramDetail->adp_name; ?> &nbsp;<br/>
                                             <h5><?php echo $ProgramDetail->adp_report_point ?> - <?php echo $objProgramme->showProgramDuration($ProgramDetail->adp_duration, $ProgramDetail->adp_from_date, $ProgramDetail->adp_to_date); ?><?php echo $ProgramDetail->adp_period ?></h5>
                                          </h4>
                                          <br />
                                       </div>
                                       <div class="tab_inner_body full_width">
                                          <!--  package_booking_form start -->
                                          <div class="package_booking_form" id="adventure_program_form">
                                 <form name="RegisterForm" id="RegisterForm" method="post" action="book-program.php" class="RegisterForm package_booking_form_main" enctype="multipart/form-data">
                                 <div class="col-lg-6 col-md-6 ">
                                 <label class="control-label">Reporting Date<span>*</span></label>
                                 <input  type="text" name="abk_reporting_date" class="homeHostelSearchInputComman  form-control booking_input" id="abk_reporting_date" placeholder="dd/mm/yyyy" value="<?php echo $data['abk_reporting_date']; ?>" readonly="readonly" >
                                 </div>
                                 <div class="col-lg-6 col-md-6">
                                 <label class="control-label">Membership code<span>*</span></label>
                                 <input type="text" placeholder="Membership code" name="abk_mem_code" id="abk_mem_code" class=" form-control booking_input">
                                 </div>
                                 <div class="col-lg-6 col-md-6">
                                 <label class="control-label">Name<span>*</span></label>
                                 <input type="text" placeholder=" Name" name="abk_name" id="abk_name" value="<?php echo $data['abk_name']; ?>" class= "form-control booking_input">
                                 </div>
                                 <div class="col-lg-6 col-md-6">
                                 <label class="control-label">Email<span>*</span></label>
                                 <input type="text" placeholder="Email" name="abk_email"   id="abk_email" value="<?php echo $data['abk_email']; ?>" class=" form-control booking_input">
                                 </div>
                                 <div class="col-lg-6 col-md-6" >
                                 <label style="width:100%">Select Gender</label>
                                 <label class="radio-inline">
                                 <input name="abk_gender" id="gender_male" type="radio" class="radiobuton" value="Male" checked="checked">Male
                                 </label>
                                 <label class="radio-inline">
                                 <input  name="abk_gender" id="gender_female" type="radio"  value="Female" <?php echo ($data['gender'] == 'Female') ? ' checked="checked"' : '';?>/>Female
                                 </label>
                                 </div>
                                 <div class="col-lg-6 col-md-6">
                                 <label for="dob"  class="control-label">Date Of Birth</label>
                                 <input  type="text" name="abk_dob" id="dob" value="<?php echo $data['abk_dob']; ?>" readonly="readonly"   class="form-control"/>
                                 </div>
                                 <div class="col-lg-6 col-md-6" >
                                 <label class="control-label">Father/Husband/Spouse<span>*</span></label>
                                 <input type="text" name="abk_father_name" placeholder="Father/Husband/Spouse" id="abk_father_name" maxlength="60" value="<?php echo $data['abk_father_name']; ?>" class="form-control datafield booking_input" />
                                 </div>
                                 <div class="col-lg-6 col-md-6">
                                 <label class="control-label" >Address<span>*</span></label>
                                 <input type="text" name="abk_address" id="abk_address" placeholder="Town/City" class="datafield1 form-control booking_input" maxlength="200"><?php echo $data['abk_address']; ?></input>
                                 </div>
                                 <div class="col-lg-6 col-md-6"  >
                                 <label for="state"  class="control-label">State</label>
                                 <select name="state" id="state" class="form-control">
                                 <option value="">Select</option>
                                 <?php echo $state_options ?>
                                 </select>
                                 </div>
                                 <div class="col-lg-6 col-md-6" >
                                 <label for="city"  class="control-label">City</label>
                                 <select name="city" id="city" class="form-control">
                                 <option value="">Select</option>
                                 <?php echo $city_options ?>
                                 </select>
                                 </div>
                                 <div class="col-lg-6 col-md-6">
                                 <label class="control-label">Postal Code<span>*</span></label>
                                 <input type="text" placeholder="Postal code" name="abk_postal_code" maxlength="6" id="abk_postal_code" value="<?php echo $data['abk_postal_code']; ?>" class="datafield booking_input form-control " />
                                 </div>
                                 <div class="col-lg-6 col-md-6  form-group">
                                 <label class="control-label">Contact number<span>*</span></label>
                                 <input type="text"  placeholder="contact number"  name="abk_phone_number" maxlength="15" id="abk_phone_number" value="<?php echo $data['abk_phone_number']; ?>" class="datafield booking_input form-control " />
                                 </div>
                                 <div class="col-lg-12 col-md-12">
                                 <div class="col-lg-4 col-md-4  form-group">
                                 <input type="file" name="photograph" id="photograph"  class="filestyle" data-buttonText="Photograph" data-classInput="input-small" data-classIcon="fa fa-picture-o"/>
                                 <input type="hidden" name="old_photograph" id="old_photograph" value="<?php echo $data['photograph']; ?>" />
                                 <small id="photographHelp" class="form-text text-muted" style="float:left">Please upload a passport size photograph. photo size should be upto 512 KB.</small>
                                 </div>
                                 <div class="col-lg-4 col-md-4  form-group" style="margin-left:2%;">
                                 <input  type="file" class="filestyle" data-buttonText="Residence Proof"  name="residence_proof" id="residence_proof" data-classInput="input-small" data-classIcon="fa fa-file-text-o"/>
                                 <input type="hidden" name="old_residence_proof" id="old_residence_proof" value="<?php echo $data['residence_proof']; ?>" />
                                 </div>
                                 <div class="col-lg-4 col-md-4  form-group" style="margin-right:-6%;">
                                 <input type="file" name="abk_signature" id="abk_signature" data-buttonText="Signature" data-classInput="input-small" class="browse filestyle" />
                                 <span class="form-field-info" title="Scanned Signature Image"><img src="images/icon_info_16x16.png" border="0" />
                                 <small id="photographHelp" class="form-text text-muted" style="float:left">Please upload a Signature size photograph. photo size should be upto 512 KB</small>
                                 </span>
                                 </div>
                                 </div>
                                 <div class="col-lg-6 col-md-6">
                                 <label class="checkbox InputGroup" id="ValidCheckbox" style="float:left">
                                 <input type="checkbox"  name="terms" id="i_agree" value="1"/> I have read this Agreement and agree to the
                                 </label>
                                 <label class="control-label" style="float:left;height:100%;padding-top:2%;">
                                 <a href="terms-membership.php" style="color:blue;" class="hover dialogBox" loader-msg="Please Wait While Getting Terms & Conditions........"> &nbsp;Terms & conditions</a>
                                 </label>
                                 </div>
                                 <div class="col-lg-6 col-md-6" style="float: right; font-size:169%; margin-right:-10%;">
                                 <label style="font-size:20px;">Program Fee (in Rupees) : &nbsp;</label>
                                 <i class="fa fa-inr rupeesicon" aria-hidden="true" style=" font-size: 24px !important; color: #ffffff;"></i> <?php echo $ProgramDetail->adp_price; ?> /-
                                 </div>
                                 <!--  package_booking_form END --> 
                                 <!--  tab_inner_body end -->
                                 <!--  tab inner three section End --> 
                                 <!-- proceed button -->
                                 <!-- proceed button -->
                                 </form>
                                 </div>
                                 </div>
                                 <div class="full_width Inner-buttonDiv">
                                 <button type="submit" style="width:100% !important; margin-top: 2%;" value="proceed to next step" class="btn_green Width100res proceed_buttton btns">proceed to next step</button>
                                 </div>
                                 </div>
                              </div>
                              <!--  inner container end --> 
                           </div>
                           <div id="program_payment_info" class="main_content_area hotel_main_content">
                              <!-- inner_container Start -->
                              <div class="inner_container">
                                 <!--  tab inner three section Start -->
                                 <div class="tab_inner_section hotel_inner_section">
                                    <div class="heading_tab_inner">
                                       <h5>payment Details</h5>
                                    </div>
                                    <!--  tab_inner_body Start-->
                                    <div class="tab_inner_body full_width">
                                       <div class="payment_details_main">
                                          <!-- Review content main -->
                                          <div class=" row review_content">
                                             <div class=" col-lg-12 col-md-12 top_head_bar">
                                                <h4><?php echo $prog->adp_name ?></h4>
                                             </div>
                                             <div class="col-md-12 col-sm-12">
                                                <span class="country_span"><?php echo "("?><?php echo $objProgramme->GetProgramType($prog->adp_type);?><?php echo ","?><?php echo $objProgramme->GetProgramCategory($prog->adp_category); ?><?php echo ")"?> </span> <span class="time_date"><i class="fa fa-clock-o"></i><?php echo $prog->adp_period ?></span> 
                                             </div>
                                          </div>
                                          <!-- Review content main -->        
                                          <div class="col-lg-12 col-md-12">
                                             <div class="payment_table table_bold">
                                                <div class="inludes_hotel_booking" >
                                                   <div class="left_lists  col-md-6 col-sm-6">
                                                      <table>
                                                         <td class="label_list">Name</td>
                                                         <td>-</td>
                                                         <td><?php echo $data["name"]; ?></td>
                                                         </tr>
                                                         <td class="label_list">Gender</td>
                                                         <td>-</td>
                                                         <td><?php echo $data["abk_gender"]; ?></td>
                                                         </tr>
                                                         <td class="label_list">Email</td>
                                                         <td>-</td>
                                                         <td><?php echo $data["email"]; ?></td>
                                                         </tr>
                                                         <td class="label_list">Address</td>
                                                         <td>-</td>
                                                         <td><?php echo $data["abk_address"]; ?></td>
                                                         </tr>
                                                      </table>
                                                   </div>
                                                   <div class="left_lists  col-md-6 col-sm-6">
                                                      <div class="table_bold">
                                                         <table>
                                                            <tr>
                                                               <td class="label_list">Program Type</td>
                                                               <td>-</td>
                                                               <td><?php echo $objProgramme->GetProgramType($prog->adp_type);?></td>
                                                            </tr>
                                                            <tr>
                                                               <td class="label_list">Program Category</td>
                                                               <td>-</td>
                                                               <td><?php echo $objProgramme->GetProgramCategory($prog->adp_category); ?></td>
                                                            </tr>
                                                            <tr>
                                                               <td class="label_list">Total Price</td>
                                                               <td>-</td>
                                                               <td class="bold">Rs.<?php echo $prog->adp_price ?></td>
                                                            </tr>
                                                            <tr>
                                                               <td class="label_list">Duration</td>
                                                               <td>-</td>
                                                               <td><?php echo $prog->adp_period ?></td>
                                                            </tr>
                                                         </table>
                                                      </div>
                                                   </div>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                    <div class="full_width t_align_c Inner-buttonDiv">
                                       <button type="submit" style="width: 50%;" value="proceed to next step" class="btn_green Width100res proceed_buttton btns">Pay Now</button>
                                    </div>
                                    <!-- payment_details_main end --> 
                                    <!--  tab_inner_body end --> 
                                 </div>
                              </div>
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
<!-- BODY end -->
<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>
<!-- include js -->
<script type="text/javascript" src="<?php echo SITE_PATH_THEME_JS ?>/bootstrap-filestyle.min.js"/>
   <script type="text/javascript">
       //add class to file type
       $(":file").filestyle({classInput: "input-small"});
</script>

