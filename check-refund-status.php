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
    if ($data['action'] == 'check') {
        if ($data['CancellationId'] == "" ) {
            $err = "The submitted form was invalid. Try submitting again";
        } else {
            $refund_reference = $objMembers->check_refund_reference($data['CancellationId']);
            if ($refund_reference) {
                //die(print_r($bookingData));
                $data['AccountID'] = $objEBSPay->account_id;
                $data['SecretKey'] = $objEBSPay->secret_key;
                $data['Action'] = 'statusByRef';
                $data['RefNo'] = $refund_reference->abk_refund_refrenceNo;
                #$res = $objEBSPay->get_refund_status_adv_program('https://secure.ebs.in/api/1_0', $data);
                if ($res) {
                    $_SESSION['refund_response'] = $res;
                }
                $objBase->Redirect('check-refund-status.php');
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
                <h3>Check Refund Status</h3>
                <p><i>* Mandatory Fields</i></p>
                <?php if (!empty($err)) { ?> 
                    <p style="background: #E6C0C0; border: #E1830C; padding: 10px;"><?php echo $err; ?></p>
                <?php } ?>
                <?php
                if ($_SESSION['refund_response']) {
                    echo '<table width="95%" align=center cellspacing=5 style="background: #E6C0C0; border: #E1830C; padding: 10px;">';
                    foreach ($_SESSION['refund_response'] as $key => $val) {
                        echo '<tr><td>' . $key . '</td><td>' . $val . '</td></tr>';
                    }
                    echo '</table><br />';
                    #unset($_SESSION['refund_response']);
                }
                ?>
                <form name="CancellationForm" id="CancellationForm" method="post" action="" class="CancellationForm">            
                    <table cellpadding="0" width="100%" cellspacing="0" class="table">                   

                        <tr class="even">
                            <td>Enter Cancellation Id<span>*</span></td>
                            <td width="65%">
                                <input type="text" name="CancellationId" id="CancellationId" value="<?php echo $data['CancellationId']; ?>" class="datafield" />
                            </td>
                        </tr>                        
                        <tr class="odd">
                            <td>&nbsp;</td>
                            <td width="65%">
                                <div class="Inner-buttonDiv"><input type="submit" value="Submit Now" class="addbtn" /></div>                                
                            </td>
                        </tr>
                    </table>                    
                    <input type="hidden" name="action" value="check" />
                </form>                              
                <link rel="stylesheet" type="text/css" href="<?php echo SITE_URL ?>jquery.validate/jquery.validate.css" />                
                <script src="<?php echo SITE_URL ?>jquery.validate/jquery.validate.js" type="text/javascript"></script>
                <script src="<?php echo SITE_URL ?>jquery.validate/jquery.validation.functions.js" type="text/javascript"></script>
                <script type="text/javascript">
                    /* <![CDATA[ */
                    jQuery(function(){                        
                        
                        jQuery("#CancellationId").validate({
                            expression: "if (VAL != '') return true; else return false;",
                            message: "Please enter the Required field"
                        });
                        
                        jQuery('.CancellationForm').validated(function(){                           
                            //alert("Use this call to make AJAX submissions.");
                            //return false;
                            jQuery('.CancellationForm').submit(function(){                         
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