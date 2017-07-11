<?php
include_once("includes/framework.php");
//get Highligh content
$HighlightsInfo = json_decode($objPage->getHighlightsInfo()->content);
$defaultProgram = array(
	"adp_uploadImgPath"=> SITE_URL."uploads/programme/photo/",
	"adp_photo"=>"default.jpg",
	);
$highLightProgram = array();
if($HighlightsInfo!=null)
{
	foreach ($HighlightsInfo as $key => $value) {
		if(isset($value->id) && $value->id!=null)
		{
			$adventureProgram =  $objProgramme->getProgramByField($value->id,array("adp_id","adp_name","adp_code","adp_duration","adp_price","adp_photo"), 1);
			$adventureProgram = $objBase->addNewPropertyTo($adventureProgram, array("adp_uploadImgPath"=> SITE_URL."uploads/programme/photo/"));
			$highLightProgram[$key] = $adventureProgram!=null?$adventureProgram:$defaultProgram;
			//$highLightProgram[$key]["uploadImgPath"] = $defaultProgram["adp_photo"];
		}
	}
}
pre($highLightProgram);
die;
?>

<script type="text/javascript">

	function hideDiv() {

		document.getElementById("highlightsDiv").style.display = "none";

		document.getElementById("highlightsCloseDiv").style.display = "none";

	}

</script>

<style type="text/css">	

	#highlightsDiv {

		position:fixed;

		left:0px;

		bottom:-285px;

		width:250px;

		height:300px;

		z-index:999;

		border:#F00 1px solid;

		/*background:#CC9900*/

		background:#cecece;

	}

	#highlightsCloseDiv {

		background:#F7941E;

		width:250px;

		height:13px;

		font-family:Verdana, Geneva, sans-serif;

		font-size:9px;

		padding-bottom:2px;

		font-weight:bold;

	}

	#highlightsCloseDiv a:link, a:active, a:hover, a:visited {

		color:#FFF;

		text-decoration:none

	}

</style>

<div id="highlightsDiv">

	<div id="highlightsCloseDiv" align="right"><a href="javascript:void(0)" onClick="hideDiv()">Close[X]</a>&nbsp;&nbsp;</div>

	<div style="height:200px; ">

		<?php echo $HighlightsInfo->content; ?>

	</div>

</div>

<script type="text/javascript">

	/*<![CDATA[*/

	function SlideUp(id,ms){

		obj=document.getElementById(id);

		animate('bottom',obj,-obj.offsetHeight,0,new Date(),ms);

	}

	function animate(mde,obj,f,t,srt,mS){

		var ms=new Date().getTime()-srt,now=(t-f)/mS*ms+f;

		if (isFinite(now)){

			obj.style[mde]=now+'px';

		}

		if (ms<mS){

			setTimeout(function(){ animate(mde,obj,f,t,srt,mS); },10);

		}

		else {

			obj.style[mde]=t+'px';

		}

	}

	jQuery(document).ready(function () {

		SlideUp('highlightsDiv',2000);

	});

	/*]]>*/

</script>