<?php

/*

 * 

 */

class clsMasters {



    public function __contruct() {

        //

    }



    public function getStateInfo($id) {

        global $objDB;

        $sql = "SELECT * from #__state WHERE state_id='$id'";

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        if ($objDB->num_rows($result)) {

            return $objDB->loadObject();

        } else {

            return false;

        }

    }



    public function list_states($whr = '') {

        global $objDB;

        $sql = "SELECT * from #__state WHERE status=1 $whr ORDER BY state_name";

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        if ($objDB->num_rows($result)) {

            return $objDB->loadObjectList();

        } else {

            return false;

        }

    }



    public function list_state_options($sel = '') {

        global $objDB;

        $options = null;

        $rows = $this->list_states();

        if ($rows) {

            foreach ($rows as $row) {

                if ($sel == $row->state_code) {

                    $options .="<option value='" . $row->state_code . "' selected>" . $row->state_name . " (" . $row->state_code . ")</option>";

                } else {

                    $options .="<option value='" . $row->state_code . "'>" . $row->state_name . " (" . $row->state_code . ")</option>";

                }

            }

        }

        return $options;

    }



    public function getCityInfo($id) {

        global $objDB;

        $sql = "SELECT * from #__city WHERE city_id='$id'";

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        if ($objDB->num_rows($result)) {

            return $objDB->loadObject();

        } else {

            return false;

        }

    }



    public function list_city($whr = '') {

        global $objDB;

        $sql = "SELECT * from #__city WHERE status=1 AND is_deleted=0 $whr ORDER BY city_name";

        $result = $objDB->setQuery($sql);

        if ($objDB->num_rows($result)) {

            return $objDB->loadObjectList();

        } else {

            return false;

        }

    }



    public function list_city_options($sel = '', $state = '', $others = true) {

        global $objDB;

        $options = null;

        $rows = $this->list_city(" AND TRIM(state_code)='" . trim($state) . "' ");

        if ($rows) {

            foreach ($rows as $row) {

                if ($sel == $row->city_id) {

                    $options .="<option value='" . $row->city_id . "' selected='selected'>" . $row->city_name . "</option>";

                } else {

                    $options .="<option value='" . $row->city_id . "'>" . $row->city_name . "</option>";

                }

            }

        }

        if ($others) {

            if ($sel == 'others') {

                //$options .="<option value='others' selected='selected'>Others</option>";

            } else {

                //$options .="<option value='others'>Others</option>";

            }

        }

        return $options;

    }



    public function getUnitInfo($id) {

        global $objDB;

        $sql = "SELECT * from #__unit WHERE unit_id='$id'";

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        if ($objDB->num_rows($result)) {

            return $objDB->loadObject();

        } else {

            return false;

        }

    }



    public function getLYHInfo($id) {

        global $objDB;

        $sql = "SELECT * from #__lyh WHERE lyh_id='$id'";

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        if ($objDB->num_rows($result)) {

            return $objDB->loadObject();

        } else {

            return false;

        }

    }



    public function getUnitInfoByCode($unit_code) {

        global $objDB;

        $sql = "SELECT * from #__unit WHERE unit_code='$unit_code'";

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        if ($objDB->num_rows($result)) {

            return $objDB->loadObject();

        } else {

            return false;

        }

    }



    public function get_state_page_info($state_code, $state_page_id = '') {

        global $objDB;

        if ($state_code) {

            if ($state_page_id) {

                $sql_whr = " AND a.state_code='$state_code' AND a.page_id='$state_page_id' ";

            } else {

                $sql_whr = " AND a.state_code='$state_code' ";

            }

            $sql = "SELECT b.*,c.state_name FROM #__state_pages a 

            LEFT JOIN #__state_pages_content b ON b.page_id=a.page_id 

            LEFT JOIN #__state c ON c.state_code=a.state_code 

            where b.type='LIVE' $sql_whr ";

            $result = $objDB->setQuery($sql);

            //echo $objDB->getQuery();

            return $objDB->loadObject($result);

        }

    }



    public function getStateName($state_code) {

        global $objDB;

        $sql = "SELECT state_name from #__state WHERE TRIM(state_code)='$state_code'";

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        if ($objDB->num_rows($result)) {

            return $row = $objDB->loadObject()->state_name;

        } else {

            return false;

        }

    }



    public function getCityName($city_id) {

        global $objDB;

        $sql = "SELECT city_name from #__city WHERE city_id='$city_id'";

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        if ($objDB->num_rows($result)) {

            return $row = $objDB->loadObject()->city_name;

        } else {

            return false;

        }

    }



    public function getUnitName($unit_code) {

        global $objDB;

        $sql = "SELECT unit_name from #__unit WHERE unit_code='$unit_code'";

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        if ($objDB->num_rows($result)) {

            return $row = $objDB->loadObject()->unit_name;

        } else {

            return false;

        }

    }



    public function getStateLinks($show_state_links = false, $state_page_id = '') {

        global $objDB, $objBase;

        $str = '';

        if ($show_state_links) {

            $state_code = $_SESSION['state_selected'];

            $sql = "SELECT b.*,c.state_name FROM #__state_pages a 

            LEFT JOIN #__state_pages_content b ON b.page_id=a.page_id 

            LEFT JOIN #__state c ON c.state_code=a.state_code 

            where b.type='LIVE' AND a.state_code='$state_code'";

            $result = $objDB->setQuery($sql);

            //echo $objDB->getQuery();

            $rows = $objDB->loadObjectlist();

            $str.='<ul class="links">';

            if ($rows) {

                foreach ($rows as $row) {

                    if ($state_page_id == $row->page_id)

                        $str.='<li><a href="' . SITE_URL . 'state-page.php?state_page_id=' . $row->page_id . '" class="active">' . $row->title . '</a></li>';

                    else

                        $str.='<li><a href="' . SITE_URL . 'state-page.php?state_page_id=' . $row->page_id . '">' . $row->title . '</a></li>';

                }

            }

            if (strstr($objBase->curPageURL(), 'state-programmes.php')) {

                $str.='<li><a href="' . SITE_URL . 'state-programmes.php?state_code=' . $state_code . '" class="active">State Level Programme</a></li>';

            } else {

                $str.='<li><a href="' . SITE_URL . 'state-programmes.php?state_code=' . $state_code . '">State Level Programme</a></li>';

            }

            if (strstr($objBase->curPageURL(), 'state-photo-gallery.php')) {

                $str.='<li><a href="' . SITE_URL . 'state-photo-gallery.php?state_code=' . $state_code . '" class="active">Photo Gallery</a></li>';

            } else {

                $str.='<li><a href="' . SITE_URL . 'state-photo-gallery.php?state_code=' . $state_code . '">Photo Gallery</a></li>';

            }

            $str.='</ul>';

        }

        return $str;

    }



    function convertCSVtoAssocMArray($file, $delimiter) {

        $result = Array();

        $size = filesize($file) + 1;

        $file = fopen($file, 'r');

        $keys = fgetcsv($file, $size, $delimiter);

        while ($row = fgetcsv($file, $size, $delimiter)) {

            for ($i = 0; $i < count($row); $i++) {

                if (array_key_exists($i, $keys)) {

                    $row[$keys[$i]] = $row[$i];

                }

            }

            $result[] = $row;

        }

        fclose($file);

        return $result;

    }



    function isValidFile($f, $a) {

        $t = self::file_extension($f);

        return ( in_array($t, $a) ) ? true : false;

    }



    function file_extension($filename) {

        return strtolower(end(explode(".", $filename)));

    }



    public function get_city_id_by_CityName($cityName) {

        global $objDB;

        $sql = "SELECT city_id from #__city WHERE city_name='" . trim($cityName) . "' ";

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        if ($objDB->num_rows($result)) {

            return $row = $objDB->loadObject()->city_id;

        } else {

            return false;

        }

    }



    function saveOtherCityOption($city_name, $state) {

        if ($city_name != "" && $state != "") {

            global $objDB;

            $result = $objDB->setQuery("Select city_id From #__city where city_name ='" . trim($city_name) . "' AND state_code='" . trim($state) . "'");

            $row = $objDB->loadObject();

            if ($row) {

                return $row->city_id;

            } else {

                return $objDB->insert_data("#__city", array("city_name" => trim($city_name), "state_code" => trim($state), "status" => "1"));

            }

        }

    }



    public function list_unit_options($sel = '', $city = '') {

        global $objDB;

        $options = null;

        $sql = "SELECT * from #__unit WHERE status = 1 AND city_code = '$city'"; //

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        $rows = $objDB->loadObjectList();

        if ($rows) {

            foreach ($rows as $row) {

                if ($sel == $row->unit_code) {

                    $options .="<option value='" . $row->unit_code . "' selected='selected'>" . $row->unit_name . "</option>";

                } else {

                    $options .="<option value='" . $row->unit_code . "'>" . $row->unit_name . "</option>";

                }

            }

        }

        if ($sel == 'others') {

            //$options .="<option value='others' selected='selected'>Others</option>";

        } else {

            //$options .="<option value='others'>Others</option>";

        }

        return $options;

    }



    public function list_lyh_options($sel = '', $city = '') {

        global $objDB;

        $options = null;

        $sql = "SELECT * from #__lyh WHERE status = 1 AND city_code = '$city'"; //

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        $rows = $objDB->loadObjectList();

        if ($rows) {

            foreach ($rows as $row) {

                if ($sel == $row->lyh_code) {

                    $options .="<option value='" . $row->lyh_code . "' selected='selected'>" . $row->lyh_name . "</option>";

                } else {

                    $options .="<option value='" . $row->lyh_code . "'>" . $row->lyh_name . "</option>";

                }

            }

        }

        if ($sel == 'others') {

            //$options .="<option value='others' selected='selected'>Others</option>";

        } else {

            //$options .="<option value='others'>Others</option>";

        }

        return $options;

    }

    

    public function getStateInfoCode($code) {

        global $objDB;

        $sql = "SELECT * from #__state WHERE state_code='$code'";

        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());

        if ($objDB->num_rows($result)) {

            return $objDB->loadObject();

        } else {

            return false;

        }

    }

    

    public function list_unit($whr = '') {

        global $objDB;

        $sql = "SELECT * from #__unit WHERE status=1 $whr ORDER BY unit_name";

        $result = $objDB->setQuery($sql);

        if ($objDB->num_rows($result)) {

            return $objDB->loadObjectList();

        } else {

            return false;

        }

    }
    //today
    public function Today()
    {
        $now  = new DateTime();
        $date = DateTime::createFromFormat('m-d-Y',$now->format('m-d-Y'));
        return$date->format(DATE_FORMAT);
    }

    public function getCheckInDate()
    {
        $now  = new DateTime();
        $date = DateTime::createFromFormat('m-d-Y',$now->format('m-d-Y'));
        $date->modify('+2 day');
        return$date->format(DATE_FORMAT);
    }

    public function getCheckOutDate()
    {
        $now  = new DateTime();
        $date = DateTime::createFromFormat('m-d-Y',$now->format('m-d-Y'));
        $date->modify('+3 day');
        return $date->format(DATE_FORMAT);
    }

    public function getBookingLastDate()
    {
        $now  = new DateTime();
        $date = DateTime::createFromFormat('m-d-Y',$now->format('m-d-Y'));
        $date->modify('+60 day');
        return $date->format(DATE_FORMAT);
    }

    public function compareDates($startDate,$endDate,$ourDateFormat=null)
    {
        $startDateJson = $this->getDateInJson($startDate,$ourDateFormat);
        $endDateJson = $this->getDateInJson($endDate,$ourDateFormat);
        $startDateNew = $this->dateFormat($startDateJson->DT.'-'.$startDateJson->MN."-".$startDateJson->YR,null,"Y-m-d");
        $endDateNew = $this->dateFormat($endDateJson->DT."-".$endDateJson->MN."-".$endDateJson->YR,null,"Y-m-d");
        if($endDateNew>$startDateNew)
        {
            return 1;
        }else if($endDateNew<$startDateNew)
        {
            return -1;
        }else{
            return 0;
        }
    }
    /*function To convert get date in array*/
    public function getDateInJson($date,$currentFormat=null)
    {
        $date = split('[/.-]', $date);
        if($currentFormat==null)
        {
            $currentFormat = "DT/MN/YR";
        }
        $currentFormat = split('[/.-]', $currentFormat);
        $DateArray = array();
        for($i = 0;$i<Count($date);$i++)
        {
            $DateArray[$currentFormat[$i]] = $date[$i];
        }
        $DateArray = (object)$DateArray;
        return $DateArray;
    } 

    /*function to convert all date time to comman format*/ 
    public function dateFormat($date,$currentFormat=null,$newFormat=null)
    {
        if($date=='' || $date==0 || $date==null)
        {
            return '';
        }
        if(is_numeric($date))
        {
            $date = date("Y-m-d",$date);
            $currentFormat = "YR/MN/DT";      
        }
        if($currentFormat!=null)
        {
           $dateJson  = $this->getDateInJson($date,$currentFormat);
        }
        else{
           $dateJson = $this->getDateInJson($date,"DT/MN/YR");   
        }
        if(!is_numeric($dateJson->MN))
        {
            $monthNumber = date_parse($dateJson->MN);
            $dateJson->MN = $monthNumber['month'];
        }
        $date = DateTime::createFromFormat('m-d-Y',$dateJson->MN."-".$dateJson->DT."-".$dateJson->YR);
        if($newFormat!=null)
        { 
            return $date->format($newFormat);
        }
        else{
            return $date->format(DATE_FORMAT);
        }
    }

}

$objMasters = new clsMasters();