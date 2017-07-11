<?php

include_once("includes/framework.php");

$node = JRequest::getVar('node');

if (!$node) {

    $node = $objMenu->get_page_node_id();

}

$sql = "SELECT a.* FROM #__downloads a where status='1' ";

$result = $objDB->setQuery($sql);

//echo $objDB->getQuery();

$rows = $objDB->loadObjectList();

$node_info = $objMenu->getInfo($node);

$objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);

?><?php include_once(PATH_INCLUDES . "/header.php"); ?>
<div id="body">
   <!-- BODY start -->
   <!--page title Start-->
  <div class="page_title" data-stellar-background-ratio="0" data-stellar-vertical-offset="0" style="background-image:url(<?php echo STATIC_HEADER_IMG;?>);">
      <ul>
        <li><a href="javascript:;">Downloads</a></li>
      </ul>
    </div>
    <!--page title end-->
   <div class="container">
     <div class="row">
        <div class="col-md-3 col-sm-3">
            <?php include_once(PATH_INCLUDES . "/left-side-new.php"); ?>
        </div>
        <div class="col-md-8 col-md-8">

           <div class="program">

                <h3 class="detailsHeading">Downloads</h3>

                <div class="detailsBlock">

                    <ul class="search_result">

                        <?php

                        $ctr = 0;

                        if($rows){                           

                            foreach ($rows as $row):

                                if ($ctr % 3 == 0 && $ctr > 0) {

                                    echo '</li><ul class="Downloads">';

                                }

                                $class= ($ctr%2=='0')? 'even':'odd';

                            ?>

                            <li class="<?php echo $class;?>">

                                <?php echo ($ctr+1)?>. &nbsp; <span class="title"><?php echo substr($row->name,0,50); ?>...</span> <a href="<?php echo WWW_UPLOAD_PATH; ?>downloads/<?php echo $row->file_name; ?>" title="<?php echo $row->name; ?>"><img src="<?php echo SITE_URL?>images/downloads.gif" /></a> <br />

                                <p><?php echo $row->description; ?></p>

                            </li>

                        <?php 

                            $ctr++;

                            endforeach;

                        }

                        ?>

                    </ul>

                    <div class="clear"><img src="images/spacer.gif" alt="" /></div>

                </div>
                </div>
        </div>
        <div class="col-md-1 col-sm-1"></div>
     </div>
   </div>
   
      <!--left side start-->
      <!-- desc icons Start-->
     
                  <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>
              
            <!-- TAB CONTENT end -->
            <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>
        
      <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" />
    
      <?php echo $objBanner->getBottomBanner(); ?>
      <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>
   
</div>
<!-- BODY end -->

<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>