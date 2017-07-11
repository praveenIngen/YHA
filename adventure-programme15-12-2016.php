

<?php
   include_once("includes/framework.php");
    
   //include Hostel controller
   $page_id = JRequest::getVar('page_id');
   $node = JRequest::getVar('node');
   if (!$node) {
       $node = $objMenu->get_page_node_id();
   }
   $data = JRequest::get('post');
   $id = JRequest::getVar('id');
   $ty = JRequest::getVar('ty');
   $prog = $objProgramme->getInfo($id, 1);
   if (!is_object($prog)) {
       $objBase->Redirect('index.php');
   }
   $proGal = $objProgramme->getGalList($id);
   $objBase->setMetaData($prog->metaTitle, $prog->metaKeyword, $prog->metaDescription);
   ?>
<?php include_once(PATH_INCLUDES . "/header.php"); ?>
<div id="body">
   <!-- BODY start -->
   <div id="content_wrapper">
      <div class="clearfix"></div>
      <!-- Home first slider start -->
      <div class="slider_tab_main">
         <div class="full_width home_slider">
            <div class="example">
               <article class="content" style="width:100%; float:left;">
                  <div id="rev_slider_116_1_wrapper" class="rev_slider_wrapper fullwidthbanner-container" data-alias="layer-animations" style="margin:0px auto;background-color:transparent;padding:0px;margin-top:0px;margin-bottom:0px;">
                     <!-- START REVOLUTION SLIDER 5.0.7 auto mode -->
                     <?php include_once("adventure-program-sldier.php"); ?>
                     <!-- END Revolution SLIDR -->
                  </div>
               </article>
            </div>
            <!-- section end -->
         </div>
      </div>
   </div>
   <div class="home_tabs_search Width100PC">
      <div class="full_width slider_content_wrap respons1st" style="padding: 1% !important; background-color: rgba(0,0,0,0.1);">
      <div class="container">
         <!-- Hostel Booking Start -->
         <div class="col-md-12 col-sm-12">
            <div class="buttonDiv travel_form_element  pull_left responsebtn col-md-2 col-sm-2" style="margin-top: 1%;">
               <button class="btn btn-yellow btn-travel Width100PC first tab1 programtabBtn active" type="button" toShow = "content1"  style=" font-size: 19px; font-weight: bold;"><a href="#content1" style="color: #ffffff;">About Us</a></button>
            </div>
            <div class="buttonDiv travel_form_element  pull_left responsebtn col-md-2 col-sm-2" style="margin-top: 1%;">
               <button class="btn btn-yellow btn-travel Width100PC tab2 programtabBtn"  type="button"  toShow = "content2"   style=" font-size: 19px; font-weight: bold;"><a href="#content2" style="color: #ffffff;"> Participation</a></button>
            </div>
            <div class="buttonDiv travel_form_element tabcontent pull_left responsebtn col-md-2 col-sm-2" style="margin-top: 1%;">
               <button class="btn btn-yellow btn-travel Width100PC tab4 programtabBtn" type="button"  toShow = "content3"  style=" font-size: 19px; font-weight: bold;"><a href="#content3" style="color: #ffffff;">Facts</a></button>
            </div>
            <div class="buttonDiv travel_form_element  pull_left responsebtn col-md-2 col-sm-2" style="margin-top: 1%;">
               <button class="btn btn-yellow btn-travel Width100PC tab4 programtabBtn"  type="button"  toShow = "content4"  style=" font-size: 19px; font-weight: bold;"><a href="#content4" style="color: #ffffff;">Directions</a></button>
            </div>
          <div class="buttonDiv travel_form_element  pull_left responsebtn col-md-2 col-sm-2" style="margin-top: 1%;">
               <button class="btn btn-yellow btn-travel Width100PC tab5 programtabBtn"   type="button"  toShow = "gallery"  style=" font-size: 19px; font-weight: bold;"><a href="#gallery" style="color: #ffffff;">Gallery</a></button>
            </div>
        
               <div class="buttonDiv travel_form_element  pull_left responsebtn col-md-2 col-sm-2" style="margin-top: 1%;">
               <?php if ($prog->adp_brochure_file != "") { ?>
           <a href="<?php echo SITE_URL ?>uploads/programme/brochure/<?php echo $prog->adp_brochure_file; ?>" style="color: #ffffff;">    <button class="btn btn-yellow btn-travel Width100PC tab6 programtabBtn"   type="button"   style=" font-size: 19px; font-weight: bold;">Brochure</button></a>
                <?php } ?>
            </div>
              
                
         </div>
         </div>
      </div>
   </div>
   <div class="container">
      <!--left side start-->
      <!-- desc icons Start-->
      <div class="col-md-12 col-sm-12" style=" margin-top: 2%;">
         <div class="tour_packages_right_section left_space_40">
            <div class="tour_packages_details_top">
               <div class="top_head_bar  ">
                  <h4 style="float: left;">
                     <?php echo $prog->adp_name ?>
                    
                  </h4>
                  <h4 class="prices_style" style="float: right; font-size:38px;"><i class="fa fa-inr rupeesicon" aria-hidden="true" style=" font-size: 36px !important; color: #ffffff;"></i><?php echo $prog->adp_price ?></h4>
               </div>
               <div class="full_width package_highlight_section top_bar" style=" border-top: 1px solid #d3d3d3; ">
                  <div class="" style="margin-left: -3%;">
                     <div class="col-md-12 col-sm-12">
                        <div class="col-md-9 col-sm-9 margnlft1">
                           <ul>
                              <li class="listalign"><i class="fa fa-location-arrow listreporticon"  aria-hidden="true"></i>
                                 <span class="starting_text" style="color:black;" ><?php echo $prog->adp_report_point ?> - <?php echo $objProgramme->showProgramDuration($prog->adp_duration, $prog->adp_from_date, $prog->adp_to_date); ?>
                                 </span>
                                 <br/>
                              </li>
                              <li class="listalign"> <i class="fa fa-calendar listreporticon" aria-hidden="true"></i> 
                                 <span class="time_date"><span  style="color:black;"><?php echo $prog->adp_period ?></span>
                                 </span>
                              </li>
                              <li class="listalign"><i class="fa fa-briefcase listreporticon" aria-hidden="true"></i>                    
                                 <span class="includes_text" style="color:black;">includes:<?php echo $prog->adp_cover_services ?>
                                 </span>
                              </li>
                           </ul>
                        </div>
                        <div class="right_includes_hotel responsecheckin1 col-md-3 col-sm-3 " style="float: right; margin-right: -3%;">
                           <?php 
                              $startDateArray = $objMasters->getDateInJson($objMasters->dateFormat($prog->adp_from_date,null,"d-M-Y"),"DD/MM/Y");
                              ?>
                           <div class="check_in_out_wrap" style="padding-left:35%;">
                              <div class="check_in responsecheckin2" style="float: left; ">
                                 <label>Start Date</label>
                                 <div class="check_in_box">
                                    <span class="day"><?php echo $startDateArray->Y; ?></span>
                                    <span class="date"><?php echo $startDateArray->DD; ?></span> 
                                    <span class="month"><?php echo $startDateArray->MM; ?></span>
                                 </div>
                              </div>
                              <?php 
                                 $endDateArray = $objMasters->getDateInJson($objMasters->dateFormat($prog->adp_to_date,null,"d-M-Y"),"DD/MM/Y");
                                 ?>
                              <div class="check_in" style="float: right;">
                                 <label>End Date</label>
                                 <div class="check_in_box">
                                    <span class="day"><?php echo $endDateArray->Y; ?></span>
                                    <span class="date"><?php echo $endDateArray->DD; ?></span> 
                                    <span class="month"><?php echo $endDateArray->MM; ?></span> 
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- tab include area End --> 
            </div>
            <!-- End .portfolio-content --> 
         </div>
      </div>
      <!-- slider end -->
      <div class="full_width hotel_details_mdl_tab" id="Travelite_middle_tabs">
         <div class="pcg_tabs_panel">
            <ul class="row">
               <li class="tab3 col-md-2 col-sm-2 "><a href="#content" class="active" style="font-size:20px;">Schedule &amp; Seats</a></li>
            </ul>
         </div>
         <div class="tabContentBottom  " >
            <!-- TAB CONTENT start -->
            <div class="tabContent">
               <div id="content" class="tabcontent">
                  <?php if ($prog->adp_is_online_booking == 1) { 
                     $seats = $objProgramme->getscheduleSeatList($prog->adp_id);
                       if (!empty($seats)) { 
                        if($prog->adp_category=='F'){ 
                           $seatHeading = 'Total Families';                    
                         }else{                      
                          $seatHeading = 'Total Seats'; } ?>                
                  <table width="100%" align="center" cellpadding="2" cellspacing="1" bgcolor="#E2e2e2" class=" table table-striped table-hover ">
                  <thead style="background-color:#f9f9f9;">
                  <tr>
                           <td align="center" width="15%" style="font-size:18px;"><b>Date</b></td>
                           <td align="center" style="font-size:18px;"><b><?php echo $seatHeading?></b></td>
                           <td align="center" style="font-size:18px;"><b>Seats Booked</b></td>
                           <td align="center" style="font-size:18px;"><b>Seats left</b></td>
                           <td align="center" style="font-size:18px;"><b>Book Now</b></td>
                           <!-- <td align="center"><b>Total Waiting</b></td>
                              <td align="center"><b>Total Waiting Request</b></td>
                              <td align="center"><b>Total Waiting Balance</b></td>-->
                        </tr>
                  </thead>
                     <tbody>
                        
                        <?php foreach ($seats as $seat) {
                         ?>
                        <tr class="colorhover" >
                           <td align="center"><?php echo date('d M, Y', $seat->inv_date);?></td>
                           <td align="center"><?php echo $seat->inv_online_seat ;?></td>
                           <td align="center"><?php echo (int)$seat->inv_online_seat< (int) $seat->booked ?$seat->inv_online_seat:(int) $seat->booked;?></td>
                           <td align="center"><?php echo (int)$seat->inv_online_seat< (int) $seat->booked ?'0':($seat->inv_online_seat - (int) $seat->booked); ?></td>
                           <?php if ((($seat->inv_online_seat - (int) $seat->booked) > 0 ) && $objMasters->compareDates($objMasters->Today(),$objMasters->dateFormat($seat->inv_date,null,null))>=0) { ?>
                           <td align="center"><a style=" color :#000000; font-weight:bold;" class="hovers"href="<?php echo SITE_URL?>/book-program.php?id= <?php echo $id ?> &dt= <?php echo $seat->inv_date ?>">Book Now</a></td>
                           <?php } else {?>
                           <td align="center">--</td>
                           <?php } ?>
                        </tr>
                        <?php } ?>
                     </tbody>
                     <?php //} ?>
                  </table>
                  <?php } else { ?>
                  <?php  echo 'No Information available related to schedule a seat.';?>
                  <?php }
                     ?>
                  <?php } else { ?>
                  <?php echo $prog->adp_contact_info; ?>
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
      <div class="full_width hotel_details_mdl_tab" id="Travelite_middle_tabs">
         <div class="pcg_tabs_pane">
            <div class="tabContentBottom" style="padding:2%;">
               <!-- TAB CONTENT start -->
               <div class="tabContentNew">
                  <div id="content1" class="tabcontent programtabContent" style="display:block;">
                     <?php echo $prog->adp_about ?>
                  </div>
                  <div id="content2" class="tabcontent programtabContent">
                     <?php echo $prog->adp_participate; ?>
                  </div>
                  <div id="content3" class="tabcontent programtabContent">
                     <?php echo $prog->adp_facts; ?>
                  </div>
                  <div id="content4" class="tabcontent programtabContent">
                     <?php if ($prog->adp_map_image != "") { ?>
                     <img src="<?php echo SITE_URL ?>uploads/programme/map/<?php echo $prog->adp_map_image; ?>">
                     <?php } else { ?>
                     No Map detail.
                     <?php } ?>
                  </div>
               
                 
                 
                
                   <div id="gallery" class="galleryBlock tabcontent programtabContent "><!-- GALLERY BLOCK start -->
                        <div class="ad-gallery " >
                            <a name="gal" id="gal"></a>
               
                <?php if ($proGal) { ?>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
  <div class="portfolio-row">
       <div class="portfolio_column_3_popup">
            <div id="portfolio-item-container" class="max-col-3 popup-gallery" data-layoutmode="fitRows">
            <ul class="gallery">
                   
                           
                                    
                                        <?php
                                        $i = 1;
                                        $cnt = count($proGal);
                                        foreach ($proGal as $row) {
                                            if ($i == 1) {
                                                $cls = 'class="first"';
                                            } elseif ($i == $cnt) {
                                                $cls = 'class="last"';
                                            } else {
                                                $cls = '';
                                            }
                                            $i = $i + 1;
                                            ?>
                                             <div class="portfolio-item col-md-4 col-sm-4 col-xs-4 portfolio-custom 2014">
                <figure><a href="<?php echo SITE_URL ?>uploads/programme/gallery/<?php echo $row->gal_image; ?>"> <img class="image4" longdesc="" title="<?php echo $row->gal_title; ?>" alt=""  src="uploads/programme/gallery/<?php echo $row->gal_image; ?>" width="100%" />  </a> </figure>

                <div class="portfolio-content">
                  <div class="portfolio-meta"> <a href="<?php echo SITE_URL ?>uploads/programme/gallery/<?php echo $row->gal_image; ?>"><i class="fa fa-search-plus"></i></a>
                    <h2 class="portfolio-title"> <a href="javascript:void(0);"></a></h2>
                  </div>
                  <!-- End .portfolio-meta --> 
              
               
                    </div>
</div>
             
             

<?php } ?>
              </ul>
              </div>
              </div>
              </div>
              </div>
             

<?php } ?>

</div>
</div>
</div>
</div>







 
                  </div>
                  <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>
               </div>
            </div>
            <!-- TAB CONTENT end -->
            



              
               
            <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>
        
      
      <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" />
      </div>
      <?php echo $objBanner->getBottomBanner(); ?>
      <div class="clear"><img src="<?php echo SITE_URL ?>images/spacer.gif" alt="" /></div>
  
</div>
<!-- BODY end -->
<script type="text/javascript">
   $(document).ready(function(){
     $(document).on('click','.programtabBtn',function(){
         var toshow = $(this).attr('toshow');
         $('.programtabContent').hide();
         $('#'+toshow).show();
         $("html, body").stop().animate({scrollTop: $('#'+toshow).offset().top-100}, '500', 'swing');
     });
   });
</script>
<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>

