<?php
include_once("includes/framework.php");
$page_id = JRequest::getVar('page_id');
$node = JRequest::getVar('node');
if (!$node) {
    $node = $objMenu->get_page_node_id();
}
$data = JRequest::get('post');
$programList = $objProgramme->getProgramList('I');
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
            <div class="program">
                <h3 class="redBullet">International Level Programmes</h3>
                <!--<div class="pagingDiv">
                       <label>1-4 of 20</label>
                     <ul class="paging">
                       <li class="prev"><a href="#">Previous</a></li>
                          <li class="next"><a href="#">Next</a></li>
                     </ul>
                </div>-->
                <ul class="listing">
                    <?php
                    if ($programList) {
                        foreach ($programList as $row):
                            ?>
                            <li>
                                <div class="infoBlock">
                                    <h3 class="redBullet"><?php echo $row->adp_name ?></h3>
                                    <div class="detailsInfo">
                                        <div class="details">
                                            <h6><?php echo $row->adp_report_point ?> - <?php echo $objProgramme->showProgramDuration($row->adp_duration, $row->adp_from_date, $row->adp_to_date); ?></h6>
                                            <div class="desc"><?php echo $row->adp_period ?> <br /><?php echo $row->adp_cover_services ?></div>
                                            <div class="price">Rs.<?php echo $row->adp_price ?>/- per member</div>
                                            <div class="buttonBlock">
                                                <div class="greenBtnLink"><a href="<?php echo $objProgramme->getBookingLink($row->adp_is_online_booking, $row->adp_id); ?>"><span>Book Now</span></a></div>
                                                <div class="greenBtnLink"><a href="<?php echo SITE_URL ?>adventure-programme.php?id=<?php echo $row->adp_id ?>&ty=s"><span>Schedule &amp; Seats</span></a></div>
                                                <div class="greenBtnLink"><a href="<?php echo SITE_URL ?>adventure-programme.php?id=<?php echo $row->adp_id ?>"><span>Read More</span></a></div>
                                                <div class="clear"><img src="images/spacer.gif" alt="" /></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="galleryBlock">

                                    <span class="title"><?php echo $objProgramme->getGalleryLink($row->galcnt, $row->adp_id); ?></span>
                                    <span class="image">
                                        <?php if ($row->adp_photo != "") { ?>
                                            <a href="<?php echo SITE_URL ?>adventure-programme.php?id=<?php echo $row->adp_id ?>"> <img src="<?php echo SITE_URL ?>uploads/programme/photo/<?php echo $row->adp_photo; ?>" alt="" width="214" height="140" /></a>
                                        <?php } else { ?>
                                            <a href="<?php echo SITE_URL ?>adventure-programme.php?id=<?php echo $row->adp_id ?>"> <img src="<?php echo SITE_URL ?>images/no_image.gif" alt="" width="214" height="140" /></a>
                                        <?php } ?> 
                                    </span>
                                </div>
                            </li>
                            <?php
                        endforeach;
                    } else {
                        echo '<li>No International Level Programmes</li>';
                    }
                    ?>
                </ul>
                <!-- <div class="pagingDiv">
                        <label>1-4 of 20</label>
                      <ul class="paging">
                        <li class="prev"><a href="#">Previous</a></li>
                           <li class="next"><a href="#">Next</a></li>
                      </ul>
                 </div>-->
                <div class="clear"><img src="images/spacer.gif" alt="" /></div>
            </div> 
        </div>
        <div class="clear"><img src="images/spacer.gif" alt="" /></div>        
    </div>    
<?php echo $objBanner->getBottomBanner(); ?>
    <div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div><!-- BODY end -->
<?php include_once(PATH_INCLUDES . "/footer.php"); ?>