<?php

include_once("includes/framework.php");





?>
 
<div class="row">

   <div class="col-lg-7 col-md-7 form-group">
		                                   
  	  <p> 1. Size of the photo/document should not be more than 512kb.</p>
  	  <p>2. Background of the photograph should be White/Blue.</p>
	   
	   <p>3. Specification of the photo should be passport size 3.5 x 4.5 cm.</p>
	   <p>4. Tilted head/selfie photo is not allowed.</p>
   
   
    
   </div>
    <div class="col-lg-5 col-md-5 ">
    <div style="float: left;"><img src="<?php echo SITE_PATH_WEBROOT;?>/Img/Passport_photo_35x45_mm.png"></div>
   <div class="col-lg-5 col-md-5 form-group" style="float: left;">
       <input type="file"  name="photograph" id="photographupload"  class=" filestyle" data-buttonText="Photograph" data-classInput="input-small" accept="image/*" data-classIcon="fa fa-picture-o"/>
       <input type="hidden" name="old_photograph" id="old_photograph" value="" />
       
    </div>
   </div>


                           


    <div class="full_width ">

       <button type="button"  style="width:100%; margin-top: 2%;" id="buttonId"  onclick="SetName();" value="" class="btn_green proceed_buttton btns"><b>Agreed</b></button>
    </div>



<script type="text/javascript" src="<?php echo SITE_PATH_THEME_JS ?>/bootstrap-filestyle.min.js"/></script>
<script type="text/javascript" src="<?php echo SITE_PATH_WEBROOT ?>/Scripts/programs.js"/></script>
<script type="text/javascript">
/*

         function sendValue (s){
            var selvalue = s.value;
            window.opener.document.getElementById('photograph').value = selvalue;
            window.close();
         }*/    


        /* function SetName() {
         	alert ("helllllllllllllooooooooolll");
        if (window.opener != null && !window.opener.closed) {
        		alert ("helllllllllllllooooooooollloooooooooo");
            var txtName = window.opener.document.getElementById("photograph");
      alert ("hellllllllllllllll");
        }
        window.close();
    }*/
</script>