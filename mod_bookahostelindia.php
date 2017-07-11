<?php
//include Master controller
include(PATH_CONTROLLERS."/MasterController.php");
$City_Options = $OBJ_MASTER_CONTROLLER->getHostelCites();
?>
<form class="fl" id="HostelsSearchbycityForm" method="post" action="<?php echo Enum::SubmitForms()->MOD_BOOKHOSTELINDIA_POST; ?>" target="_blank">
      <div class="travel_form_element">
        <label>All Destination</label>
           <select class="selectpicker homeHostelSearchInputComman" name="cityId" id="destination"  data-live-search="true"  title="Your Destination">
              <?php echo $City_Options; ?>
           </select>
      </div>
      <div class="travel_form_element">
        <label>Adults</label>
           <select class="selectpicker homeHostelSearchInputComman" id="adults" data-live-search="true" title="Adults" name="persons" >
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
           </select>
      </div>
      <div class="travel_form_element">
        <label>Check in date</label>
          <input  type="text" name="checkin" class="homeHostelSearchInputComman" id="Check_in_date" placeholder="dd/mm/yyyy" value="<?php echo @$objMasters->getCheckInDate();?>" readonly="readonly" >
            <i class="fa fa-calendar"></i> 
      </div>
      <div class="travel_form_element">
        <label>Check out date</label>
          <input type="text" name="checkout" class="homeHostelSearchInputComman" id="Check_out_date" placeholder="dd/mm/yyyy"  readonly="readonly"  value="<?php echo @$objMasters->getCheckOutDate();?>">
             <i class="fa fa-calendar"></i> 
      </div>
      <div class="travel_form_element">
        <input type="hidden" name="cityIdParam" id="cityIdParam" value="6"/>
        <input type="hidden" name="numNightsParam" id="numNightsParam" value="1"/>
        <button type="submit" class="btn-travel btn-yellow">Search</button>
      </div>
</form>
<script type="text/javascript">
  setCalDate("Check_in_date",'<?php echo @$objMasters->getCheckInDate();?>','<?php echo @$objMasters->getCheckInDate();?>','<?php echo @$objMasters->getBookingLastDate();?>');
  setCalDate("Check_out_date",'<?php echo @$objMasters->getCheckOutDate();?>','<?php echo @$objMasters->getCheckOutDate();?>','<?php echo $objBase->addDayToDate(@$objMasters->getBookingLastDate(),"d/m/Y",4);?>');
</script>
