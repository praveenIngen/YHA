<?php
include_once("includes/framework.php");
$data = JRequest::get();
$objBase->setMetaData("membership-renew-payment-confirmation", "membership-renew-payment-confirmation", "membership-renew-payment-confirmation");
if (isset($data['DR']) or isset($_SESSION['membership_TransactionID'])) {
    require('Rc43.php');
    $DR = preg_replace("/\s/", "+", $_GET['DR']);
	$objEBSPay->secret_key = 'ebskey';
    $rc4 = new Crypt_RC4($objEBSPay->secret_key);
    $QueryString = base64_decode($DR);
    $rc4->decrypt($QueryString);
    $QueryString = explode('&', $QueryString);
    $response = array();
    foreach ($QueryString as $param) {
        $param = explode('=', $param);
        $response[$param[0]] = urldecode($param[1]);
    }

    if (is_array($response) && count($response) > 0 && $response['ResponseMessage']) {
        $_SESSION['transaction_response'] = $response;
        $data['transaction_response'] = serialize($response);
        $data['cdate'] = time();
        $data['transaction_number'] = $response['TransactionID'];
        $data['member_id'] = $response['MerchantRefNo'];
        $data['amount'] = $response['Amount'];
        $payment_history_id = $objDB->insert_data("#__member_payment_history", $data);
        if ($response['ResponseCode'] == 0 && $response['ResponseMessage'] == 'Transaction Successful' && $response['MerchantRefNo'] > 0 && $response['Amount'] > 0) {
            if ($objMembers->verify_payment($response['MerchantRefNo'], $response['Amount'])) {
                $affected_rows = $objMembers->approve_renew_membership_plan($response['MerchantRefNo'], $response);
                unset($_SESSION['member_register']);
                $_SESSION['membership_TransactionID'] = $response['TransactionID'];
                $_SESSION['membership_card_link'] = $objMembers->print_membership_card($response['MerchantRefNo']);
                $_SESSION['membership_web_message'] = $objMembers->get_membership_web_message($response['MerchantRefNo'], $_SESSION['membership_card_link']);
                $objBase->Redirect('membership-renew-payment-confirmation.php?TransactionID=' . $response['TransactionID']);
            } else {
                $objBase->Redirect('membership-renew-payment-canceled.php?msg=payment-amount-miss-match');
            }
        } else {
            $objBase->Redirect('membership-renew-payment-canceled.php');
        }
    }
    ?>
    <?php include_once(PATH_INCLUDES . "/header.php"); ?>
    <div id="body">
        <!-- BODY start -->
        <div class="breadcrum"> <?php echo $objMenu->get_breadcrumb($node); ?> </div>
        <div class="content twoCol">
            <link href="<?php SITE_URL; ?>css/cards.css" rel="stylesheet" media="all" type="text/css" />
            <?php include_once(PATH_INCLUDES . "/left-side.php"); ?>
            <div class="rightCol">
                <div class="participate-rightCol">
                    <h3>membership-renew-payment-confirmation</h3>
                    <?php echo $_SESSION['membership_web_message']; ?>
                </div>
            </div>
            <div class="clear"><img src="images/spacer.gif" alt="" /></div>
        </div>
        <?php echo $objBanner->getBottomBanner(); ?>
        <div class="clear"><img src="images/spacer.gif" alt="" /></div>
    </div>
    <!-- BODY end -->
    <?php include_once(PATH_INCLUDES . "/footer.php"); ?>
<?php } else { ?>
    <?php include_once(PATH_INCLUDES . "/header.php"); ?>
    <div id="body">
        <!-- BODY start -->
        <div class="breadcrum"> <?php echo $objMenu->get_breadcrumb($node); ?> </div>
        <div class="content twoCol">
            <?php include_once(PATH_INCLUDES . "/left-side.php"); ?>
            <div class="rightCol">
                <div class="participate-rightCol">
                    <h3>membership-renew-payment-confirmation</h3>
                    <p> Oops!</p>
                    <p> It seems an error has occured, the page you are trying to reach is not accessible.</p>
                    <p>Error!</p>
                    <p> This error has occured for one of the following reasons :<br />
                        <br />
                        (a) You have used Back/Forward/Refresh button of your Browser.<br />
                        (b) You are accessing some links from History after logging out from the system.<br/>
                        (c) Either you don't have cookies support in your browser or cookies not set.<br />
                        (d) You have exceeded the session time out.</p>
                </div>
            </div>
            <div class="clear"><img src="images/spacer.gif" alt="" /></div>
        </div>
        <?php echo $objBanner->getBottomBanner(); ?>
        <div class="clear"><img src="images/spacer.gif" alt="" /></div>
    </div>
    <!-- BODY end -->
    <?php include_once(PATH_INCLUDES . "/footer.php"); ?>
<?php } ?>