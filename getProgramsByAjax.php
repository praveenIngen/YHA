
<?php
   include_once("includes/framework.php");
   $GetNoOfProgramAtATime = 5;
   $data = JRequest::get('post');
   $response = (object)array(
                     "status"=>Enum::HttpStatus()->ERROR,
                     "msg"=>"Can not get progrmas!",
                     "result"=>null,
                     "AddOn"=>null,
                     "NewAddOn"=>null,
                     "HitOnFocus"=>null,
                     "NoOfPage"=>0,
                     );
   if($data!=null && !empty($data))
   {
      if($data["NoOfPage"] >=0 && !IsNull($data["AddOn"]))
      {
         $NoOfPage = (int)$data["NoOfPage"];
         $LimitCountStart =($NoOfPage-1)*$GetNoOfProgramAtATime;
         $AddOn    = $data["AddOn"];
         $ProgramType = IsNull($data["ProgramType"])?"":$data["ProgramType"];
         if($LimitCountStart<0)
         {
            $response->msg = "Bad Request!";
            echo json_encode($response);
            exit;
         }
         $programList = $objProgramme->getProgramList($ProgramType, $st, $ed,"",null,$LimitCountStart,$GetNoOfProgramAtATime);
         $getHtmlArray = toAddProgrmaListAsAHtml($programList,$LimitCountStart+1);
         $programListCountObject = $objProgramme->getProgramList($ProgramType, $st, $ed,"","Count(*)",null,null);
         $programListCountArray = (array) $programListCountObject[0];
         $programListCount = $programListCountArray["Count(*)"];
         $response->status = Enum::HttpStatus()->OK;
         $response->msg = "Got progrmas";
         $response->result = $getHtmlArray["responseHtml"];
         $response->AddOn  = $data["AddOn"];
         $response->NewAddOn = $getHtmlArray["NewAddOn"];
         $response->NoOfPage = $programListCount>($LimitCountStart+$GetNoOfProgramAtATime)?$NoOfPage+1:0;
         $response->HitOnFocus = $getHtmlArray["HitOnFocus"];
      }
   }else{

   }
   echo json_encode($response);
   exit;

   function toAddProgrmaListAsAHtml($programList,$NoOfPage)
   {
      global $objBase,$objProgramme;
      $responseHtml = '';
      if($programList)
      {
         foreach ($programList as $row):
             
              if ($row->adp_type == 'L') {
      
                  $cls = 'hostelList';
      
              } elseif ($row->adp_type == 'S') {
      
                  $cls = 'stateList';
      
              } elseif ($row->adp_type == 'I') {
      
                  $cls = 'internationalList';
      
              } else {
      
                  $cls = 'nationalList';
      
              }
            $responseHtml .= '<li id="programLists'.$NoOfPage.'">';
            $responseHtml .= '<div class="sorting_places_wrap  list_sorting_view"><div class="col-lg-5 col-md-5 col-sm-5 padding_none"><div class="thumb_wrape"><div class="galleryBlock"><span class="image">';
            if ($row->adp_photo != "") { 
               $responseHtml .= '<a href="'.SITE_URL.'adventure-programme.php?id='.$row->adp_id.'"> <img  style="height:410px; padding-top:7px; padding-bottom:7px;" src="'.SITE_URL .'uploads/programme/photo/'.$row->adp_photo.'" alt="" width="161" height="105" /></a>';
            } else {
               $responseHtml .=  '<a href="'.SITE_URL.'adventure-programme.php?id='.$row->adp_id.'"> <img  style="height:410px; padding-top:7px; padding-bottom:7px;" src="'.SITE_URL.'images/no_image.gif" alt="" width="161" height="105" /></a>';
            }
            $responseHtml .= '</span></div></div></div>';

            $responseHtml .= '<div class="col-lg-7 col-md-7 col-sm-7"><div class="top_head_bar" ><div style="float: left;  width:70%;"><h4><a href="javascript:void(0)">'.$objBase->sub_str($row->adp_name,60).'</a></h4><h5 style=" font-weight: bold; margin-top:2%; "><i class="fa fa-square '.$cls.'"  aria-hidden="true"></i> &nbsp; ( '.$objProgramme->GetProgramType($row->adp_type).','.$objProgramme->GetProgramCategory($row->adp_category).')</h5></div><div style="float: right; width:29%; margin-top:4%;"><h3 class="prices_style"><i class="fa fa-inr rupeesicon" aria-hidden="true" style=" font-size: 19px !important; color: #ffffff;"></i>'.$row->adp_price.'</h3></div></div><div style="border-top: 1px solid #d3d3d3; margin-top:12%;"></div><div class="bottom_desc ">';
             
            $responseHtml .= '<ul class="sort_place_icons"><li><div style="float:left;width:10%"><i class="fa fa-location-arrow listreporticon"  aria-hidden="true"></i></div><div style="float:left; width:90%;"> <span class="starting_text" >'.$row->adp_report_point.'-'.$objProgramme->showProgramDuration($row->adp_duration, $row->adp_from_date, $row->adp_to_date).'</span></div></li><li><div style="float:left; width:10%"> <i class="fa fa-calendar listreporticon" aria-hidden="true"></i></div> <div style="float:left; width:90%; "> <span class="time_date"><span >'.$row->adp_period.'</span></span></div></li><li><div style="float:left;width:10%;"><i class="fa fa-briefcase listreporticon" aria-hidden="true"></i></div><div style="float:left; width:90%;"> <span class="includes_text" >includes:'.$row->adp_cover_services.'</span></div></li><li><div style="float:left; width:10%;"><i class="fa fa-inr listreporticon" aria-hidden="true" ></i></div><div style="float:left; width:90%;"> <span>'.$row->adp_price.'</span></div></li></ul>';
            $responseHtml .=  '<div class="buttonBlock " style="margin-top: 5%;"  ><div class="greenBtnLink col-md-12 col-sm-12 "><a href="'.SITE_URL.'adventure-programme.php?id='.$row->adp_id.'&ty=s"><button class="list_view_details btns " style="width: 100%; margin-bottom:3%;"  ><span style="font-size: 19px; font-weight: bold;" class="font"> Book Now</span></button></a></div><div class="clear"></div><div class="clear"></div></div></div></div></div>';
            $responseHtml .=   '</li>';
            $NoOfPage++;
         endforeach;
                  
      } else {  
         $responseHtml .= '<li>No Participate Programmes</li>';
      }
      $NoOfPage--;
      $NewAddOn = 'programLists'.$NoOfPage;
      $NoOfPage -=2;
      $HitOnFocus = "programLists".$NoOfPage;
      $result = array("NewAddOn"=>$NewAddOn,"responseHtml"=>$responseHtml,"HitOnFocus"=>$HitOnFocus);
      return $result; 
   }
?>