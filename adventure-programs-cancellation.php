<?php
include_once("includes/framework.php");
$page_id = JRequest::getVar('page_id');
$node = JRequest::getVar('node');
if(!$node){
    $node = $objMenu->get_page_node_id();
}
$data = JRequest::get('post');
$err = "";
if ($data) {
    if ($data['action'] == 'cancel') {
        if ($data['booking_id'] == "" or $data['membership_number'] == "" or $data['dob'] == "") {
            $err = "The submitted form was invalid. Try submitting again";
        } else {
            $bookingData = $objMembers->check_booking_exists($data['booking_id'], $data['membership_number'], $data['dob']);
            if ($bookingData) {
                if ((time() + 1296000) > ( $bookingData->adp_from_date )) { // 1296000 seconds = 15 days
                    $err = "Sorry ! You have crossed cancellation time limit.";
                } else {
                    //die(print_r($bookingData));
                    $data['AccountID'] = $objEBSPay->account_id;
                    $data['SecretKey'] = $objEBSPay->secret_key;
                    $data['Action'] = 'refund';
                    $data['Amount'] = (int) ($bookingData->abk_prog_price * 50) / 100; // 50% of Prgramme Price
                    $data['PaymentID'] = $bookingData->abk_PaymentID;
                    $res = $objEBSPay->cancel_adv_program('https://secure.ebs.in/api/1_0', $data);
                    if ($res) {
                        if ($res['response'] == 'SUCCESS' && $res['refrenceNo']) {
                            $objMembers->cancel_adv_program($res, $data['booking_id']);
                            $_SESSION['CancellationId'] = $res['refrenceNo'];
                        }
                        $_SESSION['cancel_response'] = $res;
                    }
                    $objBase->Redirect('adventure-programs-cancellation.php');
                }
            } else {
                $err = "Sorry ! Given credentials was invalid. Try again";
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
                <h3>Adventure Programs Cancellation</h3>
                <p><i>* Mandatory Fields</i></p>
                <?php if (!empty($err)) { ?> 
                    <p style="background: #E6C0C0; border: #E1830C; padding: 10px;"><?php echo $err; ?></p>
                <?php } ?>
                <?php
                if ($_SESSION['cancel_response']) {
                    echo '<table width="95%" align=center cellspacing=5 style="background: #E6C0C0; border: #E1830C; padding: 10px;">';
                    foreach ($_SESSION['cancel_response'] as $key => $val) {
                        echo '<tr><td>' . $key . '</td><td>' . $val . '</td></tr>';
                    }
                    echo '</table><br />';
                    if ($_SESSION['CancellationId']) {
                        echo '<p>Your Cancellation Id is: <strong>' . $_SESSION['CancellationId'] . '</strong></p>';
                    }
                    #unset($_SESSION['cancel_response']);
                    #unset($_SESSION['CancellationId']);
                }
                ?>
                <form name="CancellationForm" id="CancellationForm" method="post" action="" class="CancellationForm">            
                    <table cellpadding="0" width="100%" cellspacing="0" class="table">                   

                        <tr class="even">
                            <td>Booking Id / Registration Number<span>*</span></td>
                            <td width="65%">
                                <input type="text" name="booking_id" id="booking_id" value="<?php echo $data['booking_id']; ?>" class="datafield" />
                            </td>
                        </tr>
                        <tr class="odd">
                            <td>Membership Number<span>*</span></td>
                            <td width="65%">
                                <input type="text" name="membership_number" id="membership_number" value="<?php echo $data['membership_number']; ?>" class="datafield" />
                            </td>
                        </tr>                       
                        <tr class="even">
                            <td>Date of Birth<span>*</span></td>
                            <td width="65%">
                                <input type="text" name="dob" id="dob" value="<?php echo $data['dob']; ?>" class="datafield" /> <br />e.g. 08/22/1984
                            </td>
                        </tr>
                        <tr class="odd">
                            <td>&nbsp;</td>
                            <td width="65%">
                                <div class="Inner-buttonDiv"><input type="submit" value="Cancel Now" class="addbtn" /></div>                                
                            </td>
                        </tr>
                    </table>                    
                    <input type="hidden" name="action" value="cancel" />
                </form>                              
                <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL ?>jquery.validate/jquery.validate.css" />                
                <script src="<?php echo SITE_URL ?>jquery.validate/jquery.validate.js" type="text/javascript"></script>
                <script src="<?php echo SITE_URL ?>jquery.validate/jquery.validation.functions.js" type="text/javascript"></script>
                <script type="text/javascript">
                    /* <![CDATA[ */
                    jQuery(function(){                        
                        
                        jQuery("#booking_id").validate({
                            expression: "if (VAL != '') return true; else return false;",
                            message: "Please enter the Required field"
                        });
                        jQuery("#membership_number").validate({
                            expression: "if (VAL != '') return true; else return false;",
                            message: "Please enter the Required field"
                        });
                        jQuery("#dob").validate({
                            expression: "if (VAL != '') return true; else return false;",
                            message: "Please enter the Required field"
                        });                        
                        jQuery('.CancellationForm').validated(function(){                           
                            if(confirm('Are you sure you want to cancel your booking ?')){
                                return true;
                            }else{                                
                                return false;
                            }                            
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