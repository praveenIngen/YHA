<!--A Design by W3layouts
Author: W3layout
Author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<?php
    $code = $_SERVER['REDIRECT_STATUS'];
    $codes = array(
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error'
    );
    $source_url = 'http'.((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 's' : '').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    if (array_key_exists($code, $codes) && is_numeric($code)) {
        die("Error $code: {$codes[$code]}");
    } else {
        die('Unknown error');
    }
?>
<!DOCTYPE HTML>
<html>
<head>
<title>404 error page</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<link href="errorPageHtml/web/css/style.css" rel="stylesheet" type="text/css" media="all" />
</head>
<body>
	
	<div class="wrap">
	
		<div class="content">
		
			<div class="logo">
				<h1><a href="#"><img src="images/logo.png"/></a></h1>
				<span><img src="errorPageHtml/web/images/signal.png"/>Oops! The Page you requested was not found!</span>
			</div>
			
			
			<div class="buttom">
				<div class="seach_bar">
				<?php
echo('<h1>'.$errortitle.'</h1>');
echo('<p>'.$message.'</p>');
?>
					<h2 style="margin-top: 12%; margin-bottom: -2%;font-size: 43px;"><b>404 Error Page</b> </h2>
				<!--<form>
					   <input type="text" value="Search" onfocus="this.value = '';" onblur="if (this.value == '') {this.value = 'Search';}"><input type="submit" value="">
				    </form>-->
				    <p>You can contact us for your further querries on 1111111111111. or can email us on praveencs2015@gmail.com</p>
					 </div>
				</div>
				<div style="text-align: center;margin-top:8%;">
					<button style="width:225px;padding: 1%; background-color: #ff8000;border-radius: 18px;"><span><a href="http://localhost:90/yhastage/">Go To Home Page</a></span></button>
				</div>

			</div>
		<p class="copy_right">&#169; 2014 Template by<a href="http://w3layouts.com" target="_blank">&nbsp;w3layouts</a> </p>
	</div>
	

</body>
</html>