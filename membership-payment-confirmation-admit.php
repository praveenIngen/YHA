<?php
include_once("includes/framework.php");
$data = JRequest::get();
$MerchantRefNo = JRequest::getVar("MerchantRefNo");
$isError = false;
if (isset($data['paymentstatus'])) {

    require('Rc43.php');

    $DR = preg_replace("/\s/", "+", $_GET['DR']);

    $rc4 = new Crypt_RC4($objEBSPay->secret_key);

    $QueryString = base64_decode($DR);

    $rc4->decrypt($QueryString);

    $QueryString = explode('&', $QueryString);

    $response = array();

    $response['TransactionID'] = $_GET["txnid"] ;

    $response['PaymentID'] = $response['TransactionID'];

    $response['MerchantRefNo'] = $_GET["bookingid"] ;

    $response['Amount'] = $_GET["amount"] ;

    $response['ResponseMessage'] = $_GET['status'];
    if (is_array($response) && count($response) > 0) {

        //to check canclation
        //$objBase->Redirect('membership-payment-canceled.php');

        $data['transaction_response'] = serialize($response);

        $data['cdate'] = time();

        $data['transaction_number'] = $response['TransactionID'];

        $data['member_id'] = $response['MerchantRefNo'];

        $data['amount'] = $response['Amount'];

        $payment_history_id = $objDB->insert_data("#__member_payment_history", $data);


                //$_SESSION['membership_card_link'] = $objMembers->print_membership_card($response['MerchantRefNo']);

               

                $objBase->Redirect('membership-payment-confirmation.php?MerchantRefNo='.$objEncryption->safe_b64encode($response['MerchantRefNo']));

            } else {
                    $affected_rows = $objMembers->decline_member_payment($response['MerchantRefNo']);
                    $objBase->Redirect('membership-payment-canceled.php');

            }

        } else {
                $affected_rows = $objMembers->decline_member_payment($response['MerchantRefNo']);
            $objBase->Redirect('membership-payment-canceled.php');
          }
        } 
}