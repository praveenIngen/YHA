<?php
include_once("includes/framework.php");
$row = $objWhats->getInfo(JRequest::getVar('id'));
$objBase->setMetaData($row->metaTitle, $row->metaKeyword, $row->metaDescription);
$image_dir = 'whats-new';
if ($row->type == 2) {
    $image_dir = 'latest-news';
} else if ($row->type == 3) {
    $image_dir = 'yhai-news';
}
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
                <h3 class="detailsHeading"><?php echo $row->title; ?></h3>
                <a href="#" class="back" onclick="javascript:history.go(-1);" style="color:#000;">Go Back</a>				
                <div class="imageDiv"><img src="<?php echo WWW_UPLOAD_PATH . $image_dir . '/' . $row->image; ?>" style="float:left;" /></div>  
                <div class="news-content"><?php echo $row->long_description; ?></div>
                <div class="clear"><img src="images/spacer.gif" alt="" /></div>
            </div>
        </div>
        <div class="clear"><img src="images/spacer.gif" alt="" /></div>
    </div>
<?php echo $objBanner->getBottomBanner(); ?>
    <div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div>
<!-- BODY end -->
<?php include_once(PATH_INCLUDES . "/footer.php"); ?>