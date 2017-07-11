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
<?php include_once(PATH_INCLUDES . "/header-home.php"); ?>
<div id="body"><!-- BODY start -->
    <div class="mainBanner"><!-- MAIN BANNER start -->
        <div class="bookBlock"><!-- BOOK BLOCK start -->
            <h1>Book a Hostel</h1>
            <div class="tabBlock"><!-- TABBING start -->
                <ul class="hostelTab tab">
                    <li><a href="#india"><span>India</span></a></li>
                    <li><a id="International_href" href="http://www.hihostels.com/affiliates/hibooknow.php?lang=E&linkid=980076" target="_blank"><span>International</span></a></li>
                </ul>
                <div class="tab_container blackBox">
                    <div class="blackBoxBottom">
                        <div class="blackBoxMid">
                            <div id="india" class="tab_content1">
                                <?php include_once("mod_bookahostelindia.php"); ?>
                                <div class="clear"><img src="images/spacer.gif" alt="" /></div>
                            </div>
                            <div id="international" class="tab_content1">
                                <p>International</p>
                                <div class="clear"><img src="images/spacer.gif" alt="" /></div>                                                            
                            </div>
                        </div>
                    </div>
                    <div class="clear"><img src="images/spacer.gif" alt="" /></div>  
                </div>
                <div class="support">
                    <span class="callNow">Call Now <span class="number">7827999000</span></span>
                <!--<a href="#" class="callNow" onClick="return false;">Call Now <span class="number">011 45999000</span></a>
                <a href="#" class="liveChat">Live Chat</a>-->
                    <a href="<?php echo SITE_URL ?>enquiry-form.php" class="memberSupport">Member Support</a>
                </div>
            </div><!-- TABBING end -->
        </div><!-- BOOK BLOCK end -->
        <?php include_once(PATH_INCLUDES . "/home-membership-block.php"); ?>
        <div class="advLinks">
            <ul>
                <li class="first">
                    <a href="<?php echo SITE_URL ?>hall-of-fame.html"><img src="images/fame-icon.png" alt="" />Hall of Fame</a>
                </li>
                <li>
                    <a href="<?php echo SITE_URL ?>photo-album.php"><img src="images/gallery-icon.png" alt="" /> Image Gallery</a>
                </li>
                <li class="last">
                    <a href="<?php echo SITE_URL ?>inspire-me.html"><img src="images/me-icon.png" alt="" /> Inspire Me</a>
                </li>
            </ul>
        </div>
        <div id="slide"><!-- SLIDE start -->
            <div id="headerimgs">
                <div id="headerimg1" class="headerimg"></div>
                <div id="headerimg2" class="headerimg"></div>
            </div>
            <div id="headernav-outer"><!-- SLIDESHOW controls START -->
                <div id="headernav">
                    <div id="back" class="slideBtn"></div>
                    <div id="control" class="slideBtn"></div>
                    <div id="next" class="slideBtn"></div>
                </div>
            </div><!-- SLIDESHOW controls END -->
            <div id="headertxt"><!-- jQuery handles for the text displayed on top of the images -->
                <h2 class="caption">
                    <span id="firstline"></span>
                    <a href="#" id="secondline"></a>
                </h2>
                <p class="pictured">
                    Pictured:
                    <a href="#" id="pictureduri"></a>
                </p>
            </div>
        </div><!-- SLIDE start -->
        <div class="clear"><img src="images/spacer.gif" alt="" /></div>
    </div><!-- MAIN BANNER end -->
    <div class="updateBlock"><!-- UPDATE end -->
        <div class="label"><h5>Latest Updates</h5></div>
        <div class="update">
            <ul id="mycarousel" class="jcarousel-skin-tango">
                <?php echo $objWhats->getLatestNews(); ?>
            </ul>
        </div>
        <div class="newsletter">
            <label id="msg-label">Sign up for great deals and travel tips</label>
            <div class="textBg">
                <div class="btnBg">
                    <form id="newsletter" action="newsletter.php" method="POST">
                        <input type="text" placeholder="YHAI Newsletter" class="textBox" name="newsletter-email" id="newsletter-email" />
                        <input type="button" value="Sign up" id="newsletterSubmit" class="btn" />
                        <input type="hidden" name="backurl" value="<?php echo basename($_SERVER['SCRIPT_NAME']); ?>" />
                    </form>
                </div>
            </div>
        </div>
        <div class="clear"><img src="images/spacer.gif" alt="" /></div>
    </div><!-- UPDATE start -->
    <div class="newsBlock"><!-- NEWS BLOCK start -->
        <div class="box news"><!-- NEWS start -->
            <div class="bottom">
                <div class="mid">
                    <h4><a href="<?php echo SITE_URL ?>whats-new.php">What's New</a></h4>
                    <div  class="whiteBox">
                        <div class="whiteBoxBottom">
                            <div class="whiteBoxMid">
                                <ul class="list">
                                    <?php echo $objWhats->getWhatsNew(); ?>
                                </ul>
                                <div class="clear"><img src="images/spacer.gif" alt="" /></div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"><img src="images/spacer.gif" alt="" /></div>
                </div>
            </div>
        </div><!-- NEWS end -->
        <div class="bannerBlock"><!-- ADS start -->
            <div class="leftBanner">
                <?php echo $objBanner->getHomeBottomLeftBanner(); ?>
            </div>
            <div class="rightBanner">
                <?php echo $objBanner->getHomeBottomRightBanner(); ?>
            </div>
        </div><!-- ADS end -->
        <div class="box connect"><!-- CONNECT start -->
            <div class="bottom">
                <div class="mid">
                    <h4>YHAI Connect</h4>
                    <div class="tabBlock"><!-- TABBING start -->
                        <ul class="tabs">
                            <li><a href="#tab1"><span>Facebook</span></a></li>
                            <li><a href="#tab2"><span>Youtube</span></a></li>                            
                        </ul>
                        <div class="tab_container whiteBox">
                            <div class="whiteBoxBottom">
                                <div class="whiteBoxMid">
                                    <div id="tab1" class="tab_content" style="overflow-y: auto;overflow-x: hidden;height:142px;">
                                        <iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fyouthhostelassociationofindia&amp;width=200&amp;height=258&amp;colorscheme=light&amp;show_faces=true&amp;border_color&amp;stream=false&amp;header=false" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:258px;" allowTransparency="true"></iframe>
                                        <div class="clear"><img src="images/spacer.gif" alt="" /></div>
                                    </div>
                                    <div id="tab2" class="tab_content" style="overflow-y: auto;overflow-x: hidden;height:142px;">
                                        <p id="Youtube">coming Soon...</p>
                                        <div class="clear"><img src="images/spacer.gif" alt="" /></div>                                                            
                                    </div>
                                    <div id="tab3" class="tab_content">
                                        <p>coming Soon...</p>
                                        <div class="clear"><img src="images/spacer.gif" alt="" /></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- TABBING end -->
                    <div class="clear"><img src="images/spacer.gif" alt="" /></div>
                </div>
            </div>
        </div><!-- CONNECT end -->
        <div class="clear"><img src="images/spacer.gif" alt="" /></div>
    </div><!-- NEWS BLOCK end -->    
    <div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div><!-- BODY end -->
<?php include_once("mod_highlights.php"); ?>
<script type="text/javascript" charset="utf-8">
    jQuery(function () {
		//console.log('<?php print_r($_REQUEST);?>');
        jQuery('#newsletterSubmit').click(function () {
            var newsletter_email_id = jQuery('#newsletter-email').val();
            if (jQuery('#newsletter-email').val() == "" || jQuery('#newsletter-email').val() == "YHAI Newsletter" || isEmail(newsletter_email_id) == false){
                alert('Enter valid Email ID.');
                jQuery('#newsletter-email').focus();
                return false;
            }
            jQuery.getJSON("newsletter-ajax.php", {email: jQuery('#newsletter-email').val()}, function (res_data) {
                var msg = '';
                for (var i = 0; i < res_data.length; i++) {
                    msg += res_data[i].msg;
                }
                jQuery("#msg-label").html(msg);
            })
        })
    })
</script>
<?php include_once(PATH_INCLUDES . "/footer.php"); ?>