<?php
   include_once("includes/framework.php");
   
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
  $today = $objMasters->Today();
  //$data['date']    = $objMasters->dateFormat($today,null,"Y-m-d");
   if ($prog!=null && !empty($prog)) {
       if($prog->is_providing_transport)
       {
          $prog->ptd = $objProgramme->getProgramTransport($prog->adp_id,true,'','is_sold_out = 0');
          //pre($prog->ptd);
          $ptd = null;
          if($prog->ptd!=null && !empty($prog->ptd))
          {
            foreach ($prog->ptd as $key => $value) {
              if($value->id!=null && !empty($value->id))
              {
                $today_string       =  strtotime($objMasters->dateFormat($today,null,"Y-m-d"));
                $start_date         = strtotime($value->start_date);
                $end_date           = strtotime($value->end_date);
                if($start_date<$today_string)
                {
                  $value->start_date = $objMasters->dateFormat($today_string,null,"Y-m-d");
                }
                if(strtotime($value->start_date) >= $today_string && $today_string <= $end_date){
                  $TotalBookedPrograms = $objProgramme->getBookedProgramTransportSeats($value->id,strtotime($value->start_date),'AND status="'.Enum::Status()->ACTIVE.'"');
                  $available_seats = ($value->no_of_seats - $TotalBookedPrograms[0]->total_seats)>0?$value->no_of_seats - $TotalBookedPrograms[0]->total_seats:0;
                  if($available_seats>0)
                  {
                    $value->available_seats = $available_seats;
                    $value->max_no_of_seats = $prog->adp_category=="F"? $available_seats:1;
                  }else{
                    $value->available_seats = 0;
                    $value->max_no_of_seats = 0;
                  }
                  $ptd[$key] = $value;
                }
              }else{
                $ptd[$key] = $value;
              }
            }
            $ptd = (object)$ptd;
            //set both way
            if($ptd->going_to!=null && !empty($ptd->going_to) && $ptd->returning_from!=null && !empty($ptd->returning_from) && $prog->ptd->both_way->id!=null && $prog->ptd->both_way->id!=0)
            {
              $ptd->both_way = $prog->ptd->both_way;
              $available_seats = min($ptd->going_to->available_seats,$ptd->returning_from->available_seats);
              if($available_seats>0)
              {
                $ptd->both_way->available_seats = $available_seats;
                $ptd->both_way->max_no_of_seats = $prog->adp_category=="F"? $available_seats:1;
              }else{
                $ptd->both_way->available_seats = 0;
                $ptd->both_way->max_no_of_seats = 0;
              }
            }else{
              $ptd->both_way = null;
            }
            //setprogram_transport_type 
            if($ptd->going_to->id!= 0 &&$ptd->going_to->id!= null)
            {
              $ptd->program_transport_type->id!= Enum::ProgramTransportTypes()->GOING_TO;
            }
            if($ptd->returning_from->id!= 0 && $ptd->returning_from->id!= null)
            {
              $ptd->program_transport_type->id!= Enum::ProgramTransportTypes()->RETURNING_FROM;
            }
          }
          $prog->ptd = $ptd;
          if(($prog->ptd->going_to!=null && !empty($prog->ptd->going_to)) || ($prog->ptd->returning_from!=null && !empty($prog->ptd->returning_from)))
          {
            $prog->is_providing_transport = true;
          }else{
            $prog->is_providing_transport = false;
          }
       }
   }
   $proGal = $objProgramme->getGalList($id);
   $objBase->setMetaData($prog->metaTitle, $prog->metaKeyword, $prog->metaDescription);
   
   if (!$node) {
   
       $node = $objMenu->get_page_node_id();
   
   }
   
   $dt = JRequest::getVar('dt');
   
   //$objMembers->CheckLogin();  /// Check Login
   
   $ProgramDetail = $objProgramme->getProgramDetail($id); /// Validate Programme
   
   if (!$ProgramDetail) {
   
       $objBase->Redirect('index.php');
   
   }
   
   if ($dt != "") {
   
       $data['abk_reporting_date'] = $objMasters->dateFormat($dt,null,null);
   
   }
   
   if ($data) {
   
       if ($data['action'] == 'register') {
        $isFromBookingForm = true;
   
           $today =  $objMasters->Today();

           //convert to old format as per db requirement
           if(!IsNull($data['abk_reporting_date']))
            $data['abk_reporting_date'] = $objMasters->dateFormat($data['abk_reporting_date'],null,"Y-m-d");
            if(!IsNull($data['abk_dob']))
            $data['abk_dob'] = $objMasters->dateFormat($data['abk_dob'],null,"Y-m-d");

           $age = $objMembers->calculate_age($data['abk_dob']);
   
           if ($age < $ProgramDetail->adp_min_age or $age > $ProgramDetail->adp_max_age) {
   
               $err .= "<li>Your age  limit should be between " . $ProgramDetail->adp_min_age . " and " . $ProgramDetail->adp_max_age . "</li>";
   
           }
   
           if ($data['abk_reporting_date'] == "") {
   
               $err .= "<li>Reporting date is missing.</li>";
   
           }

           if ($data['abk_reporting_date'] < $objMasters->dateFormat($today,null,"Y-m-d")) {
   
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
           // for validate transport booking 
            if($data['is_transport_booked'])
            {
              if(($data['ptb']==null || empty($data['ptb']) || ($data['ptb']['going_to']['program_transport_details_id']==''||$data['ptb']['going_to']['program_transport_details_id']==0) && ($data['ptb']['returning_from']['program_transport_details_id']==''||$data['ptb']['returning_from']['program_transport_details_id']==0)))
              {
                $err .= "<li>Transport field's are required.</li>";
              } 
            }
   
           if ($err != "") {
   
               $err = "<ul><li>The submitted form was invalid. Try submitting again.</li>" . $err . "</ul>";
   
           } else {
              $ptb = null;
              //transport start data
              if($data['is_transport_booked']){
                if($data['ptb']['going_to']['program_transport_details_id'])
                {
                  $ptb['going_to']    = $data['ptb']['going_to'];
                  $ptb['going_to']['departure_date']     = $objMasters->dateFormat($ptb['going_to']['departure_date'],"DT/MN/YR","Y-m-d");
                  $ptb['going_to']['departure_date']      =  strtotime($ptb['going_to']['departure_date']);
                  if($data['ptb']['program_transport_type']==Enum::ProgramTransportTypes()->BOTH_WAY)
                  {
                    $ptb['going_to']['no_of_seats'] = $data['ptb']['both_way']['no_of_seats'];
                  }
                  $TotalBookedPrograms = $objProgramme->getBookedProgramTransportSeats($ptb['going_to']['program_transport_details_id'],$ptb['going_to']['departure_date'],'AND status= "'.Enum::Status()->ACTIVE.'"');
                  $ProgramTransport = $objProgramme->getProgramTransport('',false,$ptb['going_to']['program_transport_details_id'],'is_sold_out = 0 AND adp_id='.$id);
                  $available_seats = $ProgramTransport[0]->no_of_seats - $TotalBookedPrograms[0]->total_seats;

                  if($ProgramTransport[0]->start_date>$ptb['going_to']['departure_date'] || $ProgramTransport[0]->end_date<$ptb['going_to']['departure_date'])
                  {
                    $err .= "<li>Departure Date Is invalid.</li>";
                  }
                  else if($ptb['going_to']['no_of_seats']<=0 || ($available_seats<$ptb['going_to']['no_of_seats']))
                  {
                    $err .= "<li>Volvo Seats Not Available. should not 0</li>";
                  }else{
                    $ptb['going_to']['price'] = $ProgramTransport[0]->price;
                    $ptb['going_to']['total_amount'] = $ProgramTransport[0]->price*$ptb['going_to']['no_of_seats'];
                  }
                  $ptb['going_to']['created'] = time();
                  $ptb['going_to']['modified'] = time();
                  $ptb['going_to']['status']   = Enum::Status()->IN_ACTIVE;
                }
                if($data['ptb']['returning_from']['program_transport_details_id'])
                {
                  $ptb['returning_from']  = $data['ptb']['returning_from'];
                  $ptb['returning_from']['departure_date']     = $objMasters->dateFormat($ptb['returning_from']['departure_date'],null,"Y-m-d");
                  $ptb['returning_from']['departure_date']      =  strtotime($ptb['returning_from']['departure_date']);
                  if($data['ptb']['program_transport_type']==Enum::ProgramTransportTypes()->BOTH_WAY)
                  {
                    $ptb['returning_from']['no_of_seats'] = $data['ptb']['both_way']['no_of_seats'];
                  }
                  $TotalBookedPrograms = $objProgramme->getBookedProgramTransportSeats($ptb['returning_from']['program_transport_details_id'],$ptb['returning_from']['departure_date'],'AND status= "'.Enum::Status()->ACTIVE.'"');
                  $ProgramTransport = $objProgramme->getProgramTransport('',false,$ptb['returning_from']['program_transport_details_id'],'is_sold_out = 0 AND adp_id='.$id);
                  $available_seats = $ProgramTransport[0]->no_of_seats - $TotalBookedPrograms[0]->total_seats;
                  if($ProgramTransport[0]->start_date>$ptb['returning_from']['departure_date'] || $ProgramTransport[0]->end_date<$ptb['returning_from']['departure_date'])
                  {
                    $err .= "<li>Departure Date Is invalid.</li>";
                  }
                  else if($ptb['returning_from']['no_of_seats']<=0 || ($available_seats<$ptb['returning_from']['no_of_seats']))
                  {
                    $err .= "<li>Volvo Seats Not Available.</li>";
                  }else{
                    $ptb['returning_from']['price'] = $ProgramTransport[0]->price;
                    $ptb['returning_from']['total_amount'] = $ProgramTransport[0]->price*$ptb['returning_from']['no_of_seats']; 
                    
                  }
                  $ptb['returning_from']['created'] = time();
                  $ptb['returning_from']['modified'] = time();
                  $ptb['returning_from']['status']   = Enum::Status()->IN_ACTIVE;
                }
                if($data['ptb']['program_transport_type']==Enum::ProgramTransportTypes()->BOTH_WAY)
                {
                  $ProgramTransport = $objProgramme->getProgramTransport($id,false,'','is_sold_out = 0 AND program_transport_type_id='.Enum::ProgramTransportTypes()->BOTH_WAY);
                  if($ProgramTransport!=null && !empty($ProgramTransport))
                  {
                    $setPrice = $ProgramTransport[0]->price/2;
                    if($data['ptb']['both_way']['no_of_seats']>0)
                      {
                        $ptb['going_to']['price'] = $setPrice;
                        $ptb['going_to']['total_amount'] = $setPrice*$ptb['going_to']['no_of_seats'];
                        $ptb['returning_from']['price'] = $setPrice;
                        $ptb['returning_from']['total_amount'] = $setPrice*$ptb['returning_from']['no_of_seats'];
                        $ptb['going_to']['is_both_way'] = 1;
                        $ptb['returning_from']['is_both_way'] = 1;
                      }else{
                        $err .= "<li>Volvo Seats Can Not Be 0.</li>";
                      }
                  }
                }
              }
             
              // tranport end
               $membership_info = $objMembers->get_member_info_by_memberhip_code($data['abk_mem_code']);
   
               if (!$membership_info or $membership_info->status != 1) {
   
                   //$err = 'Sorry this membership code is either invalid or expired.'; // Disabled on 23July2015 as per client instructions
   
               }
   
               if (!$err) {

                   $retVal = $objProgramme->checkBookingSeat($id,$data['abk_reporting_date']);

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

                       //set transport booking
                       if($ptb!=null && !empty($ptb))
                       {
                        foreach ($ptb as $key => $value) {
                          $ptb[$key]['abk_id'] = $data['abk_id'];
                          $booking_amount = $booking_amount + $value['total_amount'];
                          $transportIds[] = $objDB->insert_data("#__program_transport_booking", $ptb[$key]);
                        }
                       }
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
   
                               $st = $objProgramme->checkBookingSeat($bkDetail->abk_prog_id, $data['abk_reporting_date']);
   
                               if ($st != 'N') {
   
                                   $response = array("abk_transaction_id" => "", "abk_PaymentID" => "");
   
                                   $objProgramme->updateBookingOnline($data['abk_id'], $response, $st);
   
                                   $objProgramme->updateOnlineBookingNumbers($data['abk_id']);
   
                                   $_SESSION['program_TransactionID'] = 'Nil';
   
                                   $_SESSION['transaction_response']['bookingid'] = $data['abk_id'];
   
                                   $objProgramme->send_programme_booking_email($bkDetail);
                                    $objProgramme->send_ProgramBooking_sms($bkDetail);
   
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
        //convert to as per ui
       $data['abk_reporting_date'] = $objMasters->dateFormat($data['abk_reporting_date'],"YR-MN-DT","d/m/Y");
       $data['abk_dob'] = $objMasters->dateFormat($data['abk_dob'],"YR-MN-DT","d/m/Y");
       }
      
   }
   

   $page_info = $objPage->get_page_info($page_id);
   
   $node_info = $objMenu->getInfo($node);
   
   $objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);
   
   //$membership_options = $objMembers->list_membership_options($data['plan_code'], 'Individual');
   
   $state_options = $objMasters->list_state_options($data['abk_state']);
   
   $city_options = $objMasters->list_city_options($data['abk_city'], $data['abk_state']);
   
   // set error message
   $MSG->ERROR = $err;
   ?>
<?php include_once(PATH_INCLUDES . "/header.php"); ?>
<div id="body">
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
                                 <li id="booking_details_li" class="ui-state-current"><a href="#program_booking_details">Booking Details</a></li>
                                 <li id="program_personal_info_li" class="ui-state-pending"><a href="#program_personal_info">Personal Info</a></li>
                                 <li class="ui-state-pending"><a href="#program_payment_info">Payment Status</a></li>
                                 <li class="ui-state-pending"><a href="#program_status_info">Confirmation</a></li>
                              </ul>
                           </div>
                           <div id="program_booking_details" class="main_content_area hotel_main_content">
                              <div class="inner_container">
                                 <!--  tab inner section two Start -->
                                 <div class="tab_inner_section hotel_inner_section">
                                    <div class="heading_tab_inner" >
                                       <h5 style="float: left;">Program Details</h5>
                                      
                                    </div>
                                    <div class="tab_inner_body full_width">
                                       <div class="col-lg-12 col-md-12 col-sm-12">
                                          <div class="tour_packages_right_section left_space_40">
                                             <div class="tour_packages_details_top row">
                                                <div class="col-md-12 col-sm-12">
                                                   <div class="top_head_bar  col-md-3 col-sm-3" style=" margin-bottom: 2%;margin-top: 2%; float: left;">
                                                   <figure  style="margin-bottom: 0%; float: left;"> <a href="<?php echo SITE_URL ?>adventure-programme.php?id=<?php echo $prog->adp_id ?>#gal" class="zoom-item" title="Portfolio Item Title"> <img style=" float: left;" src="<?php echo SITE_URL ?>uploads/programme/photo/<?php echo $prog->adp_photo; ?>" alt=""> </a></figure>
                                                </div>
                                                <div class="col-md-6 col-sm-6 prognamewidth">
                                                   <h4  class="prognamewidth"><?php echo $prog->adp_name ?></h4>
                                                   <div class="inludes_hotel_booking prognamewidth" >
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
                                                                  <td class="label_list">Total Price</td>
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
                                                <div class="right_includes_hotel col-lg-3 col-md-3 checkinmargin " style="float: right;">
                                                   <?php 
                                                      $startDateArray = $objMasters->getDateInJson($objMasters->dateFormat($prog->adp_from_date,null,"d-M-Y"),"DD/MM/Y");
                                                      ?>
                                                   <div class="check_in_out_wrap" style="padding-left:15%; padding-right:15%;">
                                                      <div class="check_in marginzero" style="float: left;">
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
                                                      <div class="check_in marginzero" style="float: right;">
                                                         <label>End Date</label>
                                                         <div class="check_in_box">
                                                            <span class="day"><?php echo $endDateArray->Y; ?></span>
                                                            <span class="date"><?php echo $endDateArray->DD; ?></span> 
                                                            <span class="month"><?php echo $endDateArray->MM; ?></span> 
                                                         </div>
                                                      </div>
                                                   </div>
                                                </div>
                                                </div>
                                               
                                                <!-- tab include area Start -->
                                                <div class="full_width package_highlight_section response_width " style=" border-top: 1px solid #d3d3d3; ">
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
                                                                  <span class="includes_text" ><?php echo $prog->adp_cover_services ?>
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
                                    <button type="submit" value="proceed to next step" style="float:right; margin-top: 2%; width:370px; font-size:135%;" id="proceed_0_next_btn" class="btn_green proceed_buttton btns Width100res">proceed to next step <i class="fa fa-chevron-right" style="margin-left:10%;" aria-hidden="true"></i>
                                    </button>
                                 </div>
                                 <!-- proceed button -->
                              </div>
                           </div>
                           <!--------personal information  block start------------>
                           <div id="program_personal_info" class="main_content_area hotel_main_content">
                              <form name="RegisterForm" id="programMemberPersonalInfo" method="post" action="book-program.php" class="RegisterForm" enctype="multipart/form-data">
                              <!--  tab inner section three Start -->
                              <div class="inner_container">
                                  <!-- Start Message section -->
                                  <?php include_once(PATH_INCLUDES."/showMsg.php");?>
                                  <!-- End Message Section -->
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
                                 <div class="col-lg-6 col-md-6 form-group">
                                 <label class="control-label">Reporting Date<span>*</span></label>
                                 <input  type="text" name="abk_reporting_date" id="abk_reporting_date_readonly" class="homeHostelSearchInputComman  form-control " id="abk_reporting_date" placeholder="dd/mm/yyyy" value="<?php echo $data['abk_reporting_date']; ?>" readonly="readonly" >
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group">
                                 <label class="control-label">Membership code<span>*</span></label>
                                <input type="text" name="abk_mem_code" placeholder="eg: 027-UTA01-L0*****" id="abk_mem_code"  value="<?php echo $data['abk_mem_code']; ?>" class="form-control" />
                                 <span class="ValidationErrors" id="abk_mem_code_show_error" style="display:none; color:red;"></span>
                                  <span style="float:right;margin-top:1%;"><u><a href="individual-membership-application.php" class="hoverLink" style="color:blue; ">Become A New Member</a></u><a href="individual-membership-application.php">
                                </a></span>
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group">
                                 <label class="control-label">Name<span>*</span></label>
                                 <input type="text" placeholder=" Name" name="abk_name" id="abk_name" value="<?php echo $data['abk_name']; ?>" class= "form-control ">
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group">
                                 <label class="control-label">Email<span>*</span></label>
                                 <input type="text" placeholder="Email" name="abk_email"   id="abk_email" value="<?php echo $data['abk_email']; ?>" class=" form-control ">
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group" >
                                 <label style="width:100%">Select Gender</label>
                                 <label class="radio-inline">
                                 <input name="abk_gender" id="gender_male" type="radio" class="radiobuton" value="Male" checked="checked">Male
                                 </label>
                                 <label class="radio-inline">
                                 <input  name="abk_gender" id="gender_female" type="radio"  value="Female" <?php echo ($data['gender'] == 'Female') ? ' checked="checked"' : '';?>/>Female
                                 </label>
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group">
                                 <label for="dob"  class="control-label">Date Of Birth</label>
                                 <input  type="text" name="abk_dob" id="abk_dob" value="<?php echo $data['abk_dob']; ?>" readonly="readonly"   class="form-control"/>
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group" >
                                 <label class="control-label">Father/Husband/Spouse<span>*</span></label>
                                 <input type="text" name="abk_father_name" placeholder="Father/Husband/Spouse" id="abk_father_name" maxlength="60" value="<?php echo $data['abk_father_name']; ?>" class="form-control datafield " />
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group">
                                 <label class="control-label" >Address<span>*</span></label>
                                 <input type="text" name="abk_address" id="abk_address" placeholder="Town/City" class="datafield1 form-control " maxlength="50" value="<?php echo $data['abk_address']; ?>"></input>
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group"  >
                                 <label for="state"  class="control-label">State</label>
                                 <select name="abk_state" id="abk_state" class="form-control">
                                 <option value="">Select</option>
                                 <?php echo $state_options ?>
                                 </select>
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group" >
                                 <label for="city"  class="control-label">City</label>
                                 <select name="abk_city" id="abk_city" class="form-control">
                                 <option value="">Select</option>
                                 <?php echo $city_options ?>
                                 </select>
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group">
                                 <label class="control-label">Postal Code<span>*</span></label>
                                 <input type="text" placeholder="Postal code" name="abk_postal_code" maxlength="6" id="abk_postal_code" value="<?php echo $data['abk_postal_code']; ?>" class="datafield  form-control " />
                                 </div>
                                 <div class="col-lg-6 col-md-6  form-group">
                                 <label class="control-label">Contact number<span>*</span></label>
                                 <input type="text"  placeholder="contact number"  name="abk_phone_number" maxlength="15" id="abk_phone_number" value="<?php echo $data['abk_phone_number']; ?>" class="datafield  form-control " />
                                 </div>
                                
                                 <div class="col-lg-12 col-md-12">
                                 <div class="col-lg-4 col-md-4  form-group">
                                 <input type="file" name="abk_photo" id="photograph"  class="filestyle" data-buttonText="Photograph" accept="image/*" data-classInput="input-small" data-classIcon="fa fa-picture-o"/>
                                 <input type="hidden" name="old_photograph" id="old_photograph" value="<?php echo $data['photograph']; ?>" />
                                 <small id="photographHelp" class="form-text text-muted" style="float:left">Please upload a passport size photograph. photo size should be upto 512 KB.</small>
                                 </div>
                                 <div class="col-lg-4 col-md-4  form-group" style="margin-left:2%;">
                                 <input  type="file" class="filestyle" data-buttonText="Residence Proof"  name="abk_residence_proof" accept="image/* application/pdf" id="residence_proof" data-classInput="input-small" data-classIcon="fa fa-file-text-o"/>
                                 <input type="hidden" name="old_residence_proof" id="old_residence_proof" value="<?php echo $data['residence_proof']; ?>" />
                                 </div>
                                 <div class="col-lg-4 col-md-4  form-group" style="margin-right:-6%;">
                                 <input type="file" name="abk_signature" id="abk_signature" data-buttonText="Signature" data-classInput="input-small" class="browse filestyle" />
                                 <span class="form-field-info" title="Scanned Signature Image"><img src="images/icon_info_16x16.png" border="0" />
                                 <small id="photographHelp" accept="image/* application/pdf"class="form-text text-muted" style="float:left">Please upload a Signature size photograph. photo size should be upto 512 KB</small>
                                 </span>
                                 </div>
                                 </div>
                                  <?php
                                  if($prog->is_providing_transport && $prog->ptd!=null && !empty($prog->ptd)){?>
                                 <div class="col-lg-12 col-md-12">
                                 <div class="col-lg-6 col-md-6  form-group">
                                 <label class="control-label" style="width:30%;">Want Volvo Transport</label>
                                 <input type="checkbox" style="margin-top:-6%;width:70%;"   name="is_transport_booked"  id="is_providing_transport" value="1" checked="checked" class="datafield  form-control " />
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group" style="float: right; font-size:169%; ">
                                 <label style="font-size:20px;">Program Fee (in Rupees) : &nbsp;</label>
                                 <i class="fa fa-inr rupeesicon" aria-hidden="true" style=" font-size: 24px !important; color: #ffffff;"></i><input style="float:right;width:20%" type="text" readonly="readonly" id="total_program_price" class="form-control " value="<?php echo $ProgramDetail->adp_price; ?>">  /-
                                 </div>
                                 </div>
                                 <div class="col-lg-12 col-md-12" style="border:1px solid" id="volvo_details_main_div">
                                  <?php if($prog->ptd->both_way!=null && !empty($prog->ptd->both_way) && $prog->ptd->both_way->id!='' && $prog->ptd->both_way->id!=0)
                                  {?>
                                 <div class="col-lg-12 col-md-12">
                                   <div class="col-lg-6 col-md-6  form-group">
                                   <label class="control-label" style="width:30%;">One way</label>
                                    <input type="radio" name="ptb[program_transport_type]" checked="checked" is_both_way="false" id="one_way"  value="<?php echo Enum::ProgramTransportTypes()->GOING_TO;?>"  <?php echo $one_way_radio;?>   lang="MUST" title="Going To" class="inputbox select_way" size="6"/>
                                   </div>
                                    <div class="col-lg-6 col-md-6  form-group">
                                     <label class="control-label" style="width:30%;">Round Trip</label>
                                      <input type="radio" name="ptb[program_transport_type]" id="both_way" value="<?php echo Enum::ProgramTransportTypes()->BOTH_WAY;?>" <?php echo $two_way_radio;?> is_both_way = "true" lang="MUST" title="Returning From" class="inputbox select_way" size="6"   />
                                   </div>
                                   </div>
                                  <?php }
                                  ?>
                                  <div class="col-lg-12 col-md-12" >
                                      <div class="col-lg-6 col-md-6 form-group two_way_price_div" style="display:none">
                                          <label class="control-label">Rounded Price</label>
                                          <input type="text" name="ptb[both_way][price]"  id="returning_price" value="<?php echo $prog->ptd->both_way->price;?>" class="datafield  form-control " />
                                      </div>
                                      <div class="col-lg-6 col-md-6 form-group two_way_price_div" style="display:none">
                                          <label class="control-label">Seats</label>
                                          <input type="text" name="ptb[both_way][no_of_seats]"  id="ptb_both_way_no_of_seats" class="datafield  form-control " min='1' max='<?php echo $prog->ptd->both_way->max_no_of_seats;?>' required="required"/>
                                          <input type="hidden" name="for_both_going" id="for_both_going" is-validate-checked='false'/>
                                          <input type="hidden" name="for_both_returning" id="for_both_returning" is-validate-checked='false'/>
                                      </div>
                                  </div>
                                  <div class="col-lg-12 col-md-12" >
                                   <table width="100%" align="center" cellpadding="2" cellspacing="1" bgcolor="#E2e2e2" style="background:none " class=" table ">
                                      <thead>
                                        <tr style="background-color: #f9f9f9;">
                                            <td>Location</td>
                                           <td> Pickup point</td>
                                            <td> Drop point</td>
                                            <td> Travel Date</td>
                                            <td>Total Seats</td>
                                            <td class="one_way_price_div">Amount</td>
                                            <td>Available</td>
                                            <td class="one_way_price_div"> No of seats</td>
                                             
                                        </tr>
                                      </thead>
                                       <tbody>
                                         <?php if($prog->ptd->going_to!=null && !empty($prog->ptd->going_to)){?>
                                          <tr id="one_way_tr">
                                          <td style="width:20%;">
                                              <input type="checkbox" style="width:41px; float:left;"  name="ptb[going_to][program_transport_details_id]"  value="<?php echo $prog->ptd->going_to->id;?>" class="datafield  form-control one_way_checkbox" to-disabled="both_way_tr" to-enabled ="one_way_tr"/> 
                                              <table style="float: left; margin-left: 50px; margin-top:-35px;text-align:center;">
                                              <tr>
                                               <td>
                                                <?php echo $prog->ptd->going_to->starting_location ; ?>
                                                  
                                                </td>
                                                </tr>
                                                <tr>
                                                <td> <b>To</b> </td>
                                                </tr>
                                                <tr>
                                                <td>
                                                <?php echo $prog->ptd->going_to->end_location ; ?>
                                                  
                                                </td>
                                                </tr>
                                              </table>
                                           </td>
                                          <td style="width:15%;" >
                                                <select  name="ptb[going_to][pickup_point]" id="pickup_point" disabled="disabled" required="required" value="<?php echo $prog->ptd->going_to->pickup_point ; ?>" class="form-control">
                                                <option value="">select pickup </option>
                                                  <?php 
                                                  if($prog->ptd->going_to->pickup_points!=null && !empty($prog->ptd->going_to->pickup_points)){
                                                    foreach ($prog->ptd->going_to->pickup_points as $key => $value) {
                                                      ?>
                                                      <option value="<?php echo $value?>"><?php echo $value?></option>
                                                      <?php
                                                    }
                                                  }?>
                                                </select>
                                           </td>
                                           <td style="width:15%;">
                                                <select  name="ptb[going_to][drop_point]" id="drop_point" disabled="disabled" required="required" value="<?php echo $prog->ptd->going_to->drop_point ; ?>" class="form-control">
                                                <option value="">select drop </option>
                                                  <?php 
                                                  if($prog->ptd->going_to->drop_points!=null && !empty($prog->ptd->going_to->drop_points)){
                                                    foreach ($prog->ptd->going_to->drop_points as $key => $value) {
                                                      ?>
                                                      <option value="<?php echo $value?>"><?php echo $value?></option>
                                                      <?php
                                                    }
                                                  }?>
                                                </select>
                                           </td>
                                           <td  style="width:15%;">
                                                <input type="selectbox"  name="ptb[going_to][departure_date]" disabled="disabled"  id="going_departure_date" value="<?php echo $prog->ptd->going_to->start_date ; ?>" class="datafield  form-control departure_date volvo_departure_date" readonly='readonly' transport-type='going_to'/>
                                            </td>
                                            <td style="width:8%;" >
                                              
                                              <input type="text" disabled="disabled"   name="ptd[going_to][total_seats]"  id="going_to_no_of_seats" value="<?php echo $prog->ptd->going_to->no_of_seats; ?>" readonly='readonly' class="datafield  form-control one_way_no_of_seats" />
                                            </td>
                                           
                                            <td class="one_way_price_div" style="width:8%;" >
                                               <input type="text" disabled="disabled"  name="ptd[going_to][price]"  id="price" value="<?php echo $prog->ptd->going_to->price ; ?>" readonly='readonly' class="datafield  form-control " />
                                            </td>
                                            <td style="width:8%;">
                                             <input type="text" disabled="disabled"   name="ptd[going_to][available_seats]"  id="available_seats" readonly='readonly' value="<?php echo $prog->ptd->going_to->available_seats ; ?>" class="datafield  form-control " />
                                             </td>
                                            <td class="one_way_price_div" style="width:8%;">
                                              <input type="text"  disabled="disabled" name="ptb[going_to][no_of_seats]"  id="returnnig_no_of_seats" min='1' max='<?php echo $prog->ptd->going_to->max_no_of_seats?>' required="required" class="datafield  form-control one_way_no_of_seats" />
                                            </td>
                                             
                                          </tr>
                                          <?php }?>
                                          <?php if($prog->ptd->returning_from!=null && !empty($prog->ptd->returning_from))
                                          {?>
                                          <tr id="both_way_tr">
                                            <td style="width:20%;">
                                                <input type="checkbox" style="width:41px; float:left;"  name="ptb[returning_from][program_transport_details_id]" id="both_way" value="<?php echo $prog->ptd->returning_from->id;?>" <?php echo $two_way_checkbox?> class="datafield  form-control one_way_checkbox" to-disabled="one_way_tr" to-enabled ="both_way_tr"/>
                                            <table style="float: left; margin-left: 50px; margin-top:-35px;text-align:center;">
                                            <tr>
                                             <td>
                                              <?php echo $prog->ptd->returning_from->starting_location ;  ?>
                                                
                                              </td>
                                              </tr>
                                              <tr>
                                              <td> <b>To</b> </td>
                                              </tr>
                                              <tr>
                                              <td>
                                              <?php echo $prog->ptd->returning_from->end_location ; ?></td>
                                              </tr>
                                              
                                            </table>
                                           </td>
                                           
                                         <td style="width:15%;">
                                                <select  name="ptb[returning_from][pickup_point]" id="pickup_point" disabled="disabled" required="required" value="<?php echo $prog->ptd->returning_from->pickup_point ; ?>" class="form-control">
                                                <option value="">select pickup </option>
                                                  <?php 
                                                  if($prog->ptd->returning_from->pickup_points!=null && !empty($prog->ptd->returning_from->pickup_points)){
                                                    foreach ($prog->ptd->returning_from->pickup_points as $key => $value) {
                                                      ?>
                                                      <option value="<?php echo $value?>"><?php echo $value?></option>
                                                      <?php
                                                    }
                                                  }?>
                                                </select>
                                           </td>
                                           <td style="width:15%;">
                                                <select  name="ptb[returning_from][drop_point]" id="drop_point" disabled="disabled" required="required" value="<?php echo $prog->ptd->returning_from->drop_point ; ?>" class="form-control">
                                                <option value="">select drop </option>
                                                  <?php 
                                                  if($prog->ptd->returning_from->drop_points!=null && !empty($prog->ptd->returning_from->drop_points)){
                                                    foreach ($prog->ptd->returning_from->drop_points as $key => $value) {
                                                      ?>
                                                      <option value="<?php echo $value?>"><?php echo $value?></option>
                                                      <?php
                                                    }
                                                  }?>
                                                </select>
                                           </td>
                                            <td style="width:15%;" >
                                                <input type="selectbox"  name="ptb[returning_from][departure_date]" disabled="disabled" id="returning_departure_date" readonly='readonly' value="<?php echo $prog->ptd->returning_from->start_date; ?>" class="datafield  form-control volvo_departure_date " transport-type='returning_from'/></td>
                                            <td style="width:8%;"  >
                                            
                                              <input type="text" name="ptd[returning_from][total_no_of_seats]"  disabled="disabled"  id="no_of_seats" value="  <?php echo $prog->ptd->returning_from->no_of_seats; ?>" class="datafield  form-control " readonly='readonly'/>
                                            </td>
                                           
                                            <td style="width:8%;" class="one_way_price_div">
                                               <input type="text"  name="ptd[returning_from][price]"   id="price" disabled="disabled" value="<?php echo $prog->ptd->returning_from->price ; ?>" readonly='readonly' class="datafield  form-control " />
                                            </td>
                                            <td  style="width:8%;" >
                                               <input type="text"  name="ptd[returning_from][available_seats]"  disabled="disabled" id="available_seats" value="<?php echo $prog->ptd->returning_from->available_seats ; ?>" class="datafield  form-control " readonly='readonly'/>
                                            </td>
                                            <td  style="width:8%;" class="one_way_price_div">
                                              <input type="text" disabled="disabled" name="ptb[returning_from][no_of_seats]" min='1' max='<?php echo $prog->ptd->returning_from->max_no_of_seats;?>' id="no_of_seats" required="required" class="datafield  form-control one_way_no_of_seats" />
                                            </td>
                                             
                                        </tr>
                                      <?php }
                                          ?>
                                       </tbody>
                                    </table>
                                  </div>
                                   <div class="col-lg-6 col-md-6" >
                                     <label style="font-size:20px;">Total Transport Fee (in Rupees) : &nbsp;</label>
                                     <i class="fa fa-inr rupeesicon" aria-hidden="true" style=" font-size: 20px !important;  color: #ffffff;"></i> <input style="width: 20%; float:right" type="text" readonly="readonly" id="total_transport_price" class="form-control "/> /-
                                  </div>
                                  </div>
                                  <?php }?>
                                  <!-- transport div ended -->
                                 <div class="col-lg-6 col-md-6 form-group" style="margin-top:3%;">
                                 <label class="checkbox InputGroup" id="ValidCheckbox" style="float:left">
                                 <input type="checkbox"  name="terms" id="i_agree" value="1"/> I have read this Agreement and agree to the
                                 </label>
                                 <label class="control-label" style="float:left;height:100%;padding-top:2%;">
                                 <a href="javascript:void(0)" style="color:blue;" class="hover " loader-msg="Please Wait While Getting Terms & Conditions........"> &nbsp;Terms & conditions</a>
                                 </label>
                                 </div>
                                 <div class="col-lg-6 col-md-6 form-group" style="float: right; font-size:169%;  margin-top:3%;">
                                 <label style="font-size:20px; margin-left:19%;">Total Amount (in Rupees) : &nbsp;</label>
                                 <i class="fa fa-inr rupeesicon" aria-hidden="true" style=" font-size: 24px !important; color: #ffffff;"></i><input  style="float:right;width:20%;" type="text" id="total_booking_price" readonly="readonly" class="form-control " value="<?php echo $ProgramDetail->adp_price; ?>">  /-
                                 </div>
                                  <input type="hidden" name="abk_country" value="IND" />
                                  <input type="hidden" name="action" value="register" />
                                  <input type="hidden" name="id" value="<?php echo $id; ?>" />
                                 <!--  package_booking_form END --> 
                                 <!--  tab_inner_body end -->
                                 <!--  tab inner three section End --> 
                                 <!-- proceed button -->
                                 <!-- proceed button -->
                                 </div>
                                 </div>
                                 <div class="full_width Inner-buttonDiv">
                                 <button type="submit" style="width:100% !important; font-size:175%; margin-top: 2%;"  id="proceed_1_next_btn" value="proceed to next step" class="btn_green Width100res proceed_buttton btns">proceed to next step</button>
                                 </div>
                                 </div>
                              </div>
                              <!--  inner container end --> 
                           </div>
                           </form>
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
 <!-- BODY end -->'
<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>
<!-- include js -->
<script type="text/javascript" src="<?php echo SITE_PATH_THEME_JS ?>/bootstrap-filestyle.min.js"></script>
<script type="text/javascript" src="<?php echo SITE_PATH_WEBROOT ?>/Scripts/programs.js"/></script>
<?php if($isFromBookingForm){?>
    <script>
      $("#booking_details_li").removeClass('ui-state-current');
      $("#booking_details_li").addClass('ui-state-complete');
      $("#program_personal_info_li").removeClass('ui-state-pending');
      $("#program_personal_info_li").addClass('ui-state-current');
      $( "#tour_booking_tabs" ).tabs({
        active: 1
      });
    </script>
 <?php }?>

 <script type="text/javascript">
  jQuery(function () {

    $(":file").filestyle({classInput: "input-small"});
    jQuery("select#abk_state").change(function () {

        jQuery.getJSON("select_city_ajax.php", {id: jQuery(this).val()}, function (res_data) {

            var city_options = '';

            for (var i = 0; i < res_data.length; i++) {

                city_options += '<option value="' + res_data[i].id + '">' + res_data[i].name + '</option>';

            }

            jQuery("#abk_city").html(city_options);

            jQuery('#abk_city option:first').attr('selected', 'selected');

        })

    });

});

 
 setCalDate("abk_reporting_date",null,"<?php echo $objBase->addDayToDate(@$objMasters->Today(),DATE_FORMAT,1);?>",null);
       $( "#tour_booking_tabs" ).tabs({
        disabled: [ 2, 3 ]
      });
</script>

<script>
$(document).ready(function(){
    $(document).on('blur','#abk_mem_code',function(event){
        event.preventDefault();
        var memberCode = $(this).val();
        if(memberCode==null || memberCode=="undefined"|| memberCode=="")
        {
            return false;
        }else{
            memberCode = memberCode.trim();
        }
        var url = '<?php echo SITE_URL?>/getMemberDetails-ajax.php?memberCode='+memberCode;
        Loader.show("Please Wait While We Varifying Membership Code");
        $.ajax({
            url: url,
            type: 'Post',
            data:'{"memberCode":'+memberCode+'}',
            dataType: 'json',
            cache: false,
            async: false,
            success: function (response) {
                if(response!=null && response !='undefined' && response !="")
                {
                    if(response.status)
                    {
                        $("#abk_mem_code").attr("isItOk",true);
                        $("#abk_mem_code").removeClass("error");
                        $("#abk_mem_code_show_error").hide();
                        autoFill(response.data);
                    }else{
                        $("#abk_mem_code").attr("isItOk",false);
                        $("#abk_mem_code").addClass("error");
                        $("#abk_mem_code_show_error").text(response.msg);
                        $("#abk_mem_code_show_error").css("display","inline");
                    }
                }
            },
            error: function(response)
            {
                $("#abk_mem_code").attr("isItOk",false);
                $("#abk_mem_code").addClass("ErrorField");
                $("#abk_mem_code_show_error").text("something went wrong...");
                $("#abk_mem_code_show_error").css("display","inline");
            }
        });
        Loader.hide();
        return false;
    });
    
    function autoFill(data){
        if(data!=null)
        {
            if(data.Person.LastName!=null)
            {
                $("#abk_name").val(data.Person.FirstName+" "+data.Person.LastName);   
             }else{
                $("#abk_name").val(data.Person.FirstName);   
             }
             
             $("#abk_email").val(data.Person.EmailAddress);
             if(data.Person.GenderName!="" && data.Person.GenderName.toLowerCase()=="male")
             {
                $("#gender_male").prop("checked", true);
             }else if(data.Person.GenderName!="" && data.Person.GenderName.toLowerCase()=="female")
             {
                $("#gender_female").prop("checked", true);
             }

             $("#abk_dob").val(DateFormat(data.Person.DateOfBirth,"YR-MN-DT"));
             if(data.Person.PersonAddress.AddressLineTwo!=null && data.Person.PersonAddress.AddressLineTwo!="")
             {
                $("#abk_address").val(data.Person.PersonAddress.AddressLineOne+" "+data.Person.PersonAddress.AddressLineTwo);   
             }else{
                $("#abk_address").val(data.Person.PersonAddress.AddressLineOne);
             }
             
             $("#abk_postal_code").val(data.Person.PersonAddress.PostalCode); 
             $("#abk_phone_number").val(data.Person.Contact); 
        }
    }
});
</script>
<?php $maxDate = $objBase->addDayToDate($objMasters->Today(),DATE_FORMAT,null,null,-10);
      $selectedDate = $data['abk_dob']!=null && !empty($data['abk_dob'])?$data['abk_dob']:$maxDate;
      if($prog->ptd->going_to->start_date!='')
      {
        $going_departure_date_min  = $objMasters->dateFormat($prog->ptd->going_to->start_date,"YR-MN-DT",DATE_FORMAT);  
      }
      if($prog->ptd->going_to->end_date!='')
      {
        $going_departure_date_max  = $objMasters->dateFormat($prog->ptd->going_to->end_date,"YR-MN-DT",DATE_FORMAT);  
      }
      if($prog->ptd->returning_from->start_date!='')
      {
        $returning_departure_date_min  = $objMasters->dateFormat($prog->ptd->returning_from->start_date,"YR-MN-DT",DATE_FORMAT);  
      }
      if($prog->ptd->returning_from->end_date!='')
      {
        $returning_departure_date_max  = $objMasters->dateFormat($prog->ptd->returning_from->end_date,"YR-MN-DT",DATE_FORMAT);  
      }
?>
<script type="text/javascript">
   
   //SET DATEOF BIRTH CALENDAR
     setCalDate("abk_dob",'<?php echo $selectedDate;?>',null,'<?php echo $maxDate;?>');
     
</script>
<?php if($prog->ptd->going_to!=null && !empty($prog->ptd->going_to)){?>
    <script>
      setCalDate("going_departure_date",'<?php echo $going_departure_date_min;?>','<?php echo $going_departure_date_min;?>','<?php echo $going_departure_date_max;?>');
      </script> 
<?php } ?>
<?php if($prog->ptd->returning_from!=null && !empty($prog->ptd->returning_from)){?>
    <script>
      setCalDate("returning_departure_date",'<?php echo $returning_departure_date_min;?>','<?php echo $returning_departure_date_min;?>','<?php echo $returning_departure_date_max;?>');
      </script> 
<?php } ?>
<script type="text/javascript">
  
    $(document).ready(function(){
        // display volvo div
        $(document).on('change','#is_providing_transport',function(){
             if($(this).is(":checked"))
            {
                $("#volvo_details_main_div").show();
            }
            else
            {
                $("#volvo_details_main_div").hide();
            }
        });

        // select ways type 
        $(document).on('change','.select_way',function(){
            if($('input[name="ptb[program_transport_type]"]:checked').attr('is_both_way')=="true")
            {
                $(".two_way_price_div").show();
                $(".one_way_price_div").hide();
                $("#both_way_tr").find(":input:not(:first)").attr('disabled', false);
                $("#one_way_tr").find(":input:not(:first)").attr('disabled', false);
                $("#both_way_tr").find(":input:not(:first)").attr('required', 'required');
                $("#one_way_tr").find(":input:not(:first)").attr('required', 'required');
                $("#ptb_both_way_no_of_seats").trigger('change');
            }else{
                $(".two_way_price_div").hide();
                $(".one_way_price_div").show();
                $("#both_way_tr").find(":input:not(:first)").attr('disabled', true);
                $("#one_way_tr").find(":input:not(:first)").attr('disabled', true);
                $("#both_way_tr").find(":input:first").attr('checked', false);
                $("#one_way_tr").find(":input:first").attr('checked', false);
                $("#both_way_tr").find(":input:not(:first)").removeAttr('required');
                $("#one_way_tr").find(":input:not(:first)").removeAttr('required');
                $(".one_way_no_of_seats").trigger('blur');
            }
        });
        //enbled and disabled the row
        $(document).on('change','.one_way_checkbox',function(){
          // var to_disabled =  $(this).attr('to-disabled');
          // var to_enabled =  $(this).attr('to-enabled');
          // if($(this).is(':checked') && $('input[name="ptb[program_transport_type]"]:checked').attr('is_both_way')=="false")
          // {
          //   $("#"+to_disabled).find(":input").attr('checked', false);
          //   $("#"+to_disabled).find(":input:not(:first)").attr('disabled', true);
          //   $("#"+to_enabled).find(":input").attr('disabled', false);
          // }else{
          //   $("#"+to_disabled).find(":input:first").attr('disabled', false);
          //   $("#"+to_enabled).find(":input:first").attr('disabled', false);
          //   //$("#"+to_disabled).find(":input").attr('disabled', false);
          //   //$("#"+to_enabled).find(":input").attr('disabled', false);
          // }
          $(".one_way_no_of_seats").trigger('blur');
          $(this).closest('tr').find(":input:not(:first)").attr('disabled', !this.checked);
        });

        //Form Submit by ajax
        $(document).on('blur','.volvo_departure_date',function(event){
          event.preventDefault();
            var date =  $(this).val();
            var ptd_id = $(this).closest('tr').find(":input:first").val();
            var tr_id = $(this).closest('tr').attr('id');
            var url = SITE.URL+"/_getTransportSeats.php";
            var transport_type =  $(this).attr('transport-type');
            var data = {"date":date, "ptd_id":ptd_id};
            Loader.show("Please Wait While We Getting Transport Details.........");
            if(!Validation.isNull(date) && !Validation.isNull(ptd_id))
            {
                $.ajax({
                  url: url,
                  type: "POST",
                  data:data,
                  dataType: 'json',
                  cache: false,
                  async: false,
                  success: function (response) {
                    if(!Validation.isNull(response))
                    {
                      if(response.status==STATUS.SUCESS)
                      {
                        var isBothWay =  'false';
                        $('input[name="ptb[program_transport_type]"]').each(function(){
                          if($(this).attr('is_both_way')=='true')
                          {
                            isBothWay = true;
                            return false;
                          }
                        });
                        if(isBothWay)
                        {
                          if(transport_type=="going_to")
                          {
                            $("#for_both_going").val(response.max_no_of_seats);
                            $("#for_both_going").attr('is-validate-checked','true');
                            if($("#for_both_returning").attr('is-validate-checked')=='true')
                            {
                              if(response.max_no_of_seats<=$("#for_both_returning").val()){
                                $("#ptb_both_way_no_of_seats").attr('max',response.max_no_of_seats);
                              } 
                            }else{
                              $("#ptb_both_way_no_of_seats").attr('max',response.max_no_of_seats);
                            }
                          }else{
                            $("#for_both_returning").val(response.max_no_of_seats);
                            $("#for_both_returning").attr('is-validate-checked','true');
                            if($("#for_both_going").attr('is-validate-checked')=='true')
                            {
                              if(response.available_seats<=$("#for_both_going").val()){
                                  $("#ptb_both_way_no_of_seats").attr('max',response.max_no_of_seats);
                                }
                            }else{
                              $("#ptb_both_way_no_of_seats").attr('max',response.max_no_of_seats);
                            }
                          }
                        }
                        $("#"+tr_id).find('input[name*="[no_of_seats]"]').attr('max',response.max_no_of_seats);  
                        $("#"+tr_id).find('input[name*="[available_seats]"]').val(response.available_seats);
                      }else{
                        AlertMsg(MSG.FAILED,response.msg);
                      }
                    }
                  },
                  error: function(response)
                  {
                    AlertMsg(MSG.FAILED,"Ooops! something went wrong!");
                  }
              });
            }
            Loader.hide();
            return false;
        });

        $(document).on('change','#ptb_both_way_no_of_seats',function(){
          var totalTransportPrice =0;
          if($('input[name="ptb[program_transport_type]"]:checked').attr('is_both_way')=="true"){
            var both_way_price = parseFloat($('input[name="ptb[both_way][price]"]').val());
            var no_of_seats = $(this).val();
            if(parseInt(no_of_seats)>0)
            {
              totalTransportPrice = parseInt(no_of_seats) * both_way_price;
              $("#total_transport_price").val(totalTransportPrice);
            }
          }
          $("#total_transport_price").val(totalTransportPrice);
          setFinalAmount();
        });

        $(document).on('blur','.one_way_no_of_seats',function(){
            var totalTransportPrice = 0;
            if($('input[name="ptb[program_transport_type]"]:checked').attr('is_both_way')=="true"){
              var both_way_price = parseFloat($('input[name="ptb[both_way][price]"]').val());
              var no_of_seats = $("#ptb_both_way_no_of_seats").val();
              if(parseInt(no_of_seats)>0)
              {
                totalTransportPrice = parseInt(no_of_seats) * both_way_price;
              }
            }else{
              $(".one_way_checkbox:checked").each(function(){
                var price = $(this).closest('tr').find('input[name*="[price]"]').val();
                var no_of_seats = $(this).closest('tr').find('input[name*="[no_of_seats]"]').val();  
                if(parseInt(no_of_seats)>0)
                {
                  totalTransportPrice = totalTransportPrice + no_of_seats*price;
                }
              });
            }
            $("#total_transport_price").val(totalTransportPrice);
            setFinalAmount();
        });

        function setFinalAmount()
        {
          var total_transport_price = parseFloat($("#total_transport_price").val());
          var total_program_price = parseFloat($("#total_program_price").val());
          if(total_transport_price!='' && total_transport_price!='NaN')
          {
            $("#total_booking_price").val(total_program_price+total_transport_price);
          }else{
            $("#total_booking_price").val(total_program_price);
          }
        }

        $(document).on('submit',function(){
            var isItOk = $("#abk_mem_code").attr("isItOk");
            if(isItOk=="true")
            {
                $("#abk_mem_code").removeClass("error");
                $("#abk_mem_code_show_error").hide();
                //return true;

            }else{
                $("#abk_mem_code").addClass("error");
                $("#abk_mem_code_show_error").css("display","inline"); 
                //$("#abk_mem_code").focus(); 
                //return true;
            }

          if($("#is_providing_transport").is(":checked") &&  parseInt($(".one_way_checkbox:checked").length)<=0)
            {
              AlertMsg(MSG.FAILED,"Please select at least one way of volvo ");
              return false;
            }else{
              return true;
            }
        });
    }); 
</script>