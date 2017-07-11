<?php

include_once("includes/framework.php");

$page_id = JRequest::getVar('page_id');

$node = JRequest::getVar('node');

if (!$node) {

    $node = $objMenu->get_page_node_id();

}

$data = JRequest::get('post');

 $startLimt = 0;
 $limitAtATime = 5;
 $NoOfPage = 0;
 $ProgramType = "L";
 //use to set li id count
 $programmeListCount = 0;

 $programList = $objProgramme->getProgramList($ProgramType,'','','',null,$startLimt,$limitAtATime);

 $node_info = $objMenu->getInfo($node);
 
 $objBase->setMetaData($node_info->metaTitle, $node_info->metaKeyword, $node_info->metaDescription);
 
 $programListCountObject = $objProgramme->getProgramList($ProgramType, $st, $ed,"","Count(*)",null,null);
 $programListCountArray  = (array)$programListCountObject[0];
 $NoOfPage = $programListCountArray["Count(*)"]<=$limitAtATime?0:2;
?>
<?php include_once(PATH_INCLUDES . "/header.php"); ?>
<div id="body">
   <!-- BODY start -->
   <div class="full_width  sorting_panel  col-md-12 col-sm-12" style="background: #f6f4f4 ; ">
      
         <h3 class="noBullet col-md-4 col-sm-4" style="margin-left:6%;">Participate Now</h3>
         <div class="rightCol " style="margin-top:24px; float:right;">
            <div class="program col-md-8 col-sm-8" style="float: right;  margin-top: 2%">
       
               <span style="font-size:130%; padding:14px;"><i class="fa fa-square hostels "  aria-hidden="true" style="margin-left:-147px;"></i>Hostel</span>
              
               </ul>
            </div>
         </div>
      </div>
   </div>
   

<div style="border-top: 1px solid #d3d3d3; margin-top:14%; margin-bottom:1%;"></div>
<div class="container">
   <div class="row" >
      <div class="full_width sorting_places_section">
         <ul class="participateListing">
            <?php
               if ($programList) {
                  
                   foreach ($programList as $row):
                      $programmeListCount++;
                       if ($row->adp_type == 'L') {
               
                           $cls = 'hostelList';
               
                       } elseif ($row->adp_type == 'S') {
               
                           $cls = 'stateList';
               
                       } elseif ($row->adp_type == 'I') {
               
                           $cls = 'internationalList';
               
                       } else {
               
                           $cls = 'nationalList';
               
                       }
                       ?>

              <li id="programLists<?php echo $programmeListCount;?>">
                       <!--sort_list start -->
                       <div class="sorting_places_wrap  list_sorting_view">
                        <div class="col-lg-5 col-md-5 col-sm-5 " style="padding:0px;">
                        <div class="thumb_wrape" style="padding:2%;" >
                        <div class="galleryBlock">
                          
                           <span class="image">
                           <?php if ($row->adp_photo != "") { ?>
                           <a href="<?php echo SITE_URL ?>adventure-programme.php?id=<?php echo $row->adp_id ?>"> <img  style="height:348px;" src="<?php echo SITE_URL ?>uploads/programme/photo/<?php echo $row->adp_photo; ?>" alt="" width="161" height="105" /></a>
                           <?php } else { ?>
                           <a href="<?php echo SITE_URL ?>adventure-programme.php?id=<?php echo $row->adp_id ?>"> <img  style="height:348px;" src="<?php echo SITE_URL ?>images/no_image.gif" alt="" width="161" height="105" /></a>
                           <?php } ?>
                           </span>
                        </div>
                        </div>
                        </div>
                        
                         <div class="col-lg-7 col-md-7 col-sm-7">
                        <div class="top_head_bar" >
                        <div style="float: left;  width:70%;">
                       <h4><a href="#"><?php echo $objBase->sub_str($row->adp_name,60)?></a></h4>
                        <h5 style=" font-weight: bold; margin-top:2%; color: #000000; "><i class="fa fa-square <?php echo $cls; ?>"  aria-hidden="true"></i> &nbsp; <?php echo "("?><?php echo $objProgramme->GetProgramType($row->adp_type);?><?php echo ","?><?php echo $objProgramme->GetProgramCategory($row->adp_category); ?><?php echo ")"?> </h5>
                        </div>
                        <div style="float: right; width:29%; margin-top:4%;">
                        <h3 class="prices_style"><i class="fa fa-inr rupeesicon" aria-hidden="true" style=" font-size: 19px !important; color: #ffffff;"></i><?php echo $row->adp_price ?></h3>
                        </div>
                        </div>
                        <div style="border-top: 1px solid #d3d3d3; margin-top:12%;"></div>
                        <div class="bottom_desc ">
                      
                         <!-- desc icons Start-->
                         <!-- desc icons End-->
                          <ul class="sort_place_icons">
                                       <li><div style="float:left;width:10%"><i class="fa fa-location-arrow listreporticon"  aria-hidden="true"></i></div>
                                         <div style="float:left; width:90%;"> <span class="starting_text" ><?php echo $row->adp_report_point ?> - <?php echo $objProgramme->showProgramDuration($row->adp_duration, $row->adp_from_date, $row->adp_to_date); ?>
                                          </span></div>
                                       </li>
                                       <li>
                                       <div style="float:left; width:10%"> <i class="fa fa-calendar listreporticon" aria-hidden="true"></i>
                                       </div> 
                                         <div style="float:left; width:90%; "> <span class="time_date"><span ><?php echo $row->adp_period ?> </span>
                                          </span>
                                          </div>
                                       </li>
                                       <li><div style="float:left;width:10%;"><i class="fa fa-briefcase listreporticon" aria-hidden="true"></i>       </div>             
                                         <div style="float:left; width:90%;"> <span class="includes_text" >includes:<?php echo $row->adp_cover_services ?>
                                          </span>
                                          </div>
                                       </li>
                                       <li><div style="float:left; width:10%;"><i class="fa fa-inr listreporticon" aria-hidden="true" ></i></div>
                                       <div style="float:left; width:90%;"> <span><?php echo $row->adp_price ?></span></div>
                                       </li>
                                    </ul>

                        <div class="buttonBlock " style="margin-top: 5%;"  >
                        <div class="greenBtnLink col-md-12 col-sm-12 ">
                           <a href="<?php echo SITE_URL ?>adventure-programme.php?id=<?php echo $row->adp_id ?>&ty=s"><button class="list_view_details btns " style="width: 100%; margin-bottom:3%;"  >
                           <span style="font-size: 19px; font-weight: bold;" class="font"> Book Now</span>
                           </button></a>
                        </div>
                       
                        
                        <div class="clear"></div>
                        <div class="clear"></div>
                     </div>
                        </div>
                        
                      </div>
                      </div>
                      
                      
                      </li>

            <?php
               endforeach;?>
                <!-- set Coustants to ajax to get progrmas -->
               <input type="hidden" value="programLists<?php echo $programmeListCount?>" id="AddOn" IsGettingPrograms="false">
               <?php $programmeListCount -=2;?>
               <input type="hidden" value="programLists<?php echo $programmeListCount?>" id="HitOnFocus" IsGettingPrograms="false">
               <input type="hidden" value="<?php echo $NoOfPage?>" id="NoOfPage">
               <input type="hidden" value="<?php echo $ProgramType?>" id="ProgramType">
               <?php } else {
               
               echo '<li>No Participate Programmes</li>';
               
               }
               ?> 
               </ul>
               <!-- set Coustants to ajax to get progrmas -->
               <input type="hidden" value="programLists<?php echo $programmeListCount?>" id="AddOn" IsGettingPrograms="false">
               <?php $programmeListCount -=2;?>
               <input type="hidden" value="programLists<?php echo $programmeListCount?>" id="HitOnFocus" IsGettingPrograms="false">
               <input type="hidden" value="<?php echo $NoOfPage?>" id="NoOfPage">
               <!-- <input type="hidden" value="programLists<?php echo $programmeListCount?>" id="AppendOn">
               <input type="hidden" value="false" id="IsGettingProgrmas"> -->
      </div>
   </div>
</div>

<!-- <div class="pagingDiv">
   <label>1-4 of 20</label>
   
   <ul class="paging">
   
   <li class="prev"><a href="#">Previous</a></li>
   
      <li class="next"><a href="#">Next</a></li>
   
   </ul>
   
   </div>-->
<div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div>
<div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div>    
<div class="clear"><img src="images/spacer.gif" alt="" /></div>
</div><!-- BODY end -->
<?php include_once(PATH_INCLUDES . "/footer_new.php"); ?>
<script type="text/javascript" src="<?php echo SITE_PATH_WEBROOT ?>/Scripts/programList.js"/></script>
