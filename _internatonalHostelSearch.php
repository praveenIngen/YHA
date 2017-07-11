<?php 
include_once("includes/framework.php");
//get international Countries
$doc = new DOMDocument();
$doc->loadHTMLFile(Enum::InternationalHostels()->GET_HOSTELS);
$xpath = new DOMXpath($doc);
$xpath_resultset = $xpath->query('//*[@name="country_slug"]');
$country = "";
foreach ($xpath_resultset as $a) {
	if($a->nodeName=="select" || $a->nodeName=="text")
	{
		$country = (strip_tags($doc->saveHTML($a), '<option>'));
	}   
 }

$now  = new DateTime();
$date = DateTime::createFromFormat('m-d-Y',$now->format('m-d-Y'));
$date->modify('-1 day');
$checkInDate = $date->format(DATE_FORMAT);

$date = DateTime::createFromFormat('m-d-Y',$now->format('m-d-Y'));
$checkOutDate = $date->format(DATE_FORMAT);

?>

<form class="fl" id="InternationalHostelsSearchForm" method="post" action="<?php echo Enum::SubmitForms()->INTERNATIONAL_HOSTEL; ?>" target="_blank">
      <div class="travel_form_element">
        <label>Country</label>
           <select class="internationlSelectpicker homeHostelSearchInputComman" name="country_slug" id="internationl_country"  data-live-search="true"  title="Your Destination">
              <?php echo $country; ?>
           </select>
      </div>
      <div class="travel_form_element">
        <label>Cities</label>
           <select class="internationlSelectpicker homeHostelSearchInputComman" id="internationalCity" data-live-search="true" title="City" name="city_slug" >
              <option value="">Select City</option>
           </select>
      </div>
      <div class="travel_form_element">
        <label>Check in date</label>
          <input  type="text" name="arrival" class="homeHostelSearchInputComman" id="IntrNational_check_in_date" placeholder="dd/mm/yyyy" value="<?php echo @$checkInDate;?>" readonly="readonly" >
            <i class="fa fa-calendar"></i> 
      </div>
      <div class="travel_form_element">
        <label>Check out date</label>
          <input type="text" name="departure" class="homeHostelSearchInputComman" id="IntrNational_check_out_date" placeholder="dd/mm/yyyy"  readonly="readonly"  value="<?php echo @$checkOutDate;?>">
             <i class="fa fa-calendar"></i> 
      </div>
      <div class="travel_form_element">
        <button type="submit" class="btn-travel btn-yellow">Search</button>
      </div>
</form>
<script type="text/javascript">
  setCalDate("IntrNational_check_in_date",'<?php echo @$checkInDate;?>','<?php echo @$checkInDate;?>',null);
  setCalDate("IntrNational_check_out_date",'<?php echo @$checkOutDate;?>','<?php echo @$checkOutDate;?>',null);
  $("#internationl_country option[value='in']").each(function() {$(this).remove();});
   $('#internationl_country').selectpicker();
   $('#internationalCity').selectpicker();
  //$('#internationalCity').selectpicker('refresh');
</script>
