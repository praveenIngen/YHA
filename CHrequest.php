<?php

   if(isset($_POST)){
//Replace this with your secret key from the citrus panel
     $data="";
    $data = $_POST;
    echo "<pre>".print_r($data)."</pre>";
    
  }
    //Need to replace the last part of URL("your-vanityUrlPart") with your Testing/Live URL
    $formPostUrl = "https://sboxcheckout.citruspay.com/ssl/checkout/1o63iipt2i"; 
    //Need to change with your Secret Key
    $secret_key = "666a4cf3c5fdd9da0920b154b8a83870af0d3b52"; 
             
    //Need to change with your Vanity URL Key from the citrus panel
    $vanityUrl = "1o63iipt2i";
 
    //Should be unique for every transaction
    $merchantTxnId = uniqid(); 
    //Need to change with your Order Amount
    $orderAmount = "1.00";
    $currency = "INR";
    $data= $vanityUrl.$orderAmount.$merchantTxnId.$currency;
   

    $securitySignature = hash_hmac('sha1', $data, $secret_key);
    echo($securitySignature);
   
 
  ?>
 
 
 <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">
 <html>
     <head>
         <meta HTTP-EQUIV="Content-Type" CONTENT="text/html;CHARSET=iso-8859-1">
 </head>
  <body>
    <form align="center" id="paymentRequest" method="post" action="https://sboxcheckout.citruspay.com/ssl/checkout/1o63iipt2i">
       <input type="hidden" id="merchantTxnId" name="merchantTxnId" value="<?=$merchantTxnId?>" />
       <input type="hidden" id="orderAmount" name="orderAmount" value="<?=$orderAmount?>" />
       <input type="hidden" id="currency" name="currency" value="<?=$currency?>" />
       <input type="text" id="email" name="email" value="someone@validemail.com" />
       <input type="text" id="phoneNumber" name="phoneNumber" value="9812309816" />        
     
       <input type="hidden" id="notifyUrl" name="notifyUrl" value="https://122.160.200.32:90/yhastage/Response.php" />
       <input type="hidden" id="secSignature" name="secSignature" value="<?=$securitySignature?>" />
       <input type="hidden" id="customParams[0].name" name="customParams[0].name" value="orderId" />
       <input type="hidden" id="customParams[0].value" name="customParams[0].value" value="222" />
       <input type="hidden" id="customParams[1].name" name="customParams[1].name" value="webServer" />
       <input type="hidden" id="customParams[1].value" name="customParams[1].value" value="website" />
      
     </form> 
    </body>
   
 </html>
  <script type="text/javascript">
      document.getElementById('paymentRequest').submit()

    </script>