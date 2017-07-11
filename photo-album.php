<?php

include_once("includes/framework.php");

$node = JRequest::getVar('node');

if (!$node) {

    $node = $objMenu->get_page_node_id();

}

$sql_count = "SELECT a.* FROM #__news a " . $cond_ext . " where type='3' AND status='1' ";

$result_count = $objDB->setQuery($sql_count);

$num_rows = $objDB->num_rows($result_count);

$objPaginator->items_total = $num_rows;

$objPaginator->paginate();

$paging = $objPaginator->display_all_pages();

if ($num_rows > 0) {

    $sql = "SELECT a.* FROM #__photo_albums a " . $cond_ext . " where alb_status='1' ORDER BY alb_updated DESC";

    $result = $objDB->setQuery($sql);

    //echo $objDB->getQuery();

    $rows = $objDB->loadObjectList($result);

}



if (JRequest::getVar('id')) {

    $sql = "SELECT a.*,b.alb_name FROM #__photos as a, #__photo_albums as b WHERE a.pht_alb_id=b.alb_id AND a.pht_alb_id='" . JRequest::getVar('id') . "'";

    $result = $objDB->setQuery($sql);

    //echo $objDB->getQuery();

    $prows = $objDB->loadObjectList($result);

}

$node_info = $objMenu->getInfo($node);

$objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);

?>
<?php include_once(PATH_INCLUDES . "/header.php"); ?>
<div id="body">

<div class="container">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
  <div class="portfolio-row">
       <div class="portfolio_column_3_popup">
            <div id="portfolio-item-container" class="max-col-3 popup-gallery" data-layoutmode="fitRows">
            <ul class="gallery">

                        <?php

                        if ($rows) {

                            $ctr_row = 0;

                            foreach ($rows as $row):

                                if ($ctr_row % 3 == 0 && $ctr_row > 0) {

                                    echo '</li></ul><ul class="gallery">';

                                }

                                ?>
             <div class="portfolio-item col-md-4 col-sm-4 col-xs-4  portfolio-custom 2014">
              
                <figure class="destination_box_wrapper"> <a href="photos.php?id=<?php echo $row->alb_id; ?>#frontView" class="" title="Portfolio Item Title"> <img src="<?php echo WWW_UPLOAD_PATH; ?>photos/<?php echo $row->alb_image; ?>" height="258px" width="339px" alt="portfolio item name" title="<?php echo $row->alb_name; ?>"> </a>
                <div class="feature_overlay_second" >
                <div class="bottom_first">
           <?php echo $objBase->sub_str($row->alb_name,40); ?>
                </div>
                </div>
                 </figure>

               
               
                    </div>

                <!-- End .portfolio-content --> 
             
               <?php $ctr_row++;
                 endforeach;

                        } ?>

                    </ul>
             
              
               
          
              </div>
              </div>
              </div>
              </div>
           
              
        <div class="clear"><img src="images/spacer.gif" alt="" /></div>

    </div>

    <?php echo $objBanner->getBottomBanner(); ?>

    <div class="clear"><img src="images/spacer.gif" alt="" /></div>

</div>
<!-- BODY end -->

<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>