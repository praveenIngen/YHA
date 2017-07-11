<?php
include_once("includes/framework.php");
//$json = "";
$city = "";
try {

	$data = array();
	if (JRequest::getVar('search_type') != "") {
	    $data['search_type'] = JRequest::getVar('search_type');
	}
	if (JRequest::getVar('selected_slug') != "") {
	    $data['selected_slug'] = JRequest::getVar('selected_slug');
	}
	if (JRequest::getVar('target_div_id') != "") {
	    $data['target_div_id'] = JRequest::getVar('target_div_id');
	}
	if($data==null || empty($data))
		return;

	//post json
	$jsonToPost = array(
		"search_type"=>"search",
		"selected_slug"=>$data['selected_slug'],
		"target_div_id"=>"cities-select",
		);
	//test URl
	// 	https://affiliates.hihostels.com/update_selections?lang=E&search_type=search&selected_slug=bg&target_div_id
	// =cities-select
	$url =sprintf("%s&%s", Enum::InternationalHostels()->GET_INTERNATIONAL_CITIES, http_build_query($jsonToPost));
	
	$opts = array('http' =>
	    array(
	        'method'  => 'POST',
	        'header'  => "Accept: html" . "Content-Type: html",
	        'content' => json_encode($data)
	    )
	);
	$context  = stream_context_create($opts);
	$result = file_get_contents($url, false, $context);
	if($result!=null)
	{
		$doc = new DOMDocument();
		$doc->loadHTML($result);
		$xpath = new DOMXpath($doc);
		$xpath_resultset = $xpath->query('//*[@name="city_slug"]');
		$city = "";
		foreach ($xpath_resultset as $a) {
			if($a->nodeName=="select" || $a->nodeName=="text")
			{
				$city = (strip_tags($doc->saveHTML($a), '<option>'));
			}   
		}
		//$city = str_replace('"', "'", $city);
		//$json = '{"status":"'.Enum::HttpStatus()->OK.'","msg": "Got cities.","result":"'.$city.'"}';
	}else{
		//$json = '{"status":"'.Enum::HttpStatus()->ERROR.'","msg": "Can not get cities.","result":"null"}';
	}

} catch (Exception $e) {
	//$json = '{"status":"'.Enum::HttpStatus()->ERROR.'","msg": "'.$e->getMessage().'","result":"null"}';
}
echo $city;
//echo $json;
exit;   
?>