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
            if ($data['security_code'] != $_SESSION['security_code']) {
                $err = "Invalid security code provided, Try Again.";
            } else {
                $data['cdate'] = time();
                $enquiry_id = $objDB->insert_data("#__enquiry_form", $data);
                $objBase->send_enquiry_acknowledgement($data);
                $objBase->send_enquiry_alert($data);
                $objBase->Redirect('affiliate-with-yhai.php?action=submitted', "Thanks for contacting YHAI. We will revert at the earliest", 0);
            }
        }
    }
}
$page_info = $objPage->get_page_info($page_id);
$node_info = $objMenu->getInfo($node);
$objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);
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
                <h3>Affiliate With YHAI</h3>
                <p><i>* Mandatory Fields</i></p><link rel="stylesheet" href="system/css/system.css" type="text/css" />
                <div id="system-message-container"><?php $objBase->getMessage(); ?></div>
                <?php if (!empty($err)) { ?> 
                    <p style="background: #E6C0C0; border: #E1830C; padding: 10px;"><?php echo $err; ?></p>
                <?php } ?>
                <form name="EnquiryForm" id="EnquiryForm" method="post" action="" class="RegisterForm">            
                    <table cellpadding="0" width="100%" cellspacing="0" class="table">
                        <tr class="odd">
                            <td>First Name<span>*</span></td>
                            <td width="65%">
                                <input type="text" name="fname" id="fname" value="<?php echo $data['fname']; ?>" class="datafield" maxlength="50" />
                            </td>

                        </tr>
                        <tr class="even">
                            <td >Last Name<span>*</span></td>
                            <td width="65%">
                                <input type="text" name="lname" id="lname" value="<?php echo $data['lname']; ?>" class="datafield" maxlength="50" />
                            </td>
                        </tr>
                        <tr class="odd">
                            <td>Designation<span></span> </td>
                            <td width="65%">
                                <input type="text" name="designation" id="designation" value="<?php echo $data['designation']; ?>" class="datafield" maxlength="50" />
                            </td>
                        </tr>
                        <tr class="even">
                            <td>Organization<span></span></td>
                            <td width="65%">
                                <input type="text" name="organization" id="organization" value="<?php echo $data['organization']; ?>" class="datafield" maxlength="100" />
                            </td>
                        </tr>
                        <tr class="odd">
                            <td>City<span>*</span></td>
                            <td width="65%">
                                <input type="text" name="city" id="city" value="<?php echo $data['city']; ?>" class="datafield" maxlength="50" />
                            </td>
                        </tr>
                        <tr class="even">
                            <td>Landline No- (STD+ Phone)<span></span></td>
                            <td width="65%">
                                <input type="text" name="phone" id="phone" value="<?php echo $data['phone']; ?>" class="datafield" maxlength="15" />
                            </td>
                        </tr>
                        <tr class="odd">
                            <td>Mobile<span>*</span></td>
                            <td width="65%">
                                <input type="text" name="mobile" id="mobile" value="<?php echo $data['mobile']; ?>" class="datafield" maxlength="10" />
                            </td>
                        </tr>
                        <tr class="even">
                            <td>Email<span>*</span></td>
                            <td width="65%">
                                <input type="text" name="email" id="email" value="<?php echo $data['email']; ?>" class="datafield" />
                            </td>
                        </tr>
                        <tr class="odd">
                            <td>Query Type<span>*</span></td>
                            <td width="65%">
                                <select name="query_type" id="query_type" class="selectbox">                                   
                                    <option value="AffiliatewithYHAI">Affiliate with YHAI</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="odd">
                            <td>Query<span>*</span></td>
                            <td width="65%">
                                <textarea name="query" id="query" class="datafield1" maxlength="500"><?php echo $data['query']; ?></textarea>
                            </td>
                        </tr>
                        <tr class="even">
                            <td>Security Code<span>*</span> </td>
                            <td width="65%">
                                <input type="text" name="security_code" id="security_code" value="" class="datafield" maxlength="6" />
                            </td>
                        </tr>
                        <tr class="odd">
                            <td>&nbsp;</td>
                            <td width="65%">
                                <img src="<?php echo SITE_URL ?>CaptchaSecurityImages.php" />
                            </td>
                        </tr>
                        <tr class="even">
                            <td>&nbsp;</td>
                            <td width="65%">
                                <div class="Inner-buttonDiv"><input type="submit" value="Submit Enquiry" class="addbtn" /></div>                                
                            </td>
                        </tr>
                    </table>                   
                    <input type="hidden" name="action" value="submit_enquiry" />
                </form>

                <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL ?>jquery.validate/jquery.validate.css" />                
                <script src="<?php echo SITE_URL ?>jquery.validate/jquery.validate.js" type="text/javascript"></script>
                <script src="<?php echo SITE_URL ?>jquery.validate/jquery.validation.functions.js" type="text/javascript"></script>
                <script type="text/javascript">
                    /* <![CDATA[ */
                    jQuery(function () {
                        jQuery("#fname").validate({
                            expression: "if (VAL != '' && alpha_only(VAL)) return true; else return false;",
                            message: "Enter the valid First Name"
                        });
                        jQuery("#lname").validate({
                            expression: "if (VAL != '' && alpha_only(VAL)) return true; else return false;",
                            message: "Enter the valid Last Name"
                        });
                        jQuery("#designation").validate({
                            expression: "if (VAL != '' && alpha_only(VAL)==false) return false; else return true;",
                            message: "Enter the valid Designation"
                        });
                        jQuery("#organization").validate({
                            expression: "if (VAL != '' && alpha_only(VAL)==false) return false; else return true;",
                            message: "Enter the valid Org. Name"
                        });
                        jQuery("#city").validate({
                            expression: "if (VAL != '') return true; else return false;",
                            message: "Enter the valid City Name"
                        });

                        jQuery("#mobile").validate({
                            expression: "if (!isNaN(VAL) && VAL.length == 10) return true; else return false;",
                            message: "Enter a valid Mobile Number"
                        });
                        
                        jQuery("#email").validate({
                            expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                            message: "Enter the valid Email ID"
                        });
                        
                        jQuery("#query_type").validate({
                            expression: "if (VAL != '') return true; else return false;",
                            message: "Select a Query Type"
                        });
                        jQuery("#query").validate({
                            expression: "if (VAL != '') return true; else return false;",
                            message: "Enter the valid Query text"
                        });
                        jQuery("#security_code").validate({
                            expression: "if (VAL != '') return true; else return false;",
                            message: "Enter the valid Security Code"
                        });
                        jQuery('.EnquiryForm').validated(function () {
                            jQuery('.EnquiryForm').submit(function () {
                                return true;
                            });
                        });
                    });
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