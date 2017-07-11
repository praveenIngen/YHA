<?php
include_once("includes/framework.php");
$msg=null;
$data = JRequest::get();
if($data['msg']){
	$objBase->setMessage($data['msg'],1);
}
$objBase->setMetaData("membership-upgrade-payment-canceled", "membership-upgrade-payment-canceled", "membership-upgrade-payment-canceled");
?>
<?php include_once(PATH_INCLUDES . "/header.php"); ?>
<div id="body"><!-- BODY start -->
    <div class="breadcrum">        
        <?php echo $objMenu->get_breadcrumb($node); ?>
    </div>
    <div class="content twoCol">
        <?php include_once(PATH_INCLUDES . "/left-side.php"); ?>
        <div class="rightCol">
            <div class="participate-rightCol"><link rel="stylesheet" href="system/css/system.css" type="text/css" />				
                <h3>Membership upgrade payment confirmation</h3>
				<div id="system-message-container"><?php $objBase->getMessage(); ?></div>
                <p style="background: #D9D9D9; border: #E1830C; padding: 10px;border-radius:5px 5px 5px 5px;">
                    Sorry! Your payment was declined for the following reason:<br /><br />
                    Response Message: <?php echo $_SESSION['transaction_response']['ResponseMessage']?><br /><br />
                    Response Code: <?php echo $_SESSION['transaction_response']['ResponseCode']?><br /><br />
                    Transaction ID: <?php echo $_SESSION['transaction_response']['TransactionID']?><br /><br />
                    Payment ID: <?php echo $_SESSION['transaction_response']['PaymentID']?><br /><br />
                    <?php unset($_SESSION['transaction_response']);?>
                </p>                
            </div>
        </div>
        <div class="clear"><img src="images/spacer.gif" alt="" /></div>        
    </div>    
    <?php echo $objBanner->getBottomBanner(); ?>
    <div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div><!-- BODY end -->
<?php include_once(PATH_INCLUDES . "/footer.php"); ?>