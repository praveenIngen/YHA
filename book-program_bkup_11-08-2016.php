<?php

include_once("includes/framework.php");

$page_id = JRequest::getVar('page_id');

$node = JRequest::getVar('node');

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

<div id="body"><!-- BODY start -->

    <div class="breadcrum">

<?php echo $objMenu->get_breadcrumb($node); ?>

    </div>

    <div class="content twoCol">

<?php include_once(PATH_INCLUDES . "/left-side.php"); ?>

        <div class="rightCol">

            <div class="participate-rightCol">

                <h3>Adventure Programme Booking</h3>

                <p><i>* Mandatory Fields</i></p>

                <?php if (!empty($err)) { ?>

                    <div style="background: #E6C0C0; border: #E1830C; padding: 10px;"><?php echo $err . $err_unknokn; ?></div>

<?php } ?>

                <style type="text/css">  *{margin: auto;} </style>

                <script src="<?php echo SITE_URL ?>/system/src/js/jscal2.js"></script>

                <script src="<?php echo SITE_URL ?>/system/src/js/lang/en.js"></script>

                <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL ?>/system/src/css/jscal2.css" />

                <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL ?>/system/src/css/border-radius.css" />

                <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL ?>/system/src/css/steel/steel.css" />

                <form name="RegisterForm" id="RegisterForm" method="post" action="book-program.php" class="RegisterForm" enctype="multipart/form-data">

                    <table cellpadding="0" width="100%" cellspacing="0" class="table">

                        <tr class="odd">

                            <td>Programme  Detail</td>

                            <td width="65%">

                                <?php echo $ProgramDetail->adp_name; ?><br>

                                <?php echo $ProgramDetail->adp_report_point ?> - <?php echo $objProgramme->showProgramDuration($ProgramDetail->adp_duration, $ProgramDetail->adp_from_date, $ProgramDetail->adp_to_date); ?><br />

<?php echo $ProgramDetail->adp_period ?>

                            </td>

                        </tr>

                        <tr class="even">

                            <td>Reporting Date <span>*</span> </td>

                            <td width="65%">

                                <input type="text" name="abk_reporting_date" id="abk_reporting_date" value="<?php echo $data['abk_reporting_date']; ?>" class="datafield" readonly="readonly" />

                            </td>

                        </tr>

                        <tr class="odd">

                            <td>Membership Code </td>

                            <td width="65%">

                                <input type="text" name="abk_mem_code" id="abk_mem_code" maxlength="30" value="<?php echo $data['abk_mem_code']; ?>" class="datafield" />

                            </td>

                        </tr>

                        <tr class="even">

                            <td>Name<span>*</span></td>

                            <td width="65%">

                                <input type="text" name="abk_name" id="abk_name" maxlength="50" value="<?php echo $data['abk_name']; ?>" class="datafield" />

                            </td>

                        </tr>

                        <tr class="odd">

                            <td>Email<span>*</span></td>

                            <td width="65%">

                                <input type="text" name="abk_email"  maxlength="80" id="abk_email" value="<?php echo $data['abk_email']; ?>" class="datafield" />

                            </td>

                        </tr>

                        <tr class="even">

                            <td>Gender<span>*</span></td>

                            <td width="65%">

                                <div class="radiobox"><input name="abk_gender" id="gender_male" type="radio" class="radiobuton" value="Male" checked="checked" /></div> <div class="radio-txt"><label for="gender_male">Male</label></div>

                                <div class="radiobox"><input name="abk_gender" id="gender_female" type="radio" class="radiobuton" value="Female" <?php echo ($data['abk_gender'] == 'Female') ? ' checked="checked"' : ''; ?> /></div> <div class="radio-txt"><label for="gender_female">Female</label></div>

                            </td>

                        </tr>

                        <tr class="odd">

                            <td>Date of Birth<span>*</span></td>

                            <td width="65%">

                                <input type="text" name="abk_dob" id="dob" value="<?php echo $data['abk_dob']; ?>" class="datafield" readonly="readonly" />

                            </td>

                        </tr>

                        <tr class="even">

                            <td>Father/Husband/Spouse<span>*</span></td>

                            <td width="65%">

                                <input type="text" name="abk_father_name" id="abk_father_name" maxlength="60" value="<?php echo $data['abk_father_name']; ?>" class="datafield" />

                            </td>

                        </tr>

                        <tr class="odd">

                            <td>Address<span>*</span></td>

                            <td width="65%">

                                <textarea name="abk_address" id="abk_address" class="datafield1" maxlength="150"><?php echo $data['abk_address']; ?></textarea>

                            </td>

                        </tr>

                        <tr class="even">

                            <td>State<span>*</span></td>

                            <td width="65%">

                                <select name="abk_state" id="abk_state" class="selectbox">

                                    <option value="">Select</option>

<?php echo $state_options ?>

                                </select>

                            </td>

                        </tr>

                        <tr class="odd">

                            <td>City<span>*</span></td>

                            <td width="65%">

                                <select name="abk_city" id="abk_city" class="selectbox" onchange="loadOtherBox(this.value);">

                                    <option value="">Select</option>

<?php echo $city_options ?>

                                </select>

                                <span id="other_box" style="display:none"><input type="text" name="other_city" id="other_city" value="" maxlength="50" /></span>

                                <script type="text/javascript" charset="utf-8">

                                    jQuery(function () {

                                        jQuery("select#abk_state").change(function () {

                                            jQuery.getJSON("select_city_ajax.php", {id: jQuery(this).val()}, function (res_data) {

                                                var city_options = '';

                                                for (var i = 0; i < res_data.length; i++) {

                                                    city_options += '<option value="' + res_data[i].id + '">' + res_data[i].name + '</option>';

                                                }

                                                jQuery("#abk_city").html(city_options);

                                                jQuery('#abk_city option:first').attr('selected', 'selected');

                                            })

                                        })

                                    })

                                    function loadOtherBox(val) {

                                        if (val == "others") {

                                            document.getElementById('other_box').style.display = '';

                                        } else {

                                            document.getElementById('other_box').style.display = 'none';

                                        }

                                    }

                                </script>

                            </td>

                        </tr>

                        <tr class="even">

                            <td>Postal Code<span>*</span></td>

                            <td width="65%">

                                <input type="text" name="abk_postal_code" maxlength="6" id="abk_postal_code" value="<?php echo $data['abk_postal_code']; ?>" class="datafield" />

                            </td>

                        </tr>

                        <tr class="odd">

                            <td>Contact Number<span>*</span></td>

                            <td width="65%">

                                <input type="text" name="abk_phone_number" maxlength="15" id="abk_phone_number" value="<?php echo $data['abk_phone_number']; ?>" class="datafield" />

                            </td>

                        </tr>

						<?php if ($ProgramDetail->adp_handicap_price > 0): ?>

                            <tr class="even">

                                <td>Handicap Discount</td>

                                <td><input type="checkbox" name="abk_handicap" id="abk_handicap" value="1"  /></td>

                            </tr>

                            <tr class="even handicap_rows">

                                <td>Handicap Certificate</td>

                                <td width="65%">

                                    <input type="file" name="abk_handicap_certificate" id="abk_handicap_certificate" />                                    

                                </td>

                            </tr>

                            <tr class="even handicap_rows">

                                <td>&nbsp;</td>

                                <td><span><span>Upload Dr. certified handicap medical certificate. photo size should be upto 512 KB</span></span></td>

                            </tr>

                            <tr class="even handicap_rows">

                                <td>Handicap Photo</td>

                                <td width="65%">

                                    <input type="file" name="abk_handicap_photo" id="abk_handicap_photo" />                                    

                                </td>

                            </tr>

                            <tr class="even handicap_rows">

                                <td>&nbsp;</td>

                                <td><span><span>Upload full photo graph to see you are handicap from whatever area. photo size should be upto 512 KB</span></span></td>

                            </tr>

                            <tr class="odd" id="adp_handicap_price" style="display:none">

                                <td>Programme Fee (in Rupees)</td>

                                <td width="65%">

                                    Rs. <?php echo $ProgramDetail->adp_handicap_price; ?> /-

                                </td>

                            </tr>

<?php endif; ?>

                        <tr class="odd" id="adp_price">

                            <td>Programme Fee (in Rupees)</td>

                            <td width="65%">

                                Rs. <?php echo $ProgramDetail->adp_price; ?> /-

                            </td>

                        </tr>

                        <tr class="even">

                            <td>Photograph <span>*</span></td>

                            <td width="65%">

                                <input type="file" name="abk_photo" id="abk_photo" />

                                <!--<span class="form-field-info" title="Passport Size Image"><img src="images/icon_info_16x16.png" border="0" /></span>-->

                            </td>

                        </tr>

                        <tr class="even">

                            <td>&nbsp;</td>

                            <td><span><span>Please upload a passport size photograph. photo size should be upto 512 KB</span></span></td>

                        </tr>

                        <tr class="odd">

                            <td>Residence Proof <span>*</span></td>

                            <td width="65%">

                                <input type="file" name="abk_residence_proof" id="abk_residence_proof" class="datafield" style="border:none;background:none;"/>

                                <input type="hidden" name="old_abk_residence_proof" id="old_abk_residence_proof" value="<?php echo $data['abk_residence_proof']; ?>" />

                            </td>

                        </tr>                        

                        <tr class="even">

                            <td>Signature</td>

                            <td width="65%">

                                <input type="file" name="abk_signature" id="abk_signature" />

                                <!--<span class="form-field-info" title="Scanned Signature Image"><img src="images/icon_info_16x16.png" border="0" /></span>-->

                            </td>

                        </tr>

                        <tr class="even">

                            <td>&nbsp;</td>

                            <td><span><span>Please upload a Signature size photograph. photo size should be upto 512 KB</span></span></td>

                        </tr>

                        <tr class="odd">

                            <td>&nbsp;</td>

                            <td>

                                <label id="ValidCheckbox" class="InputGroup">

                                    <input type="checkbox" id="i_agree" name="i_agree" value="1" /> I have read this Agreement and agree to the terms and conditions. 

                                </label>

                                <p class="terms-participate"><?php echo $ProgramDetail->adp_terms; ?></p>

                            </td>

                        </tr>

                        <tr class="add">

                            <td>&nbsp;</td>

                            <td width="65%">

                                <div class="Inner-buttonDiv"><input type="submit" value="Submit" class="addbtn" /></div>

                            </td>

                        </tr>

                    </table>

                    <input type="hidden" name="abk_country" value="IND" />

                    <input type="hidden" name="action" value="register" />

                    <input type="hidden" name="id" value="<?php echo $id; ?>" />

                </form>                

                <script type="text/javascript">

                    function removeValidateClass(colId) {

                        obj = document.getElementById(colId);

                        if (obj.value != "") {

                            FormID = 'RegisterForm';

                            for (var i = 0; i < ValidationErrors[FormID].length; i++) {

                                if (ValidationErrors[FormID][i] == obj) {

                                    ValidationErrors[FormID].splice(i, 1);

                                    jQuery("#" + colId).next('.ValidationErrors').fadeOut("fast", function () {

                                        jQuery(this).remove();

                                    });

                                    jQuery("#" + colId).removeClass('ErrorField');

                                }

                            }

                        }

                    }

                    //<![CDATA[

                    Calendar.setup({

                        inputField: "dob",

                        trigger: "dob",

                        onSelect: function () {

                            this.hide();

                            removeValidateClass('dob');

                        },

                        dateFormat: "%Y-%m-%d",

                        max: '<?php echo date("Y-m-d", strtotime("-5 Years")); ?>'

                    });

                    //]]>

                    //<![CDATA[

                    Calendar.setup({

                        inputField: "abk_reporting_date",

                        trigger: "abk_reporting_date",

                        onSelect: function () {

                            this.hide();

                            removeValidateClass('abk_reporting_date')

                        },

                        dateFormat: "%Y-%m-%d",

                        min: '<?php echo date("Y-m-d"); ?>'

                    });

                    //]]>

                </script>

                <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL ?>jquery.validate/jquery.validate.css" />

                <script src="<?php echo SITE_URL ?>jquery.validate/jquery.validate.js" type="text/javascript"></script>

                <script src="<?php echo SITE_URL ?>jquery.validate/jquery.validation.functions.js" type="text/javascript"></script>

                <script type="text/javascript">

                    /* <![CDATA[ */

                    jQuery(function () {

                        jQuery("#abk_reporting_date").validate({

                            expression: "if (VAL != '') return true; else return false;",

                            message: "Select a valid Reporting Date"

                        });

                        /*

                         jQuery("#abk_mem_code").validate({

                         expression: "if (VAL != '') return true; else return false;",

                         message: "Enetr a Valid Membership Code"

                         });*/

                        jQuery("#abk_name").validate({

                            expression: "if (VAL != '' && alpha_only(VAL)) return true; else return false;",

                            message: "Enter the valid Name"

                        });

                        jQuery("#abk_email").validate({

                            expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",

                            message: "Enter the valid Email ID"

                        });

                        jQuery("#dob").validate({

                            expression: "if (VAL != '') return true; else return false;",

                            message: "Select a valid DOB"

                        });

                        jQuery("#abk_father_name").validate({

                            expression: "if (VAL != '' && alpha_only(VAL)) return true; else return false;",

                            message: "Enter the valid guardian Name"

                        });

                        jQuery("#abk_address").validate({

                            expression: "if (VAL != '') return true; else return false;",

                            message: "Enter the valid Address"

                        });

                        jQuery("#abk_state").validate({

                            expression: "if (VAL != '') return true; else return false;",

                            message: "Select A State"

                        });

                        jQuery("#abk_city").validate({

                            expression: "if (VAL != '') return true; else return false;",

                            message: "Select A City"

                        });

                        jQuery("#abk_postal_code").validate({

                            expression: "if (!isNaN(VAL) && VAL != '') return true; else return false;",

                            message: "Enter a valid Postal Code"

                        });

                        jQuery("#abk_phone_number").validate({

                            expression: "if (VAL != '') return true; else return false;",

                            message: "Enter a valid phone Number"

                        });

                        jQuery("#abk_residence_proof").validate({

                            expression: "if (VAL == '' || !checkFileExt(VAL)) return false; else return true;",

                            message: "Upload your valid residence proof"

                        });

                        jQuery("#abk_photo").validate({

                            expression: "if (VAL == '' || !checkFileExt(VAL)) return false; else return true;",

                            message: "Upload your valid photograph"

                        });

                        jQuery("#abk_signature").validate({

                            expression: "if(VAL != '' && !checkFileExt(VAL)) return false; else return true;",

                            message: "Upload your valid signature"

                        });

                        jQuery("#ValidCheckbox").validate({

                            expression: "if (isChecked(SelfID)) return true; else return false;",

                            message: ""

                        });



                        /*

                         * Validate Handicap Fields

                         */

                        jQuery("#abk_handicap_certificate").validate({

                            expression: "if (isCheckedHandicap(VAL) && (VAL == '' || !checkFileExt(VAL))) return false; else return true;",

                            message: "Upload your valid handicap certificate"

                        });

                        jQuery("#abk_handicap_photo").validate({

                            expression: "if (isCheckedHandicap(VAL) && (VAL == '' || !checkFileExt(VAL))) return false; else return true;",

                            message: "Upload your valid handicap photo"

                        });





                        jQuery('.RegisterForm').validated(function () {

                            jQuery('.RegisterForm').submit(function () {

                                return true;

                            });

                        });

                        /*

                         * Check The Handicap Discount 

                         */

                        jQuery('#abk_handicap').click(function () {

                            if (jQuery("#abk_handicap").is(':checked')) { // checked

                                jQuery("#adp_handicap_price").show();

                                jQuery("#adp_price").hide();

                            } else { // unchecked

                                jQuery("#adp_handicap_price").hide();

                                jQuery("#adp_price").show();

                            }

                        });

                    });

                    function check_terms() {

                        //console.log('check_terms');

                        return jQuery('#i_agree').prop('checked');

                    }

                    function isCheckedHandicap() {

                        return jQuery('#abk_handicap').prop('checked');

                    }

                    function checkFileExt(fileValue)

                    {

                        var extArray = new Array('.jpg', '.jpeg', '.png', '.gif');

                        var allowSubmit = false;

                        ext = fileValue.slice(fileValue.lastIndexOf(".")).toLowerCase();

                        for (var i = 0; i < extArray.length; i++) {

                            if (extArray[i] == ext) {

                                allowSubmit = true;

                                break;

                            }

                        }

                        //alert(allowSubmit);

                        if (allowSubmit == true)

                            return true;

                        else

                            return false;

                    }

                    /* ]]> */

                </script>

            </div>

        </div>

        <div class="clear"><img src="images/spacer.gif" alt="" /></div>

    </div>

<?php echo $objBanner->getBottomBanner(); ?>

    <div class="clear"><img src="images/spacer.gif" alt="" /></div>

</div><!-- BODY end -->

<?php include_once(PATH_INCLUDES . "/footer.php"); ?>