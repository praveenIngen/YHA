<?php
include_once("includes/framework.php");
$objMembers->CheckLogin();
$data = JRequest::get();
$err = "";
$err = "";
if ($data) {
    if ($data['action'] == 'changepassword') {
        if ($data['old_password'] == "" or $data['password'] != $data['password2']) {
            $err = "The submitted form was invalid. Try submitting again";
        } else {
            if ($objMembers->verify_member_password($data['old_password'])) {
                $data['udate'] = time();
                $data['password'] = $objEncryption->encode($data['password']);
                $affected_rows = $objDB->update_data("#__member", "member_id", $data, $_SESSION['MemberData']['login']->member_id);
                if ($affected_rows) {
                    $objMembers->changePasswordAlert2Member($_SESSION['MemberData']['login']->member_id);
                    $objBase->Redirect('change-password.php', "You have successfully changed your password.", 0);
                    exit;
                } else {
                    //                
                }
            } else {
                $err = "The submitted Old Password was invalid. Try Valid Password.";
            }
        }
    }
}$objBase->setMetaData("My Account:: Change Password", "Change Password", "Change Password");
$member_plans = $objMembers->list_member_plans($_SESSION['MemberData']['login']->email);
//print "<pre>";
//print_r($member_plans);
//print "<pre>"; 
?>
<?php include_once(PATH_INCLUDES . "/header-inner.php"); ?>
<div id="body">
    <!-- BODY start -->
    <div class="breadcrum"> <?php echo $objMenu->get_breadcrumb($node); ?> </div>
    <div class="content twoCol">
        <?php include_once(PATH_INCLUDES . "/left-side.php"); ?>
        <div class="rightCol">
            <div class="dashBoard">
                <link rel="stylesheet" href="system/css/system.css" type="text/css" />
                <div class="welcome">Welcome <span><strong><?php echo $_SESSION['MemberData']['login']->email; ?></strong></span></div>
                <div id="system-message-container">
                    <?php $objBase->getMessage(); ?>
                </div>
                <?php if (!empty($err)) { ?>
                    <p style="background: #E6C0C0; border: #E1830C; padding: 10px;"><?php echo $err; ?></p>
                <?php } ?>
                <ul class="tab">
                    <li><a href="my-account.php"><span>My Account</span></a></li>
                    <li class="active"><a href="change-password.php"><span>Change Password</span></a></li>
                    <li><a href="my-bookings.php"><span>Booking & Financial</span></a></li>
                </ul>
                <div class="tabContentBottom">
                    <div class="tabContent">
                        <h3><span>Change Password</span></h3>
                        <p><i>* Mandatory Fields</i></p>
                        <div class="changePwsd">
                            <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL ?>jquery.validate/jquery.validate.css" />
                            <script src="<?php echo SITE_URL ?>jquery.validate/jquery.validate.js" type="text/javascript"></script>
                            <script src="<?php echo SITE_URL ?>jquery.validate/jquery.validation.functions.js" type="text/javascript"></script>
                            <script type="text/javascript">
                                jQuery(document).ready(function() {
                                    jQuery("#old_password").validate({
                                        expression: "if (VAL != '') return true; else return false;",
                                        message: "Please enter old password"
                                    });
                                    jQuery("#password").validate({
                                        expression: "if (VAL.length > 5 && VAL) return true; else return false;",
                                        message: "Please enter a valid password"
                                    });
                                    jQuery("#password2").validate({
                                        expression: "if ((VAL == jQuery('#password').val()) && VAL) return true; else return false;",
                                        message: "Confirm password is not matching with new pasword."
                                    });
                                    jQuery('.frmChangePassword').validated(function() {
                                        //alert("Use this call to make AJAX submissions.");                                        
                                        //return false;                                    
                                    });
                                });
                            </script>
                            <form name="frmChangePassword" id="frmChangePassword" method="post" action="" class="frmChangePassword">
                                <ul class="form">
                                    <li>
                                        <div class="label">Old Password*</div>
                                        <div class="dataField">
                                            <input name="old_password" id="old_password" type="password" class="textBox" />
                                        </div>
                                    </li>
                                    <li>
                                        <div class="label">New Password*</div>
                                        <div class="dataField">
                                            <input name="password" id="password" type="password" class="textBox" />
                                        </div>
                                    </li>
                                    <li>
                                        <div class="label">Confirm Password*</div>
                                        <div class="dataField">
                                            <input name="password2" id="password2" type="password" class="textBox" />
                                        </div>
                                    </li>
                                    <li>
                                        <div class="label">&nbsp;</div>
                                        <div class="dataField">
                                            <div class="buttonDiv">
                                                <input type="submit" value="Submit" class="btn" />
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <input type="hidden" name="action" value="changepassword" />
                                <input type="hidden" name="member_id" value="<?php echo $_SESSION['MemberData']['login']->member_id ?>" />
                            </form>
                        </div>
                        <div class="clear"><img src="images/spacer.gif" alt="" /></div>
                    </div>
                </div>
                <div class="clear"><img src="images/spacer.gif" alt="" /></div>
            </div>
        </div>
        <div class="clear"><img src="images/spacer.gif" alt="" /></div>
    </div>
    <?php echo $objBanner->getBottomBanner(); ?>
    <div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div>
<!-- BODY end -->
<?php include_once(PATH_INCLUDES . "/footer.php"); ?>