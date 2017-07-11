<?php
include_once("includes/framework.php");
$response = (object)array(
                     "status"=>Enum::HttpStatus()->ERROR,
                     "msg"=>"Can not get details!",
                     "available_seats"=>0,
                     );
$data = JRequest::get('post');
 // $data['ptd_id'] = 7;
 // $data['date'] = "24-12-2016";
if($data!=null && !empty($data))
{
	$response->status 	=	Enum::HttpStatus()->OK;
	$response->msg 		=	"Ok"; 
	$data['date'] 		= $objMasters->dateFormat($data['date'],null,"Y-m-d");
	$data['date']      =  strtotime($data['date']);
	$ProgramTransport = $objProgramme->getProgramTransport('',false,$data['ptd_id'],'is_sold_out = 0');
	if($ProgramTransport!=null && !empty($ProgramTransport))
	{
		//echo $objMasters->dateFormat($ProgramTransport[0]->start_date,null,"Y-m-d")."  ".$objMasters->dateFormat($ProgramTransport[0]->end_date,null,"Y-m-d");
		//echo $objMasters->dateFormat($data['date'],null,"Y-m-d");
		$prog = $objProgramme->getInfo($ProgramTransport[0]->adp_id, 1);
		if($ProgramTransport[0]->start_date<=$data['date'] && $ProgramTransport[0]->end_date>=$data['date']){
			$TotalBookedPrograms = $objProgramme->getBookedProgramTransportSeats($data['ptd_id'],$data['date'],'AND status="'.Enum::Status()->ACTIVE.'"');
			$available_seats = $ProgramTransport[0]->no_of_seats - $TotalBookedPrograms[0]->total_seats;
			if($available_seats>0)
          	{
          		$response->available_seats = $available_seats;
        	    $response->max_no_of_seats = $prog->adp_category=="F"? $available_seats:1;
            }else{
            	$response->available_seats = 0;
	            $response->max_no_of_seats = 0;
          	}
		}
	}
}
echo json_encode($response);
die;
?>