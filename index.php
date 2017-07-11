<?php
include_once("includes/framework.php");
$node = JRequest::getVar('node');
   if (!$node) {
        $node = $objMenu->get_page_node_id();
        }
$node_info = $objMenu->getInfo($node);
   if ($node_info) {
    $objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);
} else {
    $objBase->setMetaData(
            "Youth Hostels Association of India - Budget Youth Hostels Accommodation | YHAI ", "youth hostels association of india, youth hostels in delhi, youth hostels in india, budget youth hostelling, budget youth accommodations in india, youth adventure travelling in india, youth travel abroad facilities", "Youth Hostels Association of India aims to provide budget Youth Hostelling, Accommodation, & Education in India & also facilitates Youth Adventure Travel in India & Abroad."
    );
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!--Header-top-Start-->
<head>
      <title><?php echo $objBase->getMetaData('title') ?></title>
       <?php include_once(PATH_SHAREDS . "/Elements/_header.php"); ?> 
</head>
<body class="travel_home">
  <div id="travel_wrapper" class="travel_body_loaded">
    <?php include_once(PATH_INCLUDES . "/page_loader.php"); ?>
    <?php include_once(PATH_INCLUDES . "/header-home.php"); ?> 
<!--Header-top-End-->
<!--content body start-->
  <div id="content_wrapper"> 
      <div class="clearfix"></div>
    <!-- Home first slider start -->
          <div class="slider_tab_main">
            <div class="full_width home_slider">
             <div class="example">
               <article class="content" style="width:100%; float:left;">
                  <div id="rev_slider_116_1_wrapper" class="rev_slider_wrapper fullwidthbanner-container" data-alias="layer-animations" style="margin:0px auto;background-color:transparent;padding:0px;margin-top:0px;margin-bottom:0px;">
                  <!-- START REVOLUTION SLIDER 5.0.7 auto mode -->
                     <?php include_once("home_slider.php"); ?>
                  <!-- END Revolution SLIDR -->
                   </div>
                </article>
               </div>
      <!-- section end -->
             </div>
           </div>
    <!-- Home first slider End -->
           <div class="home_tabs_search Width100PC">
          <!-- Hostel Booking Start -->
               <?php include_once(PATH_INCLUDES . "/home-membership-block.php"); ?>
          </div>
          <!-- slider_tab_main end -->
           <!-- program_hostel_combine_start -->
              <?php include_once("program_hostel_combine.php") ?>
            <!-- program_hostel_combine section end -->
            <!-- subscribe section start -->
          <div class="full_width home_subscribe_section">
             <div class="icon_circle_overlay"></div>
                <div class="container">
                   <div class="row">
                     <div class="col-lg-12">
                         <div class="subscribe_middle_part">
                           <h3>Sign up for great deals and travel tips</h3>
                              <div class="sbuscribe_widget_middle">
                              <form id="newsletter" method="POST" class="formSubmitByAjax"  action="newsletter-ajax.php" loader-msg="Subscription being process...">
                               <input type="text" id="email" name="email"  placeholder="ENTER YOUR EMAIL ID HERE" class="send_email "  >
                               <input type="submit" class="submit_subscribe">
                               </form>
                              </div>
                              
                          </div>
                       </div>
                     </div>
                  </div>
              </div>
              <div class="full_width home_subscribe_section">
         <div class="icon_circle_overlay"></div>
            <div class="full_width Travelite_feature_section" style=" background-color: #000000;   opacity: 0.70; padding: 0%;">
               <div class="container">
                  <div class="row" style="padding: 5px;">
                    
                       <div class="row "  style="padding: 2%">
                         <a href="<?php echo Enum::AppLinks()->ANDROID;?>" target="_blank" style="margin-left:263px;">
                           <img class="googleappicon googleiconstyle" src="<?php echo SITE_PATH_WEBROOT;?>/Img/google-play-badge-12.png" alt="google play"   ></a>
                       
                       
                        
                      
                        
                      
                         <a href="<?php echo Enum::AppLinks()->ANDROID;?>" target="_blank" style="margin-left:83px; font-size:35px;">
                            <i class="fa fa-mobile mobileappicon" style="color:#ffffff;" aria-hidden="true"></i> 
                          </a>
                      
                        

                     
                      
                        
                         <a href="<?php echo Enum::AppLinks()->IPHONE;?>" target="_blank"  class="appleiconmarginleft"style="margin-left:-344px;">
                             <img class="appleappicon appleiconstyle" src="<?php echo SITE_PATH_WEBROOT;?>/Img/apples store.png" alt="app store" ></a>
                        
                        </div>
                      </div>
                  
               </div>
             </div>
           </div>
<!-- subscribe section End -->
           </div>
  <!--content body end--> 
<!--footer-top-start-->
     
         <?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>
 <!--footer-top-end-->
<!--Page main section end--> 

<script type="text/javascript" src="<?php echo SITE_PATH_WEBROOT ?>/Scripts/index.js"></script>