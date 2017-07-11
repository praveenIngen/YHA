<?php
include_once("includes/framework.php");
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
?>