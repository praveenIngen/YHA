<?php 
$sliderArray = ENUM::GetHomeSliderImages();
?>
<div id="rev_slider_116_1" class="rev_slider fullwidthabanner" style="display:none;" data-version="5.0.7">
                      <ul style="width:100% !important;">
                          <?php for($i=0;$i<count($sliderArray);$i++)
                                {
                                  $rs = 391 + $i;
                                  $rsIndex = "rs-".$rs; 
                                  ?>
                                  <!-- SLIDE  -->
                          <li data-index="<?php echo $rsIndex;?>" data-transition="parallaxhorizontal" data-slotamount="default" data-easein="default" data-easeout="default" data-masterspeed="default" data-rotate="0" data-saveperformance="off" data-title="Smooth Mask" data-description="">
                              <!-- MAIN IMAGE -->
                              <img src="<?php echo SITE_PATH_WEBROOT;?>/Img/home_slider_img/<?php echo $sliderArray[$i];?>" alt="slide2" data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" class="rev-slidebg" data-no-retina/>

                              <!-- LAYERS -->
                                <!-- Layer wrapper start-->
                              <div class="layer_wrapper_main">
                                <!-- LAYER 1 start-->
                                <div class="slider_buttons tp-caption NotGeneric-Title   tp-resizeme"
                                     id="slide-391-layer-<?php echo $i;?>"
                                     data-x="165" data-hoffset=""
                                     data-y="25" data-voffset="-120"
                                                data-width="['auto','auto','auto','auto']"
                                    data-height="['auto','auto','auto','auto']"
                                    data-transform_idle="o:1;"
                        
                                     data-transform_in="y:top;s:2000;e:Power4.easeInOut;"
                                     data-transform_out="s:1000;e:Power2.easeInOut;s:1000;e:Power2.easeInOut;"
                                    data-start="1000"
                                    data-splitin="none"
                                    data-splitout="none"
                                    data-responsive_offset="on" style="text-align:center;">
                                            
                                            <a href="javascript:void(0)"  style=" border: 1px solid #fdb714;" class="large_slide_btn home_book_now">BOOK HOSTEL</a>
                                </div>
                              <!-- LAYER 1 end-->
                            </div>
                            <!-- layer wrapper end -->
                          </li>
                          <!--slide-->
                          <?php }?>
                      </ul>
                  </div>
              </div>
                 <!-- overlay_tabs start -->
      <div class="overlay_tabs" >
        <div class="container">
          <div class="row">
  <!--  slider_tabs_serch_start -->
            <div class="col-lg-4 col-md-4 col-sm-6 tab_search_widget" style="display:none">
               <aside class="widget widget_search_availability">
                 <div class="wsa_tab">
                   <ul>
                     <li><a href="#yha_india_hostel_booking_serach" class="respon active" ><h4>INDIA
                     </h4></a></li>
                <li style="margin-left: 4%" href="_internatonalHostelSearch.php" class="loadIt" loadOn="yha_internationl_hostel_booking_serach" loader-msg="Getting International Hostels....." load-every-time="false"><a href="#yha_internationl_hostel_booking_serach"  class="respon"><h4>INTERNATIONAL
                  </h4></a></li>
                
              </ul>
            </div>
              <div id="yha_india_hostel_booking_serach" class="wsa_tab_content" style=" background: rgba(0, 0, 0, 0.35) none repeat scroll 0 0 !important;">
                  <?php include_once("mod_bookahostelindia.php"); ?>
              </div>
              <div id="yha_internationl_hostel_booking_serach" class="wsa_tab_content" style=" background: rgba(0, 0, 0, 0.35) none repeat scroll 0 0 !important;">
                  
              </div>

  </aside>
  </div>
  </div>
  </div>
  </div>
              <!-- END REVOLUTION SLIDER -->