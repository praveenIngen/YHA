<div id="rev_slider_116_1" class="rev_slider fullwidthabanner" style="display:none;" data-version="5.0.7">
                      <ul style="width:100% !important;">
                          <!-- SLIDE  -->
                           <?php 
                if($prog->adp_photo != "" || ($proGal!=null && !empty($proGal)))
              {

                    if($prog->adp_photo != "")
                    { 
                      $i = 391;
                      $rs = "rs-".$i;
                      ?>
                          <li data-index="<?php echo $rs;?>" data-transition="parallaxhorizontal" data-slotamount="default" data-easein="default" data-easeout="default" data-masterspeed="default" data-rotate="0" data-saveperformance="off" data-title="Smooth Mask" data-description="">
                              <!-- MAIN IMAGE -->
                              <img src="<?php echo SITE_URL ?>uploads/programme/photo/<?php echo $prog->adp_photo; ?>" alt="slide" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" class="rev-slidebg" data-no-retina>
 </li>
                          <!--slide-->
                          <?php }
                   foreach ($proGal as $row) {
                    $i++;
                    $rs = "rs-".$i;
                    ?>
                          <li data-index="<?php echo $rs;?>" data-transition="parallaxhorizontal" data-slotamount="default" data-easein="default" data-easeout="default" data-masterspeed="default" data-rotate="0" data-saveperformance="off" data-title="Slide Mask" data-description="">
                              <!-- MAIN IMAGE -->
                              <img src="uploads/programme/gallery/<?php echo $row->gal_image; ?>" alt="slide" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" class="rev-slidebg" data-no-retina>
                              
                          </li>
                          <!--slide-->
                           <?php }
                } else{ ?>
                              <li data-index="rs-391" data-transition="parallaxhorizontal" data-slotamount="default" data-easein="default" data-easeout="default" data-masterspeed="default" data-rotate="0" data-saveperformance="off" data-title="Slide Mask" data-description="">
                              <!-- MAIN IMAGE -->
                             <img src="<?php echo SITE_DEFAULT_IMAGE_FULL_PATH ?>" alt="slide"
                  data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" class="rev-slidebg" data-no-retina>
                              
                          </li>
                          <?php  } ?>
                          <!--slide-->
                              
                          <!--slide-->
                      </ul>
                  </div>
              </div>
                 <!-- overlay_tabs start -->
      <div class="overlay_tabs">
        <div class="container">
          <div class="row">
  <!--  slider_tabs_serch_start -->
  <div class=" col-md-8 col-sm-8 tab_search_widget tab_search_widget2" style="float:left; margin-top:-8%;">
               <aside class="widget widget_search_availability">
                 
              <div id="yha_india_hostel_booking_serach" class="wsa_tab_content adventure" style=" background: rgba(0, 0, 0, 0.35) none repeat scroll 0 0 !important;">
               <h3 class="progslidername"><?php echo $prog->adp_name ?></h3> 
              </div>
              </aside>
              </div>
            <div class="col-md-4 col-sm-4 tab_search_widget tab_search_widget3" style="float:right; right:0;">
               <aside class="widget widget_search_availability">
                 
              <div id="yha_india_hostel_booking_serach" class="wsa_tab_content adventure" style=" background: rgba(0, 0, 0, 0.35) none repeat scroll 0 0 !important;">
                  <div class="right_includes_hotel " style="float: left; font-size:17px; width:100%;">
                 
                  <?php 
                  $startDateArray = $objMasters->getDateInJson($objMasters->dateFormat($prog->adp_from_date,null,"d-M-Y"),"DD/MM/Y");
                  ?>
                  <div class="check_in_out_wrap responsecheckin2 " style="padding-left:15%; padding-right:15%;" >
                    <div class="check_in" style="float: left; ">
                     <label style="color: #fff;">Start Date</label>
                       <div class="check_in_box">
                        <span class="day"><?php echo $startDateArray->Y; ?></span>
                        <span class="date " style="background: #f6f6f6;"><?php echo $startDateArray->DD; ?></span> 
                        <span class="month" style="background: #f6f6f6;"><?php echo $startDateArray->MM; ?></span>
                       </div>
                    </div>
                      <?php 
                  $endDateArray = $objMasters->getDateInJson($objMasters->dateFormat($prog->adp_to_date,null,"d-M-Y"),"DD/MM/Y");
                  ?>
                  <div class="check_in check_in_margin" style="float: right; "  >
                    <label style="color: #fff;">End Date</label>
                      <div class="check_in_box">
                       <span class="day"><?php echo $endDateArray->Y; ?></span>
                       <span class="date " style="background: #f6f6f6;"><?php echo $endDateArray->DD; ?></span> 
                       <span class="month " style="background: #f6f6f6;"><?php echo $endDateArray->MM; ?></span> 
                      </div>
                  </div>
                </div>
          </div>
          <h2 style="color: #ffffff ; margin-top:60%; text-align:center;"><i class="fa fa-inr rupeesicon" aria-hidden="true" style=" font-size: 26px !important; color: #ffffff !important;"></i><?php echo $prog->adp_price ?></h2>
        <div class="buttonDiv travel_form_element  pull_left responsebtn " style="margin-top: 1%;  width: 230px; ">
               <button class="btn btn-yellow btn-travel Width100PC first tab1 programtabBtn active" type="button" toShow = "content1"  style=" font-size: 19px; font-weight: bold;"><a href="#content1" style="color: #ffffff;">Read More</a></button>
        <div class="buttonDiv travel_form_element  pull_left responsebtn " style="  width: 230px; ">
               <button class="btn btn-yellow btn-travel Width100PC first tab1 programtabBtn active" type="button"  toShow = "content3" style=" font-size: 19px; font-weight: bold;"><a href="#content3" style="color: #ffffff;">Book Now</a></button>
                  
              </div>

              

  <!--  
              <!-- END REVOLUTION SLIDER -->