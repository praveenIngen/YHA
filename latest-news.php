<?php
include_once("includes/framework.php");
$node = JRequest::getVar('node');
if(!$node){
    $node = $objMenu->get_page_node_id();
}
$sql_count = "SELECT a.* FROM #__news a " . $cond_ext . " where type='2' AND status='1' ";
$result_count = $objDB->setQuery($sql_count);
$num_rows = $objDB->num_rows($result_count);
$objPaginator->items_total = $num_rows;
$objPaginator->paginate();
$paging = $objPaginator->display_all_pages();
if ($num_rows > 0) {
    $sql = "SELECT a.* FROM #__news a " . $cond_ext . " where type='2' AND status='1' " . $objPaginator->limit;
    $result = $objDB->setQuery($sql);
    //echo $objDB->getQuery();
    $rows = $objDB->loadObjectList($result);
}
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
            <div class="participate-rightCol">

                <h3 class="detailsHeading">Latest news</h3>
                <ul>
                    <?php
                    $ctr = 0;
                    if ($rows) {
                        foreach ($rows as $row):
                            ?>
                            <li style="float:left; width:100%; margin-bottom:5px;">
                                <h6><a href="<?php echo SITE_URL . 'latest-news-details.php?id=' . $row->id; ?>" style="color:#e1830c !important;"><?php echo $row->title; ?></a></h6>	
                                <?php
                                $filename = UPLOAD_PATH . '/latest-news/' . $row->thumb_image;
                                if ($row->thumb_image != "" && file_exists($filename)) {
                                    ?>
                                    <div class="imageDiv"><a href="<?php echo SITE_URL . 'yhai-news-details.php?id=' . $row->id; ?>" style="color:#e1830c !important;"><img src="<?php echo WWW_UPLOAD_PATH . 'latest-news/' . $row->thumb_image; ?>" alt="" style="float:left;" /></a></div>
                                    <?php
                                }
                                ?>
                                <div class="info">

                                    <p><?php echo $row->short_description; ?></p>
                                </div>
                            </li>
                            <?php
                        endforeach;
                    } else {
                        echo '<li style="float:left; width:100%; margin-bottom:5px;">No Record(s) Found.</li>';
                    }
                    ?>   

                </ul>				
                <div class="pagination"><?php echo $paging; ?></div>
            </div>

        </div>
        <div class="clear"><img src="images/spacer.gif" alt="" /></div>
    </div>
<?php echo $objBanner->getBottomBanner(); ?>
    <div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div>
<!-- BODY end -->
<?php include_once(PATH_INCLUDES . "/footer.php"); ?>
	 