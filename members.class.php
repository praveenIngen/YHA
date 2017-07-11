<?php
//include MemberShipController controller
include_once(PATH_CONTROLLERS."/MemberShipController.php");

class clsMembers {

    public function __contruct() {
        //
    }

    function getMemberShipNumberInfo($membership_number,$whr=null) {
        global $objDB;
        $condi = '';
        if($whr!=null && $whr!='')
        {
          $condi = ' AND '.$whr;  
        }
        $sql = "SELECT a.*,b.* from #__member a 
        JOIN #__member_plans b ON b.member_id=a.member_id        
        WHERE b.membership_number= '$membership_number' $condi ";
        $result = $objDB->setQuery($sql);

        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObject($result);
        } else {
            return false;
        }
    }
    
    function getMemberShipNumberadmitInfo($membership_number,$whr=null) {
        global $objDB;
        $condi = '';
        if($whr!=null && $whr!='')
        {
          $condi = $whr;  
        }
        $sql = "SELECT * from #__member as m 
        JOIN #__member_plans as mp ON m.member_id= mp.member_id        
        WHERE mp.membership_number= '$membership_number' $condi ";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObject($result);
        } else {
            return false;
        }
    }


    public function approve_member_payment($member_plan_id, $response) {
        global $objDB, $MEMBERSHIP_CONTROLLER, $objMasters;
        //die(">>>".$member_plan_id);
        $memberData = $this->get_member_data($member_plan_id);
        $data = (array)$memberData;
        $data['TransactionID'] = $response['TransactionID'];
        $data['PaymentID'] = $response['PaymentID'];
        $data['TransactionResponse'] = serialize($response);
        $data['udate'] = time();
        $data['status'] = 1;
        $affected_rows_member_plans = $objDB->update_data("#__member_plans", "member_plan_id", $data, $member_plan_id);

        //call api to save data for offline membership
        $PostData = array_merge((array)$memberData,$data);
        $PostData['city'] = $objMasters->getCityName($PostData['city']);
        $PostData['state'] = $objMasters->getStateName($PostData['state']);
        $PostData['online_membership_card'] = SITE_URL."print-e-card-ind.php?mid=".$memberData->member_plan_id;
        if($PostData!=null && !empty($PostData))
        {
            $PostData =  json_encode($PostData);
            $ApiResponse = $MEMBERSHIP_CONTROLLER->Put($PostData); 
              
        }
        if ($affected_rows_member_plans) {
            //$this->setMembershipCommission($memberData, true);
            $this->send_membership_email($member_plan_id);
            $this->send_email_to_admin($member_plan_id);
        }
        return $affected_rows_member_plans;
    }

    public function decline_member_payment($member_plan_id) {
        global $objDB, $MEMBERSHIP_CONTROLLER, $objMasters;
        //die(">>>".$member_plan_id);
        $memberData = $this->get_member_data($member_plan_id);
        $data = (array)$memberData;
        $data['TransactionID'] = '';
        $data['PaymentID'] = '';
        $data['TransactionResponse'] = '';
        $data['udate'] = time();
        $data['status'] = 0;
        $affected_rows_member_plans = $objDB->update_data("#__member_plans", "member_plan_id", $data, $member_plan_id);

        //call api to save data for offline membership
        $PostData = array_merge((array)$memberData,$data);
        $PostData['city'] = $objMasters->getCityName($PostData['city']);
        $PostData['state'] = $objMasters->getStateName($PostData['state']);
        $PostData['online_membership_card'] = SITE_URL."print-e-card-ind.php?mid=".$memberData->member_plan_id;
        if($PostData!=null && !empty($PostData))
        {
            $PostData =  json_encode($PostData);
            $ApiResponse = $MEMBERSHIP_CONTROLLER->Put($PostData); 
              
        }
        if ($affected_rows_member_plans) {
            //$this->setMembershipCommission($memberData, true);
            //$this->send_membership_email($member_plan_id);
            //$this->send_email_to_admin($member_plan_id);
        }
        return $affected_rows_member_plans;
    }

    function getInfo($member_id) {
        global $objDB;
        $sql = "SELECT a.*,b.* from #__member a 
        JOIN #__member_plans b ON b.member_id=a.member_id        
        WHERE a.member_id='$member_id'";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObject($result);
        } else {
            return false;
        }
    }

    function getMerchantInfo($member_plan_id,$whr = null) {
        global $objDB;
        if(IsNull($whr))
        {
          $whr = "";   
        }
        $sql = "SELECT a.*,b.* from #__member a 
        JOIN #__member_plans b ON b.member_id=a.member_id        
        WHERE b.member_plan_id='".$member_plan_id."' ".$whr."";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObject($result);
        } else {
            return false;
        }
    }


    public function list_membership($whr = '') {
        global $objDB;
        $sql = "SELECT a.* from #__membership_plans a WHERE 1 $whr";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObjectList();
        } else {
            return false;
        }
    }

    public function list_membership_options($sel = '', $category = '') {
        global $objDB;
        $options = null;
        if ($category != "") {
            $rows = $this->list_membership(" AND status=1 AND category='$category' ");
        } else {
            $rows = $this->list_membership(" AND status=1 ");
        }
        if ($rows) {
            foreach ($rows as $row) {
                if ($sel == $row->plan_code) {
                    $options .= "<option value='" . $row->plan_code . "' selected>" . $row->name . "</option>";
                } else {
                    $options .= "<option value='" . $row->plan_code . "'>" . $row->name . "</option>";
                }
            }
        }
        return $options;
    }

    public function list_membership_prices($category = '') {
        global $objDB;
        $membership_prices = null;
        $sql = "SELECT a.plan_code, a.total_amount,a.service_tax from #__membership_plans a WHERE 1 AND a.status=1 AND a.category='$category' ";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            $rows = $objDB->loadObjectList();
            foreach ($rows as $row) {
                $service_tax_percent = $row->service_tax;
                $service_tax_value = ($row->total_amount * $service_tax_percent) / 100;
                $membership_prices[$row->plan_code] = ceil($row->total_amount + $service_tax_value);
            }
        } else {
            return false;
        }

        return $membership_prices;
    }

    public function get_membership_plan_amount($plan_code) {
        global $objDB;
        $sql = "SELECT a.total_amount,a.service_tax,a.misc_charges,a.fee FROM #__membership_plans a WHERE 1 AND a.plan_code='$plan_code' ";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        $arr = array();
        if ($objDB->num_rows($result)) {
            $row = $objDB->loadObject();
            $service_tax_percent = $row->service_tax;
            $service_tax_value = ($row->total_amount * $service_tax_percent) / 100;
            $arr['total_amount'] = (int) ceil($row->total_amount + $service_tax_value);
            $arr['plan_amount'] = $row->fee;
            $arr['service_tax'] = $service_tax_value;
            $arr['misc_charges'] = $row->misc_charges;
            return $arr;
        } else {
            return false;
        }
    }

    public function save_register($data) {
        global $Config, $objDB, $objBase, $objEncryption;
        if (strstr($data['plan_code'], 'I1') or strstr($data['plan_code'], 'I5') or strstr($data['plan_code'], 'INS_ABV_12_1') or strstr($data['plan_code'], 'INS_ABV_12_5')) {
            if ($data['plan_code'] == "" or $data['email'] == "" or $data['state'] == "" or $data['mobile'] == "") {
                return false;
            }
        } else if (strstr($data['plan_code'], 'J1') or strstr($data['plan_code'], 'S1') or strstr($data['plan_code'], 'S2') or strstr($data['plan_code'], 'L0')) {
            if ($data['plan_code'] == "" or $data['email'] == "" or $data['dob'] == "" or $data['state'] == "" or $data['mobile'] == "") {
                return false;
            }
        }
        $data['commission_amount'] = $this->getPlanCommissionAmt($data['plan_code']);
        $data['status'] = 0;
        $data['cdate'] = time();
        $data['udate'] = time();
        $data['password'] = $objEncryption->encode($objBase->GenRandomPassword());
        $memberData = $this->check_member_exists($data['email']);
        if (!$memberData->member_id) {
            $member_id = $objDB->insert_data("#__member", $data);
        } else {
            $member_id = $memberData->member_id;
        }

        if ($member_id) {
            $data['member_id'] = $member_id;
            $data['unit'] = $this->getMemberUnitCode($data['city']);
            $member_plan_id  = $objDB->insert_data("#__member_plans", $data);
            return $member_plan_id;
        }
    }

    //method to save member
    public function save_member($data) {
        global $Config, $objDB, $objBase, $objEncryption;
        if ($data['state'] == "") {
            return false;
        }

        $data['commission_amount'] = $this->getPlanCommissionAmt($data['plan_code']);
        $data['status'] = 0;
        $data['cdate'] = time();
        $data['udate'] = time();
        $data['password'] = $objEncryption->encode($objBase->GenRandomPassword());

        $data['modified_by'] = $_SESSION['user']['login']->admin_id;
        $data['modified_by_name'] = $_SESSION['user']['login']->username;

        $memberData = $this->check_member_exists($data['email']);
        if (!$memberData->member_id) {
            $member_id = $objDB->insert_data("#__member", $data);
        } else {
            $member_id = $memberData->member_id;
        }

        if ($member_id) {
            $data['member_id'] = $member_id;
            $data['unit'] = $this->getMemberUnitCode($data['city']);

            //echo "<pre>";print_r($data);die;
            $member_plan_id = $objDB->insert_data("#__member_plans", $data);
            return $member_plan_id;
        }
    }

    public function getPlanCommissionAmt($plan_code) { // Get Commission Amount
        global $objDB;
        $sql = "Select commission_amount FROM  #__membership_plans WHERE 
				plan_code='" . $plan_code . "'";
        $objDB->setQuery($sql);
        $row = $objDB->loadObject();
        return $row->commission_amount;
    }

    function getMemberUnitCode($ct) { /// Get Membership Unit
        global $objDB;
        $code = "";
        if ($ct != "") {
            $sql = "SELECT unit_id, unit_code FROM #__unit WHERE city_code='" . $ct . "' and status='1'";
            $result = $objDB->setQuery($sql);
            $num_rows = $objDB->num_rows($result);
            if ($num_rows > 1) {
                $rows = $objDB->loadObjectList();
                $last_commn_to_unit = $this->check_last_commn_to_unit($ct);
                if ($rows) {
                    foreach ($rows as $row) {
                        if ($row->unit_id > $last_commn_to_unit) {
                            return $row->unit_code;
                            break;
                        }
                    }
                    return $rows[0]->unit_code;
                }
            } else {
                $row = $objDB->loadObject();
                $code = $row->unit_code;
            }
        }
        return $code;
    }

    public function check_last_commn_to_unit($ct) {
        global $objDB;
        $sql = "SELECT last_comm_to FROM #__commission WHERE city='" . $ct . "' ORDER BY comm_id DESC LIMIT 1;";
        $result = $objDB->setQuery($sql);
        $num_rows = $objDB->num_rows($result);
        if ($num_rows) {
            $row = $objDB->loadObject();
            return $row->last_comm_to;
        }
    }

    function getMembershipCodeSerialNo($plan_code) { // Get Membership Plan Serial Number
        global $objDB;
        if ($plan_code != "") {
            $sql = "SELECT mem_num_cnt FROM #__membership_plans WHERE plan_code='" . $plan_code . "'";
            $result = $objDB->setQuery($sql);
            if ($objDB->num_rows($result)) {
                $row = $objDB->loadObject();
                $objDB->update_data("#__membership_plans", "plan_code", array('mem_num_cnt' => ((int) $row->mem_num_cnt + 1)), $plan_code);
                return ($row->mem_num_cnt + 1);
            }
        }
    }

    private function generate_membership_number($data) { // Generate Membership Code 
        global $objDB;
        if (is_object($data)) {
            $contCode = '027-';
            if ($data->unit != '') {
                $stateCode = trim($data->unit) . '-';
            } else {
                $stateCode = trim($data->state) . '-';
            }
            $plan_code = trim($data->plan_code);
            $serialNo = $this->getMembershipCodeSerialNo($plan_code);
            //return $contCode . $stateCode . $plan_code . "-TEMP-" . $serialNo;
            //return $contCode . $stateCode . $plan_code . "-" . $serialNo; //modified on 19-November-2015
            return $contCode . $stateCode . $plan_code . $serialNo;
        }
    }

    public function approve_member($member_plan_id, $response) {
        global $objDB, $MEMBERSHIP_CONTROLLER, $objMasters;
        //die(">>>".$member_plan_id);
        $memberData = $this->get_member_data($member_plan_id);
        if ($memberData && $memberData->plan_category != "IYTC"  ) {
            if ($memberData->membership_number == "") {
                $data['status'] = 1;
                $data['udate'] = time();
                $data['membership_number'] = $this->generate_membership_number($memberData);
                $plan_validity = $this->cal_plan_validity($memberData);
                $data['valid_from'] = $plan_validity['from'];
                $data['valid_to'] = $plan_validity['to'];
                $data['TransactionID'] = $response['TransactionID'];
                $data['PaymentID'] = $response['PaymentID'];
                $data['TransactionResponse'] = serialize($response);
                $affected_rows_member_plans = $objDB->update_data("#__member_plans", "member_plan_id", $data, $member_plan_id);
                $affected_rows_member = $objDB->update_data("#__member", "member_id", $data, $memberData->member_id);
                //$member_plan_id = $response['member_plan_id'];

                //call api to save data for offline membership
                $PostData = array_merge((array)$memberData,$data);
                $PostData['city'] = $objMasters->getCityName($PostData['city']);
                $PostData['state'] = $objMasters->getStateName($PostData['state']);
                $PostData['online_membership_card'] = SITE_URL."print-e-card-ind.php?mid=".$memberData->member_plan_id;

                if($PostData!=null && !empty($PostData))
                {
                    $PostData =  json_encode($PostData);
                    $ApiResponse = $MEMBERSHIP_CONTROLLER->Post($PostData);
                }

                //die(">>>".$affected_rows_member_plans);
                if ($affected_rows_member_plans) {
                    $this->setMembershipCommission($memberData, true);
                    $this->send_membership_email($member_plan_id);
                    $this->send_membership_sms($member_plan_id);
                }
            }
        } else {
            $data['TransactionID'] = $response['TransactionID'];
            $data['PaymentID'] = $response['PaymentID'];
            $data['TransactionResponse'] = serialize($response);
            $data['udate'] = time();

            if ($memberData->iytc_confirmation == 1) {
                $data['status'] = 1;
                $data['membership_number'] = $this->yhai_iytc_membership_number($memberData);
                $plan_validity = $this->cal_plan_validity($memberData);
                $data['valid_from'] = $plan_validity['from'];
                $data['valid_to'] = $plan_validity['to'];

                $data['iytc_membership_number'] = $this->get_iytc_membership_number();
                $lastDayOfNextMonth = date("t-m-Y", strtotime("next month"));
                $data['iytc_valid_to'] = strtotime($lastDayOfNextMonth) + (365 * 24 * 60 * 60);
            }
            $affected_rows_member = $objDB->update_data("#__member", "member_id", $data, $memberData->member_id);
            $affected_rows_member_plans = $objDB->update_data("#__member_plans", "member_plan_id", $data, $member_plan_id);

            $this->update_iytc_membership_code($data['iytc_membership_number']);

            //call api to save data for offline membership
            $PostData = array_merge((array)$memberData,$data);
            $PostData['city'] = $objMasters->getCityName($PostData['city']);
            $PostData['state'] = $objMasters->getStateName($PostData['state']);
            $PostData['online_membership_card'] = SITE_URL."print-e-card-ind.php?mid=".$memberData->member_plan_id;
            if($PostData!=null && !empty($PostData))
            {
                $PostData =  json_encode($PostData);
                $ApiResponse = $MEMBERSHIP_CONTROLLER->Post($PostData); 
                  
            }
            

            if ($affected_rows_member_plans) {
                $this->setMembershipCommission($memberData, true);
                $this->send_membership_email($member_plan_id);
                $this->send_email_to_admin($member_plan_id);
            }
        }
        return $affected_rows_member_plans;
    }

    public function UpdateMemberApiData($data)
    {
        global $objDB, $MEMBERSHIP_CONTROLLER, $objMasters;
        //call api to save data for offline membership
        $data['city'] = $objMasters->getCityName($data['city']);
        $data['state'] = $objMasters->getStateName($data['state']);
        $data['online_membership_card'] = SITE_URL."print-e-card-ind.php?mid=".$memberData->member_plan_id;
        $ApiResponse = null;
        if($data!=null && !empty($data))
        {
            $data =  json_encode($data);
            $ApiResponse = $MEMBERSHIP_CONTROLLER->Put($data);
        }
        return $ApiResponse;
    }

    public function setMembershipCommission($data, $isOnline = true) {  //Calculate Commission on Membership
        global $objDB, $objMasters;
        $national_amount = 0;
        $state_amount = 0;
        $unit_amount = 0;
        $LYH_amount = 0;
        $GYH_amount = 0;
        if ($isOnline == true) {
            if ($data->unit != '') {
                $unitInfo = $objMasters->getUnitInfoByCode($data->unit);
                $stateInfo = $objMasters->getStateInfo($data->state);
                $source = 'WU';
                $unit_amount = $state_amount = (($data->plan_amount * 25) / 100); // 25 % of Membership Fee                
                $national_amount = (($data->plan_amount * 50) / 100); // 50 % of Membership Fee
            } else {
                $source = 'W';
                $national_amount = $state_amount = (($data->plan_amount * 50) / 100); // 50 % of Membership Fee
            }
            $dataArr['unit_amount'] = $unit_amount;
            $dataArr['state_amount'] = $state_amount;
            $dataArr['national_amount'] = $national_amount;
            $dataArr['source'] = $source;
            $dataArr['member_plan_id'] = $data->member_plan_id;
            $dataArr['comm_amount_on'] = $data->plan_amount;
            $dataArr['last_comm_to'] = $unitInfo->unit_id;
            $dataArr['country'] = $data->country;
            $dataArr['state'] = $data->state;
            $dataArr['is_adhoc_state'] = ($stateInfo->type) ? 'Y' : 'N';
            $dataArr['city'] = $data->city;
            $dataArr['unit'] = $data->unit;
            $dataArr['is_adhoc_unit'] = ($unitInfo->type) ? 'Y' : 'N';
            $dataArr['cdate'] = time();
            $comm_id = $objDB->insert_data("#__commission", $dataArr);
        } elseif ($isOnline == false) {
            
        }
    }

    public function get_member_data($member_plan_id) {
        global $objDB;
        $sql = "SELECT b.*,a.email,a.password,a.status as member_status FROM #__member_plans b
        LEFT JOIN #__member a ON b.member_id=a.member_id        
        WHERE b.member_plan_id='$member_plan_id'";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObject($result);
        } else {
            return false;
        }
    }

    public function cal_plan_validity($data) {
        $validity = array();
        $from = time();
        switch ($data->plan_code) {
            case 'J1':
            case 'S1':
            case 'CJ1':
            case 'CS1':
            case 'I1':
            case 'INS_ABV_12_1':
                //$to = mktime(0, 0, 0, date('m'), date('d'), date('Y') + 1);// ingen
                if (date('m') > 9)
                    $to = mktime(0, 0, 0, 12, 31, date('Y') + 1);
                else
                    $to = mktime(0, 0, 0, 12, 31, date('Y'));
                break;
            case 'I2':
            case 'S2':
                //$to = mktime(0, 0, 0, date('m'), date('d'), date('Y') + 2); // ingen
                if (date('m') > 9)
                    $to = mktime(0, 0, 0, 12, 31, date('Y') + 2);
                else
                    $to = mktime(0, 0, 0, 12, 31, date('Y') + 1);
                break;
            case 'L0':
                //$to = mktime(0, 0, 0, 12, 31, date("Y") + 99); 
                $to = mktime(0, 0, 0, 12, 31, 2037);
                break;
            case 'I5':
            case 'INS_ABV_12_5':
                //$to = mktime(0, 0, 0, date('m'), date('d'), date('Y') + 5); // ingen
                if (date('m') > 9)
                    $to = mktime(0, 0, 0, 12, 31, date('Y') + 5);
                else
                    $to = mktime(0, 0, 0, 12, 31, date('Y') + 4);
                break;
            case 'IYTC':
                $to = strtotime("365 days");
                break;
            
        }
        $validity['from'] = $from;
        $validity['to'] = $to;
        return $validity;
    }

    public function send_membership_email($member_plan_id) {
        global $objDB, $objBase, $mail, $objEncryption;
        $memberData = $this->get_member_data($member_plan_id);
        if ($memberData->email != "") {
            $email_to = $memberData->email;
            $subject = 'Welcome to Youth Hostels Association of India';
            $message = $this->get_membership_email_message($memberData);
             $cc = GLOBAL_CC;
         $objBase->_send_mail($email_to, $subject, $message, $cc," ");
        }
    }

    public function send_membership_sms($member_plan_id ,$status="") {
        global $objDB, $objBase,$objMasters, $objMessageService, $objEncryption;
        if($member_plan_id){
        $memberData = $this->get_member_data($member_plan_id);
       

       // SMS API Script Start
     
        
        if($memberData->mobile !=""){

                $mobile = $memberData->mobile;
             
                $memberCategoryType = $memberData->plan_category; 
               $message = $objMessageService->getMessageFor($memberData->plan_category,null);
              $arrMessage = array(
             "{MEMBERSHIP_NUMBER}" => $memberData->membership_number,
             "{NAME}" => $memberData->fname,
             );
          $message = @str_replace(array_keys($arrMessage), array_values($arrMessage), $message);
          $objMessageService->sendMessage($message,$mobile);
        }
    

        
    
    }
        // SMS API Script End
        return true;
 }


    public function member_login($data) {
        global $objDB, $objEncryption;
        if (!$data)
            return false;
        $DOB = ($data['DOB']) ? date("m/d/Y", strtotime($data['DOB'])) : $data['DOB'];
        $sql = "SELECT b.*,a.email,a.password,a.status as member_status from #__member a 
        JOIN #__member_plans b ON b.member_id=a.member_id        
        WHERE 1 AND ((a.email='" . $data['EmailID'] . "' AND a.password='" . $objEncryption->encode($data['PassWord']) . "') OR (b.membership_number='" . $data['MemberID'] . "' AND b.dob='" . $DOB . "')) AND a.status=1 and b.type IN('a','e') ";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObject($result);
        } else {
            return false;
        }
    }

    private function get_membership_email_message($memberData) {
        global $objEncryption, $objMasters;
        $message = '';
        if ($memberData->plan_code == "CJ1" || $memberData->plan_code == "CS1") {
            $message = '
                Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />
                Greetings from the Youth Hostels Association of India.<br /><br />

                This is to acknowledge that we have received your application regarding the YHAI & IYTC Co Branded Membership.<br /><br />

                Please note your Transaction Details:<br /><br />
                <b>
                Payment ID: ' . $memberData->PaymentID . '<br />
                Merchant Reference Number: ' . $memberData->member_plan_id . '<br />

                </b>
                <br />
                <a href="' . SITE_URL . 'print-e-card-ind.php?mid=' . $memberData->member_plan_id . '" target="_blank">Click here to Print your Membership e-Card.</a>
                <br />
                <br /><br />
                You will receive the confirmation regarding the Acceptance/Rejection of your membership application within 2 working days subject to valid age/identity documents upload. For successfully accepted applications, the membership card will be dispatched within 15 working days.
                <br /><br />
                Your YHAI-IYTC Co Branded membership card is an international card.
                <br /><br />
                YHAI membership is valid in more than 90 countries with an access to more than 4000 hostels worldwide. You may visit http://www.yhaindia.org/ to participate in adventure and trekking programs, hostel bookings and much more.			<br /><br />
                IYTC membership is valid in 124 countries which offers you 40000 plus unique discounts at more than 126000 locations worldwide. To know more and get started now, visit http://www.yhaindia.org/. You may also join us on Facebook at http://www.facebook.com/youthhostelassociationofindia to be a part of various discussions, travel tips & latest updates. For further assistance you may write us to contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day.
                <br /><br />
                Regards<br />
                Team YHA India
        ';
        } else if (strstr($memberData->plan_code, "J1") or strstr($memberData->plan_code, "S1") or strstr($memberData->plan_code, "S2")) {
            $message = '
            Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />

            Welcome to Youth Hostels Association of India.<br /><br />

            Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>' . date('d F Y', $memberData->valid_to) . '</strong>. <br /><br />

            Please note your address details registered with us:<br /><br />
            
            ' . $memberData->fname . ' ' . $memberData->lname . '<br />
            ' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
            ' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
            ' . $objMasters->getStateName($memberData->state) . '<br />
            ' . $memberData->phone . ', ' . $memberData->mobile . '<br /><br />
			
            <br />
            <a href="' . SITE_URL . 'print-e-card-ind.php?mid=' . $memberData->member_plan_id . '" target="_blank">Click here to Print your Membership e-Card.</a>
            <br />   
              Please send the photo by e-mail with your temporary membership card details to <a href="mailto:priyank@yhaindia.org" target="_blank"><strong>priyank@yhaindia.org</strong></a> if not uploaded at the time of membership registration.&nbsp;<br />
              If any membership card lost or stolen there is  Duplicate card fee as under</p>
            <ul type="disc">
              <li>PVC Card (for Life membership) : Rs.100 (including postage charges)</li>              
            </ul>
            <br /><br />
            Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

            To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

            
            You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br />
			<br />
            For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br />
            <br /><br /><br />
            Happy Hostelling,<br />
            Team YHA India
            <br /><br /><br />
            <small><strong>Disclaimer</strong><br />
            This is an auto-generated communication. The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, retransmission, dissemination or other use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender and delete the material from any computer.<br />
            <br />
            By joining YHA India, you are indicating that you have read, understood, and agree to YHA India\'s User Agreement, Terms & Conditions, and Privacy Policies <br />
            </small>            
           ';
        } else if (strstr($memberData->plan_code, "I1") or strstr($memberData->plan_code, "I5") or strstr($memberData->plan_code, "INS_ABV_12_1") or strstr($memberData->plan_code, "INS_ABV_12_5")) {
            $message = '
            Dear <strong>' . $memberData->email . '</strong>,<br /><br />

            Welcome to Youth Hostels Association of India.<br /><br />

            Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>' . date('d F Y', $memberData->valid_to) . '</strong>. <br /><br />

            Please note your address details registered with us:<br /><br />
            
            ' . $memberData->organisation . '<br />
            ' . $memberData->organisation_head . '<br />
            ' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
            ' . $objMasters->getCityName($memberData->city) . '–' . $memberData->postal_code . '<br />
            ' . $objMasters->getStateName($memberData->state) . '<br />
            ' . $memberData->phone . ', ' . $memberData->mobile . '<br /><br />			
			
            <br />
            <a href="' . SITE_URL . 'print-e-card-ins.php?mid=' . $memberData->member_plan_id . '" target="_blank">Click here to Print your Membership e-Card.</a>
            <br />
            Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

            To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

            
            You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br /><br />
	    <br />
            For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br /><br />
            <br /><br /><br /><br />
            Happy Hostelling,<br />
            Team YHA India            
            <br /><br /><br /><br />
            <small><strong>Disclaimer</strong><br />
            This is an auto-generated communication. The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, retransmission, dissemination or other use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender and delete the material from any computer.<br />
            <br />
            By joining YHA India, you are indicating that you have read, understood, and agree to YHA India\'s User Agreement, Terms & Conditions, and Privacy Policies <br />
            </small>     
           ';
        } else {
            if (strstr($memberData->plan_code, "L0")) {
                $message = '
				Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />

				Welcome to Youth Hostels Association of India.<br /><br />

				Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>LIFETIME</strong>. <br /><br />

				Your Lifetime membership card will be dispatched on following address within 15 working days:<br /><br />
				
				' . $memberData->fname . ' ' . $memberData->lname . '<br />
				' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
				' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
				' . $objMasters->getStateName($memberData->state) . '<br />
				' . $memberData->phone . ', ' . $memberData->mobile . '<br /><br />
				
				<br />
                                <a href="' . SITE_URL . 'print-e-card-ind.php?mid=' . $memberData->member_plan_id . '" target="_blank">Click here to Print your Membership e-Card.</a>
                                <br />
                                <br />
				  Please send the photo by e-mail with your temporary membership card details to <a href="mailto:priyank@yhaindia.org" target="_blank"><strong>priyank@yhaindia.org</strong></a> if not uploaded at the time of membership registration.&nbsp;<br />
				  If any membership card lost or stolen there is  Duplicate card fee as under</p>
				<ul type="disc">
				  <li>PVC Card (for Life membership) : Rs.100 (including postage charges)</li>				  
				</ul>
				<br /><br />
				Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

				To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

				
				You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br />
				<br />
				For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br />
				<br /><br /><br />
				Happy Hostelling,<br />
				Team YHA India
				<br /><br /><br />
				<small><strong>Disclaimer</strong><br />
				This is an auto-generated communication. The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, retransmission, dissemination or other use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender and delete the material from any computer.<br />
				<br />
				By joining YHA India, you are indicating that you have read, understood, and agree to YHA India\'s User Agreement, Terms & Conditions, and Privacy Policies <br />
				</small>            
			   ';
            } else {
                $message = '
				Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />

				Welcome to Youth Hostels Association of India.<br /><br />

				Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>LIFETIME</strong>. <br /><br />

				Please note your address details registered with us:<br /><br />
				
				' . $memberData->fname . ' ' . $memberData->lname . '<br />
				' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
				' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
				' . $objMasters->getStateName($memberData->state) . '<br />
				' . $memberData->phone . ', ' . $memberData->mobile . '<br /><br />
				<br />
                                <a href="' . SITE_URL . 'print-e-card-ind.php?mid=' . $memberData->member_plan_id . '" target="_blank">Click here to Print your Membership e-Card.</a>
                                <br />
				<br />
				  Please send the photo by e-mail with your temporary membership card details to <a href="mailto:priyank@yhaindia.org" target="_blank"><strong>priyank@yhaindia.org</strong></a> if not uploaded at the time of membership registration.&nbsp;<br />
				  If any membership card lost or stolen there is  Duplicate card fee as under</p>
				<ul type="disc">
				  <li>PVC Card (for Life membership) : Rs.100 (including postage charges)</li>				  
				</ul>
				<br /><br />
				Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

				To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

				
				You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br />
				<br />
				For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br />
				<br /><br /><br />
				Happy Hostelling,<br />
				Team YHA India
				<br /><br /><br />
				<small><strong>Disclaimer</strong><br />
				This is an auto-generated communication. The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, retransmission, dissemination or other use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender and delete the material from any computer.<br />
				<br />
				By joining YHA India, you are indicating that you have read, understood, and agree to YHA India\'s User Agreement, Terms & Conditions, and Privacy Policies <br />
				</small>            
			   ';
            }
        }

        return $message;
    }

    public function get_membership_web_message($member_plan_id, $print_card_links = '') {
        global $objEncryption, $objMasters;
        $memberData = $this->get_member_data($member_plan_id);
        //echo "====".$memberData->plan_code."====";

        $message = '';
        if ($memberData->plan_code == "CJ1" || $memberData->plan_code == "CS1" ) {
            $message = '
            Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />

            We have received your application regarding the YHAI & IYTC Co Branded Membership. You will receive the confirmation regarding the Acceptance/Rejection of your membership application within 2 working days via E Mail, subject to valid age/identity documents upload. For successfully accepted applications, the membership card will be dispatched within 15 working days.<br /><br />For more information, you can write a mail to us on contact@yhaindia.org, or call us at 011 45999000<br /><br />
            <b>Payment ID:</b> ' . $memberData->PaymentID . '<br />
            <b>Transaction No.:</b> ' . $memberData->TransactionID . '
            <br /><br />
            ';
        } else if (strstr($memberData->plan_code, "J1") or strstr($memberData->plan_code, "S1") or strstr($memberData->plan_code, "S2")) {
            $message = '
            Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />

            Welcome to Youth Hostels Association of India.<br /><br />

            Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>' . date('d F Y', $memberData->valid_to) . '</strong>. <br /><br />

            Please note your address details registered with us:<br /><br />
            
            ' . $memberData->fname . ' ' . $memberData->lname . '<br />
            ' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
            ' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
            ' . $objMasters->getStateName($memberData->state) . '<br />
            ' ;
            if($memberData->phone!="" && $memberData->mobile!=""){
            $message .=''.$memberData->phone . ', ' . $memberData->mobile . '<br /><br />';}
            else{
                $message .='' . $memberData->mobile . '<br /><br />';
            }
			
           $message .= ' <br />
            <br />
                <a href="' . SITE_URL . 'print-e-card-ind.php?mid=' . $memberData->member_plan_id . '" target="_blank">Click here to Print your Membership e-Card.</a>
                <br />
              Please send the photo by e-mail with your temporary membership card details to <a href="mailto:priyank@yhaindia.org" target="_blank"><strong>priyank@yhaindia.org</strong></a> if not uploaded at the time of membership registration.&nbsp;<br />
              If any membership card lost or stolen there is  Duplicate card fee as under</p>
            <ul type="disc">
              <li>PVC Card (for Life membership) : Rs.100 (including postage charges)</li>              
            </ul>
            <br /><br />';
            if ($print_card_links) {
                //$message .= $print_card_links;
            }

            $message .='<br /><br />Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

            To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

            
            You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br />
			<br />
            For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br />
            <br /><br /><br />
            Happy Hostelling,<br />
            Team YHA India            
           ';
        } else if (strstr($memberData->plan_code, "I1") or strstr($memberData->plan_code, "I5") or strstr($memberData->plan_code, "INS_ABV_12_1") or strstr($memberData->plan_code, "INS_ABV_12_5")) {
            $message = '
            Dear <strong>' . $memberData->email . '</strong>,<br /><br />

            Welcome to Youth Hostels Association of India.<br /><br />

            Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>' . date('d F Y', $memberData->valid_to) . '</strong>. <br /><br />

            Please note your address details registered with us:<br /><br />
            
            ' . $memberData->organisation . '<br />
            ' . $memberData->organisation_head . '<br />
            ' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
            ' . $objMasters->getCityName($memberData->city) . '–' . $memberData->postal_code . '<br />
            ' . $objMasters->getStateName($memberData->state) . '<br />
            ';
            if($memberData->phone!="" && $memberData->mobile!=""){
            $message .=''.$memberData->phone . ', ' . $memberData->mobile . '<br /><br />';}
            else{
                $message .='' . $memberData->mobile . '<br /><br />';
            }

            if ($print_card_links) {
                //$message .= $print_card_links;
            }

            $message .='<br />
             <br />
            <a href="' . SITE_URL . 'print-e-card-ins.php?mid=' . $memberData->member_plan_id . '" target="_blank">Click here to Print your Membership e-Card.</a>
            <br />   
            <br />
            Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

            To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

            
            You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br /><br />
			<br />
            For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br /><br />
            <br /><br /><br /><br />
            Happy Hostelling,<br />
            Team YHA India             
           ';
        } else {
            if (strstr($memberData->plan_code, "L0")) {
                $message = '
				Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />

				Welcome to Youth Hostels Association of India.<br /><br />

				Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>LIFETIME</strong>. <br /><br />

				Your Lifetime membership card will be dispatched on following address within 15 working days:<br /><br />
				
				' . $memberData->fname . ' ' . $memberData->lname . '<br />
				' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
				' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
				' . $objMasters->getStateName($memberData->state) . '<br />
				' ;
            if($memberData->phone!="" && $memberData->mobile!=""){
            $message .=''.$memberData->phone . ', ' . $memberData->mobile . '<br /><br />';}
            else{
                $message .='' . $memberData->mobile . '<br /><br />';
            }		
				'<br />
                                <br />
                                <a href="' . SITE_URL . 'print-e-card-ins.php?mid=' . $memberData->member_plan_id . '" target="_blank">Click here to Print your Membership e-Card.</a>
                                <br /> 
				  Please send the photo by e-mail with your temporary membership card details to <a href="mailto:priyank@yhaindia.org" target="_blank"><strong>priyank@yhaindia.org</strong></a> if not uploaded at the time of membership registration.&nbsp;<br />
				  If any membership card lost or stolen there is  Duplicate card fee as under</p>
				<ul type="disc">
				  <li>PVC Card (for Life membership) : Rs.100 (including postage charges)</li>              
				</ul>
				<br /><br />';
                if ($print_card_links) {
                    //$message .= $print_card_links;
                }

                $message .='<br /><br />Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

				To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

				
				You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br />
				<br />
				For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br />
				<br /><br /><br />
				Happy Hostelling,<br />
				Team YHA India            
			   ';
            } else {
                $message = '
				Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />

				Welcome to Youth Hostels Association of India.<br /><br />

				Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>LIFETIME</strong>. <br /><br />

				Please note your address details registered with us:<br /><br />
				
				' . $memberData->fname . ' ' . $memberData->lname . '<br />
				' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
				' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
				' . $objMasters->getStateName($memberData->state) . '<br />
				';
            if($memberData->phone!="" && $memberData->mobile!=""){
            $message .=''.$memberData->phone . ', ' . $memberData->mobile . '<br /><br />';}
            else{
                $message .='' . $memberData->mobile . '<br /><br />';
            }		
				'<br />
				  Please send the photo by e-mail with your temporary membership card details to <a href="mailto:priyank@yhaindia.org" target="_blank"><strong>priyank@yhaindia.org</strong></a> if not uploaded at the time of membership registration.&nbsp;<br />
				  If any membership card lost or stolen there is  Duplicate card fee as under</p>
				<ul type="disc">
				  <li>PVC Card (for Life membership) : Rs.100 (including postage charges)</li>              
				</ul>
				<br /><br />';
                if ($print_card_links) {
                    //$message .= $print_card_links;
                }

                $message .='<br />'
                        . '<br />
                                <a href="' . SITE_URL . 'print-e-card-ins.php?mid=' . $memberData->member_plan_id . '" target="_blank">Click here to Print your Membership e-Card.</a>
                                <br /> 
                                <br />Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

				To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

				
				You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br />
				<br />
				For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br />
				<br /><br /><br />
				Happy Hostelling,<br />
				Team YHA India            
			   ';
            }
        }
        //die($message);
        return $message;
    }

    public function check_member_exists($member_email) {
        global $objDB;
        $sql = "SELECT member_id from #__member WHERE email='" . $member_email . "' ";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObject();
        } else {
            return false;
        }
    }

    public function verify_member_password($password) {
        global $objDB, $objEncryption;
        $password = $objEncryption->encode($password);
        $sql = "SELECT 1 from #__member WHERE password='" . $password . "' ";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return true;
        } else {
            return false;
        }
    }

    public function verify_payment($MerchantRefNo, $Amount) {
        global $objDB;
        $membership_prices = null;
        $sql = "SELECT member_plan_id FROM #__member_plans a WHERE 1 AND a.member_plan_id='$MerchantRefNo' AND a.total_amount='$Amount' ";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        //die(">>>".$objDB->num_rows($result));
        $arr = array();
        if ($objDB->num_rows($result)) {
            return true;
        } else {
            return false;
        }
    }

    public function calculate_age($birthDate) {
        //date in mm/dd/yyyy format; or it can be in other formats as well        
        //explode the date to get month, day and year
        $birthDate = date("m/d/Y", strtotime($birthDate));
        $birthDate = explode("/", $birthDate);
        //get age from date or birthdate
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y") - $birthDate[2]) - 1) : (date("Y") - $birthDate[2]));
        return $age;
    }

    public function CheckLogin($type = 0, $RedirectPage = 'member-login.php') {
        global $objBase;
        if (!isset($_SESSION['MemberData']['login'])) {
            $objBase->Redirect($RedirectPage);
            exit;
        }
    }

    public function list_member_plans($member_email) {
        global $objDB;
        $sql = "SELECT b.*,a.email,a.password,a.status as member_status from #__member a 
        JOIN #__member_plans b ON b.member_id=a.member_id        
        WHERE a.email='$member_email' AND b.TransactionID!='' ORDER BY cdate DESC";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObjectList($result);
        } else {
            return false;
        }
    }

    public function print_membership_card($member_plan_id) {
        global $objDB;
        $str = '';
        $memberData = $this->get_member_data($member_plan_id);
        if ($memberData) {
            if (strstr($memberData->plan_code, "L0")) {
                $str .='<a href="' . SITE_URL . 'print-membership-card.php?member_plan_id=' . $member_plan_id . '" class="colorbox_cards"><strong><u>Print Membership Card</u></strong></a>';
            } else if (strstr($memberData->plan_code, "I1") or strstr($memberData->plan_code, "I5") or strstr($memberData->plan_code, "INS_ABV_12_1") or strstr($memberData->plan_code, "INS_ABV_12_5")) {
                $str .='<a href="' . SITE_URL . 'print-membership-card-ins.php?member_plan_id=' . $member_plan_id . '" class="colorbox_ind_cards"><strong><u>Print Membership Card</u></strong></a>';
            } else {
                $str .='<a href="' . SITE_URL . 'print-membership-card1.php?member_plan_id=' . $member_plan_id . '" class="colorbox_ind_cards"><strong><u>Print Membership Card</u></strong></a>';
            }
            return $str;
        } else {
            return false;
        }
        return $str;
    }

    public function print_membership_card_admin($member_plan_id) {
        global $objDB;
        $str = '';
        $memberData = $this->get_member_data($member_plan_id);
        if ($memberData) {
            if (strstr($memberData->plan_code, "L0")) {
                $str .='<a href="' . SITE_URL . 'print-membership-card.php?member_plan_id=' . $member_plan_id . '" class="colorbox_cards modal" rel="{handler: \'iframe\', size: {x: 460, y: 400}}"><strong><u>Print Membership Card</u></strong></a>';
            }else if (strstr($memberData->plan_code, "L0") && $row->valid_from >= strtotime('2015/07/01')) {
                $str .='<a href="' . SITE_URL . 'print-pvc-card.php?member_plan_id=' . $member_plan_id . '" class="colorbox_cards modal" rel="{handler: \'iframe\', size: {x: 460, y: 400}}"><strong><u>Print PVC Card</u></strong></a>';
            } else if (strstr($memberData->plan_code, "I1") or strstr($memberData->plan_code, "I5") or strstr($memberData->plan_code, "INS_ABV_12_1") or strstr($memberData->plan_code, "INS_ABV_12_5")) {
                $str .='<a href="' . SITE_URL . 'print-membership-card-ins.php?member_plan_id=' . $member_plan_id . '" class="colorbox_ind_cards modal" rel="{handler: \'iframe\', size: {x: 460, y: 400}}"><strong><u>Print Membership Card</u></strong></a>';
            } else if (strstr($memberData->plan_code, "CS1") || strstr($memberData->plan_code, "CJ1") ) {
                $str .='<a href="' . SITE_URL . 'print-membership-card-iytc.php?member_plan_id=' . $member_plan_id . '" class="colorbox_cards modal" rel="{handler: \'iframe\', size: {x: 950, y: 350}}"><strong><u>Print Membership Card</u></strong></a>';
            } else {
                $str .='<a href="' . SITE_URL . 'print-membership-card1.php?member_plan_id=' . $member_plan_id . '" class="colorbox_ind_cards modal"  rel="{handler: \'iframe\', size: {x: 460, y: 400}}"><strong><u>Print Membership Card</u></strong></a>';
            }
            return $str;
        } else {
            return false;
        }
        return $str;
    }

    public function changePasswordAlert2Member($member_id) {
        global $objDB, $objEncryption, $objBase;
        $message = '';
        $memberData = $this->getInfo($member_id);
        if ($memberData && $member_id && $memberData->email && $memberData->password) {
            $email_to = $memberData->email;
            $subject = 'Forgot password retrieve alert for YHAI';
            $message.='
               <table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="100%">
                <tbody>
                    <tr>
                        <td bgcolor="#ffffff" valign="TOP">
                            <p>Dear ' . $memberData->fname . ' ' . $memberData->lname . ',</p>
                            <p>You have successfully retrieved your password for <a href="' . SITE_URL . '"><u>www.yhaindia.org</u></a>                            
                            <br /><br /> Your password is &ndash; <strong>' . $objEncryption->decode($memberData->password) . '</strong>
                            </p>
                            <br /><br />
                        </td>
                    </tr>
                    <tr>
                        <td bgcolor="#ffffff" valign="TOP">
                            <p>
                                Visit <a href="' . SITE_URL . '"> <u>www.yhaindia.org</u></a>  to participate in adventure and trekking programs, hostel bookings and many more. <br /><br />To know more and get started now, log on to <a href="' . SITE_URL . '"><u>www.yhaindia.org</u></a><br />
                                <br />
                                <br />
                                You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia"><u>http://www.facebook.com/youthhostelassociationofindia</u></a>  to be a part of various discussions, travel tips and much more<br />
                                <br />
                                For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day<br />
                                <br /><br />
                                <strong>Happy Hostelling,<br />
                                Team YHA India</strong>
                             </p>
                             <p>                                
                                <b>Disclaimer</b><br />
                                <small>This is a business communication. The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, retransmission, dissemination or other use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender and delete the material from any computer.</small>
                             </p>
                        </td>
                    </tr>
                </tbody>
            </table>';
            //die("                       $email_to, $subject, $message");
            $objBase->_send_mail($email_to, $subject, $message);
        }
        return false;
    }

    public function check_booking_exists($booking_id, $membership_number, $dob) {
        global $objDB;
        if ($booking_id && $membership_number && $dob) {
            $sql = "
            SELECT a.*,b.adp_name,b.adp_from_date FROM #__adventure_program_booking a 
            JOIN #__adventure_programs b ON b.adp_id=a.abk_prog_id
            WHERE a.abk_status='A' 
            AND a.abk_id='$booking_id' 
            AND a.abk_mem_code='$membership_number'
            AND a.abk_dob='" . strtotime($dob) . "'
            ";
            $result = $objDB->setQuery($sql);
            //echo $objDB->getQuery();          
            if ($objDB->num_rows($result)) {
                return $objDB->loadObject();
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function cancel_adv_program($res, $booking_id) {
        global $objDB;
        if ($res && $booking_id) {
            if ($res['response'] == 'SUCCESS' && $res['refrenceNo']) {
                $sql = "
                UPDATE #__adventure_program_booking  
                SET abk_status='C', abk_refund_refrenceNo='" . $res['refrenceNo'] . "', abk_refund_response='" . serialize($res) . "'
                WHERE abk_status='A' AND abk_id='$booking_id' 
                ";
                $affected_rows = $objDB->query($sql);
                return $affected_rows;
            }
        }
    }

    public function check_refund_reference($refrenceNo) {
        global $objDB;
        if ($refrenceNo) {
            $sql = "
                SELECT * FROM #__adventure_program_booking                  
                WHERE abk_refund_refrenceNo='$refrenceNo' 
                ";
            $result = $objDB->setQuery($sql);
            $row = $objDB->loadObject();
            if ($row) {
                return $row;
            }
        }
    }

    public function loadWorkBookLib() { // Load Workbook Library
        $sheet_name = date('Y-m-d');
        chdir('..');
        chdir('libraries');
        chdir('phpxls');
        require_once 'Writer.php';
        //chdir('..');
        //chdir('com');
        $workbook = new Spreadsheet_Excel_Writer();
        return $workbook;
    }

    public function workSheetHeadingFormat($workbook) { // get Worksheet Heading Format 
        $format_bold = & $workbook->addFormat();
        $format_bold->setBold();
        $format_bold->setColor('white');
        $format_bold->setFontFamily('Arial');
        $format_bold->setSize(11);
        $format_bold->setBorder(1);
        $format_bold->setAlign("center");
        $format_bold->setFgColor("green");
        $format_bold->setTextWrap();
        return $format_bold;
    }

    public function workSheetOddFormat($workbook) { // get Worksheet Odd Format 
        $format_gray = & $workbook->addFormat();
        $format_gray->setFgColor(26);
        $format_gray->setBorder(1);
        $format_gray->setColor('black');
        $format_gray->setFontFamily('Arial');
        $format_gray->setSize(11);
        $format_gray->setTextWrap();
        return $format_gray;
    }

    public function workSheetEvenFormat($workbook) { // get Worksheet Event Format 
        $format_white = & $workbook->addFormat();
        $format_white->setFgColor(42);
        $format_white->setColor('black');
        $format_white->setBorder(1);
        $format_white->setFontFamily('Arial');
        $format_white->setSize(11);
        $format_white->setTextWrap();
        return $format_white;
    }

    public function export_members() {
        global $objDB, $objBase, $objMasters;
        /* $sql = "SELECT m.email, mp.*,
          (SELECT username   FROM  #__admin WHERE admin_id = last_updated_by ) as admin_username
          FROM #__member m
          INNER JOIN #__member_plans mp ON m.member_id = mp.member_id
          WHERE 1 AND (TransactionID > 0) AND type='a' " . $_SESSION['filter'][$_SERVER[SCRIPT_FILENAME]]['sql_whr'] . " ORDER BY cdate DESC ";
         */
        $sql = "SELECT *,source.*,(SELECT username   FROM  #__admin WHERE admin_id = last_updated_by ) as admin_username  FROM #__member m 
				INNER JOIN #__member_plans mp ON m.member_id = mp.member_id
				LEFT JOIN #__offline_source source ON mp.TransactionID=source.source_transaction_id
				WHERE 1 AND (TransactionID != '') AND (TransactionID != 'NULL') " . $_SESSION['filter'][$_SERVER[SCRIPT_FILENAME]]['sql_whr'] . " ORDER BY mp.member_plan_id DESC";

        $result = $objDB->setQuery($sql);
        $rows = $objDB->loadObjectList($result);

        $file_name_xls = "Members_" . date("d-m-Y");
        $sheet_name = date('Y-m-d');

        $workbook = $this->loadWorkBookLib();

        $format_bold = $this->workSheetHeadingFormat($workbook);
        $format_gray = $this->workSheetOddFormat($workbook);
        $format_white = $this->workSheetEvenFormat($workbook);

        $worksheet = & $workbook->addWorksheet($sheet_name);
        $worksheet->write(0, 0, "Marchant Ref. No.", $format_bold);
        $worksheet->write(0, 1, "Membership Code", $format_bold);
        $worksheet->write(0, 2, "Plan Code", $format_bold);
        $worksheet->write(0, 3, "Plan Category", $format_bold);
        $worksheet->write(0, 4, "Name", $format_bold);
        $worksheet->write(0, 5, "Email", $format_bold);
        $worksheet->write(0, 6, "Gender", $format_bold);
        $worksheet->write(0, 7, "DOB", $format_bold);
        $worksheet->write(0, 8, "Address", $format_bold);
        $worksheet->write(0, 9, "City", $format_bold);
        $worksheet->write(0, 10, "Pincode", $format_bold);
        $worksheet->write(0, 11, "State", $format_bold);
        $worksheet->write(0, 12, "Phone", $format_bold);
        $worksheet->write(0, 13, "Mobile", $format_bold);
        $worksheet->write(0, 14, "Unit", $format_bold);
        $worksheet->write(0, 15, "Validity", $format_bold);
        $worksheet->write(0, 16, "Transaction Id", $format_bold);
        $worksheet->write(0, 17, "Payment Id", $format_bold);
        $worksheet->write(0, 18, "Amount Paid", $format_bold);
        $worksheet->write(0, 19, "Fee Breakup", $format_bold);
        $worksheet->write(0, 20, "Registration Date", $format_bold);
        $worksheet->write(0, 21, "Photographs", $format_bold);
        $worksheet->write(0, 22, "Age/ID Proof Type", $format_bold);
        $worksheet->write(0, 23, "Document Name", $format_bold);
        $worksheet->write(0, 24, "IYTC Membership Code", $format_bold);
        $worksheet->write(0, 25, "IYTC Validity", $format_bold);
        $worksheet->write(0, 26, "Last Updated By", $format_bold);
        $worksheet->write(0, 27, "Last Updated On", $format_bold);

        $i = 1;
        $cnt = $i;

        if ($rows) {
            foreach ($rows as $row) {
                if ($row->photograph) {
                    $image = UPLOAD_PATH . "/members/" . $row->photograph;
                } else {
                    $image = "";
                }
                if ($row->plan_code == 'L0'):
                    $validity = "LIFETIME";
                else:
                    $validity = $objBase->FormatDate($row->valid_from, "d M Y") . "-" . $objBase->FormatDate($row->valid_to, "d M Y");
                endif;
                $plan_breakup_str = "Plan Amount:" . $row->plan_amount . "";
                if ($row->plan_breakup) {
                    $plan_breakup = unserialize($row->plan_breakup);
                    if ($plan_breakup) {
                        foreach ($plan_breakup as $plan_breakup_key => $plan_breakup_val) {
                            $plan_breakup_str .=$plan_breakup_key . ":" . $plan_breakup_val . "";
                        }
                    }
                }
                $format = ($i % 2 == 0) ? $format_gray : $format_white;
                $last_updated_date = ($row->last_updated_on) ? date("Y-m-d h:i a", $row->last_updated_on) : "";

                $worksheet->write($i, 0, $row->member_plan_id, $format);
                $worksheet->write($i, 1, $row->membership_number, $format);
                $worksheet->write($i, 2, $row->plan_code, $format);
                $worksheet->write($i, 3, $row->plan_category, $format);
                $worksheet->write($i, 4, $row->fname . " " . $row->lname, $format);
                $worksheet->write($i, 5, $row->email, $format);
                $worksheet->write($i, 6, $row->gender, $format);
                $worksheet->write($i, 7, $row->dob, $format);
                $worksheet->write($i, 8, $row->address1, $format);
                $worksheet->write($i, 9, $objMasters->getCityName($row->city), $format);
                $worksheet->write($i, 10, $row->postal_code, $format);
                $worksheet->write($i, 11, $objMasters->getStateName($row->state), $format);
                $worksheet->write($i, 12, $row->phone, $format);
                $worksheet->write($i, 13, $row->mobile, $format);
                $worksheet->write($i, 14, $row->unit, $format);
                $worksheet->write($i, 15, $validity, $format);
                $worksheet->write($i, 16, $row->TransactionID, $format);
                $worksheet->write($i, 17, $row->PaymentID, $format);
                $worksheet->write($i, 18, $row->total_amount, $format);
                $worksheet->write($i, 19, $plan_breakup_str, $format);
                $worksheet->write($i, 20, $objBase->FormatDate($row->cdate), $format);
                $worksheet->write($i, 21, $row->photograph, $format);
                $worksheet->write($i, 22, $row->prooftype, $format);
                $worksheet->write($i, 23, $row->proofdoc, $format);
                $worksheet->write($i, 24, $row->iytc_membership_number, $format);
                if ($row->iytc_valid_to > 0) {
                    $worksheet->write($i, 25, $objBase->FormatDate($row->iytc_valid_to, "F Y"), $format);
                } else {
                    $worksheet->write($i, 25, "", $format);
                }
                $worksheet->write($i, 26, $row->admin_username, $format);
                $worksheet->write($i, 27, $last_updated_date, $format);
                /* if($image){
                  $worksheet->insertBitmap ($i,15,$image,50,50,60,60);
                  }else{
                  $worksheet->write($i, 15, "");
                  } */
                $i++;
            }
        }
        $workbook->send($file_name_xls . '.xls');
        $workbook->close();
        die();
    }

    public function export_incomplete_members() {
        global $objDB, $objBase, $objMasters;
        $sql = "SELECT * FROM #__member m INNER JOIN #__member_plans mp ON m.member_id = mp.member_id
            WHERE 1 AND (TransactionID = 0 OR TransactionID = '' OR TransactionID = 'NULL') AND (plan_code!='')  " . $_SESSION['filteri']['sql_whr'] . " ORDER BY mp.cdate DESC";

        $result = $objDB->setQuery($sql);
        // echo $objDB->getQuery();die;
        $rows = $objDB->loadObjectList($result);

        $file_name_xls = "Incomplete_Members_" . date("d-m-Y");
        $sheet_name = date('Y-m-d');
        chdir('..');
        chdir('libraries');
        chdir('phpxls');
        require_once 'Writer.php';
        //chdir('..');
        //chdir('com');
        $workbook = new Spreadsheet_Excel_Writer();

        $format_bold = & $workbook->addFormat();
        $format_bold->setBold();

        $format_red = & $workbook->addFormat();
        //$format_red->setBottom(2);//thick
        $format_red->setBold();
        $format_red->setColor('red');
        $format_red->setFontFamily('Arial');
        $format_red->setSize(8);

        $format_green = & $workbook->addFormat();
        $format_green->setColor('green');
        $format_green->setFontFamily('Arial');
        $format_green->setSize(8);

        $worksheet = & $workbook->addWorksheet($sheet_name);
        $worksheet->write(0, 0, "Marchant Ref. No.", $format_bold);
        $worksheet->write(0, 1, "Membership Code", $format_bold);
        $worksheet->write(0, 2, "Plan Code", $format_bold);
        $worksheet->write(0, 3, "Plan Category", $format_bold);
        $worksheet->write(0, 4, "Name", $format_bold);
        $worksheet->write(0, 5, "Email", $format_bold);
        $worksheet->write(0, 6, "Gender", $format_bold);
        $worksheet->write(0, 7, "DOB", $format_bold);
        $worksheet->write(0, 8, "Address", $format_bold);
        $worksheet->write(0, 9, "City", $format_bold);
        $worksheet->write(0, 10, "Pincode", $format_bold);
        $worksheet->write(0, 11, "State", $format_bold);
        $worksheet->write(0, 12, "Phone", $format_bold);
        $worksheet->write(0, 13, "Mobile", $format_bold);
        $worksheet->write(0, 14, "Unit", $format_bold);
        $worksheet->write(0, 15, "Validity", $format_bold);
        $worksheet->write(0, 16, "Transaction Id", $format_bold);
        $worksheet->write(0, 17, "Payment Id", $format_bold);
        $worksheet->write(0, 18, "Amount Paid", $format_bold);
        $worksheet->write(0, 19, "Fee Breakup", $format_bold);
        $worksheet->write(0, 20, "Registration Date", $format_bold);
        $worksheet->write(0, 21, "Photographs", $format_bold);
        $worksheet->write(0, 22, "Age/ID Proof Type", $format_bold);
        $worksheet->write(0, 23, "Document Name", $format_bold);
        $worksheet->write(0, 24, "IYTC Membership Code", $format_bold);
        $worksheet->write(0, 25, "IYTC Validity", $format_bold);
        $i = 1;
        $cnt = $i;

        if ($rows) {
            foreach ($rows as $row) {
                if ($row->photograph) {
                    $image = UPLOAD_PATH . "/members/" . $row->photograph;
                } else {
                    $image = "";
                }
                if ($row->plan_code == 'L0'):
                    $validity = "LIFETIME";
                else:
                    $validity = $objBase->FormatDate($row->valid_from, "d M Y") . "-" . $objBase->FormatDate($row->valid_to, "d M Y");
                endif;
                $plan_breakup_str = "Plan Amount:" . $row->plan_amount . "
";
                if ($row->plan_breakup) {
                    $plan_breakup = unserialize($row->plan_breakup);
                    foreach ($plan_breakup as $plan_breakup_key => $plan_breakup_val) {
                        $plan_breakup_str .=$plan_breakup_key . ":" . $plan_breakup_val . "
";
                    }
                }
                $worksheet->write($i, 0, $row->member_plan_id);
                $worksheet->write($i, 1, $row->membership_number);
                $worksheet->write($i, 2, $row->plan_code);
                $worksheet->write($i, 3, $row->plan_category);
                $worksheet->write($i, 4, $row->fname . " " . $row->lname);
                $worksheet->write($i, 5, $row->email);
                $worksheet->write($i, 6, $row->gender);
                $worksheet->write($i, 7, $row->dob);
                $worksheet->write($i, 8, $row->address1);
                $worksheet->write($i, 9, $objMasters->getCityName($row->city));
                $worksheet->write($i, 10, $row->postal_code);
                $worksheet->write($i, 11, $objMasters->getStateName($row->state));
                $worksheet->write($i, 12, $row->phone);
                $worksheet->write($i, 13, $row->mobile);
                $worksheet->write($i, 14, $row->unit);
                $worksheet->write($i, 15, $validity);
                $worksheet->write($i, 16, $row->TransactionID);
                $worksheet->write($i, 17, $row->PaymentID);
                $worksheet->write($i, 18, $row->total_amount);
                $worksheet->write($i, 19, $plan_breakup_str);
                $worksheet->write($i, 20, $objBase->FormatDate($row->cdate));
                $worksheet->write($i, 21, $row->photograph);
                $worksheet->write($i, 22, $row->prooftype);
                $worksheet->write($i, 23, $row->proofdoc);
                $worksheet->write($i, 24, $row->iytc_membership_number);
                if ($row->iytc_valid_to > 0) {
                    $worksheet->write($i, 25, $objBase->FormatDate($row->iytc_valid_to, "F Y"));
                } else {
                    $worksheet->write($i, 25, "");
                }
                /* if($image){
                  $worksheet->insertBitmap ($i,15,$image,50,50,60,60);
                  }else{
                  $worksheet->write($i, 15, "");
                  } */
                $i++;
            }
        }
        $workbook->send($file_name_xls . '.xls');
        $workbook->close();
        die();
    }

    public function export_iytc_members() {
        global $objDB, $objBase, $objMasters;
        $sql = "SELECT m.email,mp.* FROM #__member m 
        INNER JOIN #__member_plans mp ON m.member_id = mp.member_id
        WHERE mp.plan_category = 'IYTC' AND (TransactionID > 0) AND type='a' " . $_SESSION['filter'][$_SERVER[SCRIPT_FILENAME]]['sql_whr'] . " ORDER BY cdate DESC ";
        $result = $objDB->setQuery($sql);
        // echo $objDB->getQuery();die;
        $rows = $objDB->loadObjectList($result);

        $file_name_xls = "Members_" . date("d-m-Y");
        $sheet_name = date('Y-m-d');
        chdir('..');
        chdir('libraries');
        chdir('phpxls');
        require_once 'Writer.php';
        //chdir('..');
        //chdir('com');
        $workbook = new Spreadsheet_Excel_Writer();

        $format_bold = & $workbook->addFormat();
        $format_bold->setBold();

        $format_red = & $workbook->addFormat();
        //$format_red->setBottom(2);//thick
        $format_red->setBold();
        $format_red->setColor('red');
        $format_red->setFontFamily('Arial');
        $format_red->setSize(8);

        $format_green = & $workbook->addFormat();
        $format_green->setColor('green');
        $format_green->setFontFamily('Arial');
        $format_green->setSize(8);

        $worksheet = & $workbook->addWorksheet($sheet_name);
        $worksheet->write(0, 0, "Marchant Ref. No.", $format_bold);
        $worksheet->write(0, 1, "Membership Code", $format_bold);
        $worksheet->write(0, 2, "Plan Code", $format_bold);
        $worksheet->write(0, 3, "Plan Category", $format_bold);
        $worksheet->write(0, 4, "Name", $format_bold);
        $worksheet->write(0, 5, "Email", $format_bold);
        $worksheet->write(0, 6, "Gender", $format_bold);
        $worksheet->write(0, 7, "DOB", $format_bold);
        $worksheet->write(0, 8, "Address", $format_bold);
        $worksheet->write(0, 9, "City", $format_bold);
        $worksheet->write(0, 10, "Pincode", $format_bold);
        $worksheet->write(0, 11, "State", $format_bold);
        $worksheet->write(0, 12, "Phone", $format_bold);
        $worksheet->write(0, 13, "Mobile", $format_bold);
        $worksheet->write(0, 14, "Unit", $format_bold);
        $worksheet->write(0, 15, "Validity", $format_bold);
        $worksheet->write(0, 16, "Transaction Id", $format_bold);
        $worksheet->write(0, 17, "Payment Id", $format_bold);
        $worksheet->write(0, 18, "Amount Paid", $format_bold);
        $worksheet->write(0, 19, "Fee Breakup", $format_bold);
        $worksheet->write(0, 20, "Registration Date", $format_bold);
        $worksheet->write(0, 21, "Photographs", $format_bold);
        $worksheet->write(0, 22, "Age/ID Proof Type", $format_bold);
        $worksheet->write(0, 23, "Document Name", $format_bold);
        $worksheet->write(0, 24, "IYTC Membership Code", $format_bold);
        $worksheet->write(0, 25, "IYTC Validity", $format_bold);

        $i = 1;
        $cnt = $i;

        if ($rows) {
            foreach ($rows as $row) {
                if ($row->photograph) {
                    $image = UPLOAD_PATH . "/members/" . $row->photograph;
                } else {
                    $image = "";
                }
                if ($row->plan_code == 'L0'):
                    $validity = "LIFETIME";
                else:
                    $validity = $objBase->FormatDate($row->valid_from, "d M Y") . "-" . $objBase->FormatDate($row->valid_to, "d M Y");
                endif;
                $plan_breakup_str = "Plan Amount:" . $row->plan_amount . "
";
                if ($row->plan_breakup) {
                    $plan_breakup = unserialize($row->plan_breakup);
                    foreach ($plan_breakup as $plan_breakup_key => $plan_breakup_val) {
                        $plan_breakup_str .=$plan_breakup_key . ":" . $plan_breakup_val . "
";
                    }
                }
                $worksheet->write($i, 0, $row->member_plan_id);
                $worksheet->write($i, 1, $row->membership_number);
                $worksheet->write($i, 2, $row->plan_code);
                $worksheet->write($i, 3, $row->plan_category);
                $worksheet->write($i, 4, $row->fname . " " . $row->lname);
                $worksheet->write($i, 5, $row->email);
                $worksheet->write($i, 6, $row->gender);
                $worksheet->write($i, 7, $row->dob);
                $worksheet->write($i, 8, $row->address1);
                $worksheet->write($i, 9, $objMasters->getCityName($row->city));
                $worksheet->write($i, 10, $row->postal_code);
                $worksheet->write($i, 11, $objMasters->getStateName($row->state));
                $worksheet->write($i, 12, $row->phone);
                $worksheet->write($i, 13, $row->mobile);
                $worksheet->write($i, 14, $row->unit);
                $worksheet->write($i, 15, $validity);
                $worksheet->write($i, 16, $row->TransactionID);
                $worksheet->write($i, 17, $row->PaymentID);
                $worksheet->write($i, 18, $row->total_amount);
                $worksheet->write($i, 19, $plan_breakup_str);
                $worksheet->write($i, 20, $objBase->FormatDate($row->cdate));
                $worksheet->write($i, 21, $row->photograph);
                $worksheet->write($i, 22, $row->prooftype);
                $worksheet->write($i, 23, $row->proofdoc);
                $worksheet->write($i, 24, $row->iytc_membership_number);
                if ($row->iytc_valid_to > 0) {
                    $worksheet->write($i, 25, $objBase->FormatDate($row->iytc_valid_to, "F Y"));
                } else {
                    $worksheet->write($i, 25, "");
                }
                /* if($image){
                  $worksheet->insertBitmap ($i,15,$image,50,50,60,60);
                  }else{
                  $worksheet->write($i, 15, "");
                  } */
                $i++;
            }
        }
        $workbook->send($file_name_xls . '.xls');
        $workbook->close();
        die();
    }

    public function export_iytc_incomplete_members() {
        global $objDB, $objBase, $objMasters;
        $sql = "SELECT * FROM #__member m INNER JOIN #__member_plans mp ON m.member_id = mp.member_id
            WHERE mp.member_for = 'iytc' AND (TransactionID = 0 OR TransactionID = '') AND (plan_code!='')  " . $_SESSION['filteri']['sql_whr'] . " ORDER BY mp.cdate DESC";

        $result = $objDB->setQuery($sql);
        // echo $objDB->getQuery();die;
        $rows = $objDB->loadObjectList($result);

        $file_name_xls = "Incomplete_Members_" . date("d-m-Y");
        $sheet_name = date('Y-m-d');
        chdir('..');
        chdir('libraries');
        chdir('phpxls');
        require_once 'Writer.php';
        //chdir('..');
        //chdir('com');
        $workbook = new Spreadsheet_Excel_Writer();

        $format_bold = & $workbook->addFormat();
        $format_bold->setBold();

        $format_red = & $workbook->addFormat();
        //$format_red->setBottom(2);//thick
        $format_red->setBold();
        $format_red->setColor('red');
        $format_red->setFontFamily('Arial');
        $format_red->setSize(8);

        $format_green = & $workbook->addFormat();
        $format_green->setColor('green');
        $format_green->setFontFamily('Arial');
        $format_green->setSize(8);

        $worksheet = & $workbook->addWorksheet($sheet_name);
        $worksheet->write(0, 0, "Marchant Ref. No.", $format_bold);
        $worksheet->write(0, 1, "Membership Code", $format_bold);
        $worksheet->write(0, 2, "Plan Code", $format_bold);
        $worksheet->write(0, 3, "Plan Category", $format_bold);
        $worksheet->write(0, 4, "Name", $format_bold);
        $worksheet->write(0, 5, "Email", $format_bold);
        $worksheet->write(0, 6, "Gender", $format_bold);
        $worksheet->write(0, 7, "DOB", $format_bold);
        $worksheet->write(0, 8, "Address", $format_bold);
        $worksheet->write(0, 9, "City", $format_bold);
        $worksheet->write(0, 10, "Pincode", $format_bold);
        $worksheet->write(0, 11, "State", $format_bold);
        $worksheet->write(0, 12, "Phone", $format_bold);
        $worksheet->write(0, 13, "Mobile", $format_bold);
        $worksheet->write(0, 14, "Unit", $format_bold);
        $worksheet->write(0, 15, "Validity", $format_bold);
        $worksheet->write(0, 16, "Transaction Id", $format_bold);
        $worksheet->write(0, 17, "Payment Id", $format_bold);
        $worksheet->write(0, 18, "Amount Paid", $format_bold);
        $worksheet->write(0, 19, "Fee Breakup", $format_bold);
        $worksheet->write(0, 20, "Registration Date", $format_bold);
        $worksheet->write(0, 21, "Photographs", $format_bold);
        $i = 1;
        $cnt = $i;

        if ($rows) {
            foreach ($rows as $row) {
                if ($row->photograph) {
                    $image = UPLOAD_PATH . "/members/" . $row->photograph;
                } else {
                    $image = "";
                }
                if ($row->plan_code == 'L0'):
                    $validity = "LIFETIME";
                else:
                    $validity = $objBase->FormatDate($row->valid_from, "d M Y") . "-" . $objBase->FormatDate($row->valid_to, "d M Y");
                endif;
                $plan_breakup_str = "Plan Amount:" . $row->plan_amount . "";
                if ($row->plan_breakup) {
                    $plan_breakup = unserialize($row->plan_breakup);
                    foreach ($plan_breakup as $plan_breakup_key => $plan_breakup_val) {
                        $plan_breakup_str .=$plan_breakup_key . ":" . $plan_breakup_val . "";
                    }
                }
                $worksheet->write($i, 0, $row->member_plan_id);
                $worksheet->write($i, 1, $row->membership_number);
                $worksheet->write($i, 2, $row->plan_code);
                $worksheet->write($i, 3, $row->plan_category);
                $worksheet->write($i, 4, $row->fname . " " . $row->lname);
                $worksheet->write($i, 5, $row->email);
                $worksheet->write($i, 6, $row->gender);
                $worksheet->write($i, 7, $row->dob);
                $worksheet->write($i, 8, $row->address1);
                $worksheet->write($i, 9, $objMasters->getCityName($row->city));
                $worksheet->write($i, 10, $row->postal_code);
                $worksheet->write($i, 11, $objMasters->getStateName($row->state));
                $worksheet->write($i, 12, $row->phone);
                $worksheet->write($i, 13, $row->mobile);
                $worksheet->write($i, 14, $row->unit);
                $worksheet->write($i, 15, $validity);
                $worksheet->write($i, 16, $row->TransactionID);
                $worksheet->write($i, 17, $row->PaymentID);
                $worksheet->write($i, 18, $row->total_amount);
                $worksheet->write($i, 19, $plan_breakup_str);
                $worksheet->write($i, 20, $objBase->FormatDate($row->cdate));
                $worksheet->write($i, 21, $row->photograph);
                /* if($image){
                  $worksheet->insertBitmap ($i,15,$image,50,50,60,60);
                  }else{
                  $worksheet->write($i, 15, "");
                  } */
                $i++;
            }
        }
        $workbook->send($file_name_xls . '.xls');
        $workbook->close();
        die();
    }

    public function add_new_membership($member_plan_id) {
        global $objDB, $objBase, $mail, $objEncryption;
        $memberData = $this->get_member_data($member_plan_id);

        //echo "<pre>";print_r($memberData);die;
        if ($memberData->plan_category == "IYTC" && $memberData->membership_number == "")  {

            $codeSql = "SELECT * FROM #__iytc_membership_code WHERE used=0 ORDER BY id LIMIT 0, 1";
            $codeRes = $objDB->setQuery($codeSql);
            //die($objDB->getQuery());
            $codeRow = $objDB->loadObject($codeRes);

            $iytcData['membership_number'] = $codeRow->membership_code;
            $iytcData['valid_from'] = time();
            $lastDayOfNextMonth = date("t-m-Y", strtotime("next month"));
            $iytcData['valid_to'] = strtotime($lastDayOfNextMonth) + (365 * 24 * 60 * 60);

            //update the IYTC membership no
            $objDB->update_data("#__member_plans", "member_plan_id", $iytcData, $member_plan_id);
            //prepare the data for new yhai member
            $data['member_id'] = $memberData->member_id;
            $age = $this->calculate_age($memberData->dob);

            if ($age < 18) {
                $data['plan_code'] = 'J1';
            } else {
                $data['plan_code'] = 'S1';
            }

            $data['plan_category'] = "Individual";
            $data['membership_number'] = ""; //asign new membership no
            $data['fname'] = $memberData->fname;
            $data['lname'] = $memberData->lname;
            $data['gender'] = $memberData->gender;
            $data['address1'] = $memberData->address1;
            $data['address2'] = $memberData->address2;
            $data['address3'] = $memberData->address3;
            $data['phone'] = $memberData->phone;
            $data['mobile'] = $memberData->mobile;
            $data['photograph'] = $memberData->photograph;
            $data['organisation'] = $memberData->organisation;
            $data['organisation_head'] = $memberData->organisation_head;
            $data['file'] = $memberData->file;
            $data['doc_reminder_cnt'] = $memberData->doc_reminder_cnt;
            $data['doc_reminder_date'] = $memberData->doc_reminder_date;
            $data['country'] = $memberData->country;
            $data['state'] = $memberData->state;
            $data['city'] = $memberData->city;
            $data['postal_code'] = $memberData->postal_code;
            $data['unit'] = $memberData->unit;
            $data['dob'] = $memberData->dob;
            $data['valid_from'] = $memberData->valid_from;
            $data['valid_to'] = $memberData->valid_to;
            $data['cdate'] = time();
            $data['udate'] = time();
            $data['modified_by'] = $memberData->modified_by;
            $data['status'] = 1;
            $data['type'] = $memberData->type;
            $data['changed_on'] = $memberData->changed_on;
            $data['old_membership_number'] = $memberData->old_membership_number;
            $data['total_amount'] = $memberData->total_amount;
            $data['plan_amount'] = $memberData->plan_amount;
            $data['misc_charges'] = $memberData->misc_charges;
            $data['service_tax'] = $memberData->service_tax;
            $data['TransactionID'] = $memberData->TransactionID;
            $data['TransactionResponse'] = $memberData->TransactionResponse;
            $data['PaymentID'] = $memberData->PaymentID;
            $data['refund_refrenceNo'] = $memberData->refund_refrenceNo;
            $data['plan_breakup'] = $memberData->plan_breakup;
            $data['prooftype'] = $memberData->prooftype;
            $data['proofdoc'] = $memberData->proofdoc;
            $data['member_association_id'] = $codeRow->membership_code;
            //echo "<pre>";print_r($data);die;            
            $updateData['membership_number'] = $this->yhai_iytc_membership_number($memberData);
            $plan_validity = $this->cal_plan_validity($memberData);
            $updateData['valid_from'] = $plan_validity['from'];
            $updateData['valid_to'] = $plan_validity['to'];
            $affected_rows_member_plans = $objDB->update_data("#__member_plans", "member_plan_id", $updateData, $member_plan_id);
            if ($affected_rows_member_plans) {
                $this->setMembershipCommission($yhaiMemberData, true);
                $this->send_membership_sms($yhai_member_plan_id);
            }

            $objDB->update_data("#__iytc_membership_code", "id", array("used" => 1), $codeRow->id);
            return $yhai_member_plan_id;
        }
    }

    public function send_email_to_admin($member_plan_id) {
        global $objDB, $objBase, $objMasters;
        $memberData = $this->get_member_data($member_plan_id);
        if ($memberData->email != "") {

            $email_to = $memberData->email;
            $subject = 'New Application Received.';
            $message = '
            <p>
                Dear Admin,
                <br /><br />
                New application for YHAI- IYTC Membership received on ' . $objBase->FormatDate($memberData->cdate, "d M Y") . '.
                <br /><br />
                Applicant details:
                <br /><br />

                Name: ' . $memberData->fname . ' ' . $memberData->lname . '<br />
                Contact Number: ' . $memberData->mobile . '<br />
                Email ID: ' . $memberData->email . '<br />
                Phone No.: ' . $memberData->phone . '<br /><br />                            
                Complete Postal Address: <br />
                ' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
                ' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
                ' . $objMasters->getStateName($memberData->state) . '<br /><br />
                Payment ID: ' . $memberData->PaymentID . '<br />
                Merchant Reference Number: ' . $memberData->member_plan_id . '<br />
                <br /><br />
                Application pending for approval.
                <br /><br />
                Regards,<br />
                Team YHA India
            </p>
            ';
            //$cc = array('support@isic.co.in','contact@yhaindia.org');                 
            $to = 'info@isic.co.in';
            $cc = GLOBAL_CC;
            $objBase->_send_mail($to, $subject, $message, $cc, $bcc);
        }
    }

    public function send_acceptence_email($member_plan_id, $confirmation) {
        global $objDB, $objBase, $mail, $objEncryption, $objMasters;
        $memberData = $this->get_member_data($member_plan_id);

        if ($memberData->email != "") {
            if ($confirmation == 1) {
                $email_to = $memberData->email;
                $subject = 'Membership Accepted.';
                $userMessage = '
				Dear ' . $memberData->fname . ' ' . $memberData->lname . ',<br /><br />
				Welcome to the Youth Hostels Association of India. 
				<br /><br />
				This is to confirm that your YHAI- IYTC Co Branded membership application has been accepted.
				<br /><br />
				Your YHAI Membership number is ' . $memberData->membership_number . ' valid till ' . $objBase->FormatDate($memberData->valid_to, "d M Y") . '<br />
				Your IYTC Membership number is ' . $memberData->iytc_membership_number . ' valid till ' . $objBase->FormatDate($memberData->iytc_valid_to, "M Y") . '
				<br /><br />
				Now you may enjoy the world of hostelling, adventure, discounts & much more!!!
				<br /><br />
				<a href="' . SITE_URL . 'print-membership-card-iytc.php?member_plan_id=' . $memberData->member_plan_id . '">Click here</a> to preview, save or print your membership card
				<br /><br />
				The Membership card will get delivered to you within 15 working days on the below mentioned address:
				<br /><br />
				' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
				' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
				' . $objMasters->getStateName($memberData->state) . '
				<br /><br />
				Your YHAI-IYTC Co Branded membership card is an international card.
				<br /><br />
				YHAI membership is valid in more than 90 countries with an access to more than 4000 hostels worldwide. You may visit http://www.yhaindia.org/ to participate in adventure and trekking programs, hostel bookings and much more. 
				<br /><br />
				IYTC membership is valid in 124 countries which offers you 40000 plus unique discounts at more than 126000 locations worldwide.To know more and get started now, visit http://www.yhaindia.org/ You may also join us on Facebook at http://www.facebook.com/youthhostelassociationofindia to be a part of various discussions, travel tips & latest updates. For further assistance you may write us to contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day.
				<br /><br />
				Regards,<br />
				Team YHA India
				';

                $adminMessage = '
				Dear Admin,<br /><br />
				The  YHAI- IYTC Co Branded membership application has been accepted for ' . $memberData->fname . ' ' . $memberData->lname . '.
				<br /><br />
				The Membership credentials are:
				<br /><br />
				Name of the Member: ' . $memberData->fname . ' ' . $memberData->lname . '<br />
				Date of Application: ' . $objBase->FormatDate($memberData->cdate, "d M Y") . '<br />
				Date of Membership Confirmation: ' . $objBase->FormatDate(time(), "d M Y") . '<br />
				Membership Number: ' . $memberData->membership_number . '<br />
				Membership Validity: ' . $objBase->FormatDate($memberData->valid_to, "M Y") . '<br /><br />
				Card to be sent on: <br /><br />
				' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
				' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
				' . $objMasters->getStateName($memberData->state) . '<br />
				
				<br /><br />

				Regards<br />
				Team YHA India
				';
                #$cc = 'contact@yhaindia.org';
                $cc = GLOBAL_CC;
                $bcc = null;
                $objBase->_send_mail($memberData->email, $subject, $userMessage, $cc, $bcc);
                $objBase->_send_mail('contact@yhaindia.org', $subject, $adminMessage, $cc, $bcc);
            } else if ($confirmation == 2) {
                $email_to = $memberData->email;
                $subject = 'Membership rejected.';
                $userMessage = '
				Dear ' . $memberData->fname . ' ' . $memberData->lname . ',<br /><br />
				Greetings from the Youth Hostels Association of India.<br /><br />
				This is to confirm that your YHAI- IYTC Co Branded membership application has been rejected due to the following reason:
				<br /><br />
				Reason for Application Rejection: ' . $memberData->rejection_reson . ' (' . $memberData->confirmation_note . ')
				<br /><br />
				Please send the required document/upgraded information, to contact@yhaindia.org in order to complete the membership process.
				<br /><br />
				You are requested to send us the required information within 30 days of this notification in order to process the application otherwise your payment may be forfeited.
				<br /><br />
				Your membership will be processed only after we receive the complete and correct set of information.
				<br /><br />
				For further assistance you may write us to contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day.
				<br /><br />
				Regards<br />
				Team YHA India
				
				';

                $adminMessage = '
				Dear Admin,<br /><br />
				The  YHAI- IYTC Co Branded membership application has been rejected for ' . $memberData->fname . '.
				<br /><br />
				The Membership credentials are:
				<br /><br />
				Name of the Member: ' . $memberData->fname . ' ' . $memberData->lname . '<br />
				Date of Membership Application: ' . $objBase->FormatDate($memberData->cdate, "d M Y") . '<br />
				Reason for Rejection: ' . $memberData->rejection_reson . '<br />
				Status: Not Confirmed<br /><br />
				' . $memberData->confirmation_note . '
				<br /><br />
				Regards,<br />
				Team YHA India
				';
                #$cc = 'contact@yhaindia.org';
                $cc = GLOBAL_CC;
                $bcc = null;
                $objBase->_send_mail($memberData->email, $subject, $userMessage, $cc, $bcc);
                $objBase->_send_mail('contact@yhaindia.org', $subject, $adminMessage, $cc, $bcc);
            }
        }
    }

    public function send_remainder_email($memberData) {
        global $objDB, $objBase, $mail, $objEncryption, $objMasters;
        $subject = 'Application Pending for Approval';
        $adminMessage = '
		Dear Admin,<br /><br />
		Application Pending for approval.
		<br /><br />
		Applicant details::
		<br /><br />
		Name: ' . $memberData->fname . ' ' . $memberData->lname . '<br />
		Contact Number: ' . $memberData->mobile . '<br />
		Email ID: ' . $memberData->email . '<br />
		Phone No.: ' . $memberData->phone . '<br /><br />
		Complete Postal Address: <br />
		' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
		' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
		' . $objMasters->getStateName($memberData->state) . '<br /><br />
		Payment ID: ' . $memberData->PaymentID . '<br />
		Merchant Reference Number: ' . $memberData->member_plan_id . '<br />

		<br /></br>
		Regards,<br />
		Team YHA India
		';
        $to = 'info@isic.co.in';
        $cc = GLOBAL_CC;
        $bcc = null;
        $objBase->_send_mail($to, $subject, $adminMessage, $cc, $bcc);
    }

    public function dob($dob) {
        $dobArr = explode("/", $dob);
        $dob = $dobArr[1] . "/" . $dobArr[0] . "/" . $dobArr[2];
        return $dob;
    }

    public function yhai_iytc_membership_number($data) { // Generate Membership Code 
        global $objDB;
        if (is_object($data)) {
            $contCode = '027--';
            if ($data->unit != '') {
                $stateCode = trim($data->unit) . '--';
            } else {
                $stateCode = trim($data->state) . '--';
            }
            $plan_code = trim($data->plan_code);
            $serialNo = $this->getMembershipCodeSerialNo($plan_code);
            return $contCode . $stateCode . $plan_code . "--" . $serialNo;
        }
    }

    public function get_iytc_membership_number() {
        global $objDB;
        $codeSql = "SELECT * FROM #__iytc_membership_code WHERE used=0 ORDER BY id LIMIT 0, 1";
        $codeRes = $objDB->setQuery($codeSql);
        //die($objDB->getQuery());
        $codeRow = $objDB->loadObject($codeRes);

        return $codeRow->membership_code;
    }

    public function update_iytc_membership_code($iytc_membership_number) {
        global $objDB;
        $objDB->update_data("#__iytc_membership_code", "membership_code", array("used" => 1), $iytc_membership_number);
    }

    public function getPlanCategoryByPlanCode($plan_code) {
        global $objDB;
        $sql = "SELECT a.* from #__membership_plans a WHERE plan_code = '$plan_code'";
        $result = $objDB->setQuery($sql);
        if ($objDB->num_rows($result)) {
            $row = $objDB->loadObject();
            //prexit($row);
            return $row->category;
        } else {
            return false;
        }
    }

    public function add_blank_card($data) {
        global $objDB;
        $data['TransactionResponse'] = serialize($response);
        $data['TransactionID'] = $data['source_transaction_id'];
        $data['PaymentID'] = 1;
        $data['is_offline'] = 1;
        $data['status'] = 1;
        $data['cdate'] = time();
        $data['udate'] = time();
        $data['is_blank_card'] = 1;
        $data['plan_category'] = $this->getPlanCategoryByPlanCode($data['plan_code']);

        $data['member_id'] = $member_id;

        $member_plan_id = $objDB->insert_data("#__member_plans", $data);
        $memberData = $this->get_member_data($member_plan_id);

        if ($memberData->plan_code == "CJ1" || $memberData->plan_code == "CS1") {
            $data['plan_category'] = "IYTC";
            $data['membership_number'] = $this->yhai_iytc_membership_number($memberData);
            $plan_validity = $this->cal_plan_validity($memberData);
            $data['valid_from'] = $plan_validity['from'];
            $data['valid_to'] = $plan_validity['to'];
            $data['iytc_membership_number'] = $this->get_iytc_membership_number();
            $lastDayOfNextMonth = date("t-m-Y", strtotime("next month"));
            $data['iytc_valid_to'] = strtotime($lastDayOfNextMonth) + (365 * 24 * 60 * 60);
            $data['iytc_confirmation'] = 1;

            $affected_rows_member_plans = $objDB->update_data("#__member_plans", "member_plan_id", $data, $member_plan_id);
            $this->update_iytc_membership_code($data['iytc_membership_number']);
            //prexit($data);
        } else {
            $data['membership_number'] = $this->generate_membership_number($memberData);
            $plan_validity = $this->cal_plan_validity($memberData);
            $data['valid_from'] = $plan_validity['from'];
            $data['valid_to'] = $plan_validity['to'];
            $affected_rows_member_plans = $objDB->update_data("#__member_plans", "member_plan_id", $data, $member_plan_id);
        }
    }

    public function list_membership_basic_prices($category = '') {
        global $objDB;
        $membership_prices = null;
        if ($category != '') {
            $whereSql = "AND a.category='$category'";
        }
        $sql = "SELECT a.plan_code, a.total_amount, a.service_tax, a.fee FROM #__membership_plans a WHERE 1 AND a.status=1  $whereSql";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            $rows = $objDB->loadObjectList();
            foreach ($rows as $row) {
                $service_tax_percent = $row->service_tax;
                //$service_tax_value = ($row->total_amount * $service_tax_percent) / 100;
                //$membership_prices[$row->plan_code] = ceil($row->total_amount + $service_tax_value);
                $membership_prices[$row->plan_code] = $row->fee;
            }
        } else {
            return false;
        }

        return $membership_prices;
    }

    private function check_expired_member_plans() {
        global $objDB;
        $sql = "SELECT COUNT(*) as count_total FROM #__member_plans a WHERE valid_to < '" . time() . "' AND type='a' AND plan_code!='L0' AND membership_number!='' ";
        $result = $objDB->setQuery($sql);
        if ($objDB->num_rows($result)) {
            $row = $objDB->loadObject();
            return $row->count_total;
        } else {
            return false;
        }
    }

    private function get_expired_member_plans() {
        global $objDB;
        $sql = "SELECT a.* FROM #__member_plans a WHERE valid_to < '" . time() . "' AND type='a' AND plan_code!='L0' AND membership_number!='' ";
        $result = $objDB->setQuery($sql);
        if ($objDB->num_rows($result)) {
            return $rows = $objDB->loadObjectList();
        } else {
            return false;
        }
    }

    public function disable_expired_member_plans() {
        global $objDB;
        $arr = null;
        $count_expired_member_plans = $this->check_expired_member_plans();
        if ($count_expired_member_plans) {
            $expired_member_plans = $this->get_expired_member_plans();
            if ($expired_member_plans) {
                foreach ($expired_member_plans as $expired_member_plan) {
                    // set Inactive/expired
                    $objDB->update_data("#__member_plans", "member_plan_id", array("status" => 0, "type" => 'e'), $expired_member_plan->member_plan_id);
                    $arr [] = $expired_member_plan;
                }
            }
        }
        return $arr;
    }

    public function member_plans_type($type) {
        $plans_type = '';
        switch ($type) {
            case 'a':
                $plans_type = '<span style="color:green;">Active</span>';
                break;
            case 'e':
                $plans_type = '<span style="color:red;">Expired</span>';
                break;
            case 'e':
                $plans_type = '<span style="color:#F7941E;">Renewed</span>';
                break;
            case 'u':
                $plans_type = '<span style="color:#F7941E;">Upgraded</span>';
                break;
        }
        return $plans_type;
    }

    public function membership_renew_register($member_plan_id) {
        global $objDB, $objMembershipPlans;
        $member_data = $this->get_member_data($member_plan_id);
        $data = (array) $member_data;
        $data['old_membership_number'] = $member_data->membership_number;
        $plan_amount = $this->get_membership_plan_amount($member_data->plan_code);
        $data['member_plan_id'] = "";
        $data['total_amount'] = $plan_amount['total_amount']; // inclusive Service tax
        $data['plan_amount'] = $plan_amount['plan_amount']; // exclusive Service tax
        $data['service_tax'] = $plan_amount['service_tax'];
        $data['misc_charges'] = $plan_amount['misc_charges'];
        $data['plan_breakup'] = $objMembershipPlans->get_serialized_paln_breakup($member_data->plan_code);
        $data['membership_number'] = "";
        $data['valid_from'] = "";
        $data['valid_to'] = "";
        $data['cdate'] = time();
        $data['udate'] = time();
        $data['status'] = 0;
        $data['TransactionID'] = '';
        $data['TransactionResponse'] = '';
        $data['PaymentID'] = '';
        $data['refund_refrenceNo'] = '';
        $data['refund_response'] = '';
        $data['plan_breakup'] = '';
        $_SESSION['membership_renew'] = $data;
        $new_member_plan_id = $objDB->insert_data("#__member_plans", $data);
        $_SESSION['membership_renew']['member_plan_id'] = $new_member_plan_id;
        if ($new_member_plan_id) {
            return $new_member_plan_id;
        } else {
            return false;
        }
    }

    public function approve_renew_membership_plan($member_plan_id, $response) {
        global $objDB;
        $memberData = $this->get_member_data($member_plan_id);
        if ($memberData && $memberData->plan_category != "IYTC") {
            if ($memberData->membership_number == "") {
                $data['status'] = 1;
                $data['type'] = 'a';
                $data['udate'] = time();
                $data['membership_number'] = $this->generate_membership_number($memberData);
                $plan_validity = $this->cal_plan_validity($memberData);
                $data['valid_from'] = $plan_validity['from'];
                $data['valid_to'] = $plan_validity['to'];
                $data['TransactionID'] = $response['TransactionID'];
                $data['PaymentID'] = $response['PaymentID'];
                $data['TransactionResponse'] = serialize($response);
                $affected_rows_member_plans = $objDB->update_data("#__member_plans", "member_plan_id", $data, $member_plan_id);
                $affected_rows_member = $objDB->update_data("#__member", "member_id", $data, $memberData->member_id);
                //die(">>>".$affected_rows_member_plans);
                if ($affected_rows_member_plans) {
                    $this->setMembershipCommission($memberData, true);
                    $this->send_renew_membership_email($member_plan_id);
                    $this->send_renew_membership_sms($member_plan_id);
                }
            }
        } else {
            $data['TransactionID'] = $response['TransactionID'];
            $data['PaymentID'] = $response['PaymentID'];
            $data['TransactionResponse'] = serialize($response);
            $data['udate'] = time();

            if ($memberData->iytc_confirmation == 1) {
                $data['status'] = 1;
                $data['type'] = 'a';
                $data['membership_number'] = $this->yhai_iytc_membership_number($memberData);
                $plan_validity = $this->cal_plan_validity($memberData);
                $data['valid_from'] = $plan_validity['from'];
                $data['valid_to'] = $plan_validity['to'];

                $data['iytc_membership_number'] = $this->get_iytc_membership_number();
                $lastDayOfNextMonth = date("t-m-Y", strtotime("next month"));
                $data['iytc_valid_to'] = strtotime($lastDayOfNextMonth) + (365 * 24 * 60 * 60);
            }
            $affected_rows_member = $objDB->update_data("#__member", "member_id", $data, $memberData->member_id);
            $affected_rows_member_plans = $objDB->update_data("#__member_plans", "member_plan_id", $data, $member_plan_id);

            $this->update_iytc_membership_code($data['iytc_membership_number']);

            if ($affected_rows_member_plans) {
                $this->setMembershipCommission($memberData, true);
                $this->send_renew_membership_email($member_plan_id);
                $this->send_renew_email_to_admin($member_plan_id);
            }
        }
        return $affected_rows_member_plans;
    }

    public function send_renew_membership_email($member_plan_id) {
        global $objDB, $objBase, $mail, $objEncryption;
        $memberData = $this->get_member_data($member_plan_id);
        if ($memberData->email != "") {
            $email_to = $memberData->email;
            $subject = 'Welcome to Youth Hostels Association of India';
            $message = $this->get_renew_membership_email_message($memberData);
            $cc = GLOBAL_CC;
            $bcc = null;
            $objBase->_send_mail($email_to, $subject, $message, $cc, $bcc);
        }
    }

    public function send_renew_membership_sms($member_plan_id) {
        global $objDB, $objBase;
        //$memberData = $this->get_member_data($member_plan_id);
        // SMS API Script Start
        // SMS API Script End
        return true;
    }

    private function get_renew_membership_email_message($memberData) {
        global $objEncryption, $objMasters;
        $message = '';
        if ($memberData->plan_code == "CJ1" || $memberData->plan_code == "CS1") {
            $message = '
				Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />
				Greetings from the Youth Hostels Association of India.<br /><br />
				
				This is to acknowledge that we have received your application regarding the YHAI & IYTC Co Branded Membership.<br /><br />

				Please note your Transaction Details:<br /><br />
				<b>
				Payment ID: ' . $memberData->PaymentID . '<br />
				Merchant Reference Number: ' . $memberData->member_plan_id . '<br />
				
				</b>
				<br /><br />
				You will receive the confirmation regarding the Acceptance/Rejection of your membership application within 2 working days subject to valid age/identity documents upload. For successfully accepted applications, the membership card will be dispatched within 15 working days.
				<br /><br />
				Your YHAI-IYTC Co Branded membership card is an international card.
				<br /><br />
				YHAI membership is valid in more than 90 countries with an access to more than 4000 hostels worldwide. You may visit http://www.yhaindia.org/ to participate in adventure and trekking programs, hostel bookings and much more.			<br /><br />
				IYTC membership is valid in 124 countries which offers you 40000 plus unique discounts at more than 126000 locations worldwide. To know more and get started now, visit http://www.yhaindia.org/. You may also join us on Facebook at http://www.facebook.com/youthhostelassociationofindia to be a part of various discussions, travel tips & latest updates. For further assistance you may write us to contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day.
				<br /><br />
				Regards<br />
				Team YHA India
			';
        } else if (strstr($memberData->plan_code, "J1") or strstr($memberData->plan_code, "S1") or strstr($memberData->plan_code, "S2")) {
            $message = '
            Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />

            Welcome to Youth Hostels Association of India.<br /><br />

            Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>' . date('d F Y', $memberData->valid_to) . '</strong>. <br /><br />

            Your membership card will be dispatched on following address within 15 working days:<br /><br />
            
            ' . $memberData->fname . ' ' . $memberData->lname . '<br />
            ' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
            ' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
            ' . $objMasters->getStateName($memberData->state) . '<br />
            ' . $memberData->phone . ', ' . $memberData->mobile . '<br /><br />
			
            <br />
              Please send the photo by e-mail with your temporary membership card details to <a href="mailto:priyank@yhaindia.org" target="_blank"><strong>priyank@yhaindia.org</strong></a> if not uploaded at the time of membership registration.&nbsp;<br />
              If any membership card lost or stolen there is  Duplicate card fee as under</p>
            <ul type="disc">
              <li>PVC Card (for Life membership) : Rs.100 (including postage charges)</li>
              <li>Paper Card (for 1 &amp; 2 years) : Rs.60 (including postage charges)</li>
            </ul>
            <br /><br />
            Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

            To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

            
            You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br />
			<br />
            For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br />
            <br /><br /><br />
            Happy Hostelling,<br />
            Team YHA India
            <br /><br /><br />
            <small><strong>Disclaimer</strong><br />
            This is an auto-generated communication. The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, retransmission, dissemination or other use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender and delete the material from any computer.<br />
            <br />
            By joining YHA India, you are indicating that you have read, understood, and agree to YHA India\'s User Agreement, Terms & Conditions, and Privacy Policies <br />
            </small>            
           ';
        } else if (strstr($memberData->plan_code, "I1") or strstr($memberData->plan_code, "I5") or strstr($memberData->plan_code, "INS_ABV_12_1") or strstr($memberData->plan_code, "INS_ABV_12_5")) {
            $message = '
            Dear <strong>' . $memberData->email . '</strong>,<br /><br />

            Welcome to Youth Hostels Association of India.<br /><br />

            Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>' . date('d F Y', $memberData->valid_to) . '</strong>. <br /><br />

            Your membership card will be dispatched on following address within 15 working days:<br /><br />
            
            ' . $memberData->organisation . '<br />
            ' . $memberData->organisation_head . '<br />
            ' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
            ' . $objMasters->getCityName($memberData->city) . '–' . $memberData->postal_code . '<br />
            ' . $objMasters->getStateName($memberData->state) . '<br />
            ' . $memberData->phone . ', ' . $memberData->mobile . '<br /><br />			
			

            Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

            To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

            
            You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br /><br />
			<br />
            For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br /><br />
            <br /><br /><br /><br />
            Happy Hostelling,<br />
            Team YHA India            
            <br /><br /><br /><br />
            <small><strong>Disclaimer</strong><br />
            This is an auto-generated communication. The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, retransmission, dissemination or other use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender and delete the material from any computer.<br />
            <br />
            By joining YHA India, you are indicating that you have read, understood, and agree to YHA India\'s User Agreement, Terms & Conditions, and Privacy Policies <br />
            </small>     
           ';
        } else {
            $message = '
            Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />

            Welcome to Youth Hostels Association of India.<br /><br />

            Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>LIFETIME</strong>. <br /><br />

            Your membership card will be dispatched on following address within 15 working days:<br /><br />
            
            ' . $memberData->fname . ' ' . $memberData->lname . '<br />
            ' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
            ' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
            ' . $objMasters->getStateName($memberData->state) . '<br />
            ' . $memberData->phone . ', ' . $memberData->mobile . '<br /><br />
			
            <br />
              Please send the photo by e-mail with your temporary membership card details to <a href="mailto:priyank@yhaindia.org" target="_blank"><strong>priyank@yhaindia.org</strong></a> if not uploaded at the time of membership registration.&nbsp;<br />
              If any membership card lost or stolen there is  Duplicate card fee as under</p>
            <ul type="disc">
              <li>PVC Card (for Life membership) : Rs.100 (including postage charges)</li>
              <li>Paper Card (for 1 &amp; 2 years) : Rs.60 (including postage charges)</li>
            </ul>
            <br /><br />
            Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

            To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

            
            You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br />
			<br />
            For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br />
            <br /><br /><br />
            Happy Hostelling,<br />
            Team YHA India
            <br /><br /><br />
            <small><strong>Disclaimer</strong><br />
            This is an auto-generated communication. The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, retransmission, dissemination or other use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender and delete the material from any computer.<br />
            <br />
            By joining YHA India, you are indicating that you have read, understood, and agree to YHA India\'s User Agreement, Terms & Conditions, and Privacy Policies <br />
            </small>            
           ';
        }

        return $message;
    }

    public function membership_upgrade_register($member_plan_id) {
        global $objDB, $objMembershipPlans;
        $member_data = $this->get_member_data($member_plan_id);
        $data = (array) $member_data;
        $data['plan_code'] = 'L0';
        //print "<pre>";print_r($member_data);exit;
        $data['old_membership_number'] = $member_data->membership_number;
        $plan_amount = $this->get_membership_plan_amount($data['plan_code']);
        $data['member_plan_id'] = "";
        $data['total_amount'] = $plan_amount['total_amount']; // inclusive Service tax
        $data['plan_amount'] = $plan_amount['plan_amount']; // exclusive Service tax
        $data['service_tax'] = $plan_amount['service_tax'];
        $data['misc_charges'] = $plan_amount['misc_charges'];
        $data['plan_breakup'] = $objMembershipPlans->get_serialized_paln_breakup($data['plan_code']);
        $data['membership_number'] = "";
        $data['valid_from'] = "";
        $data['valid_to'] = "";
        $data['cdate'] = time();
        $data['udate'] = time();
        $data['status'] = 0;
        $data['TransactionID'] = '';
        $data['TransactionResponse'] = '';
        $data['PaymentID'] = '';
        $data['refund_refrenceNo'] = '';
        $data['refund_response'] = '';
        $data['plan_breakup'] = '';
        $_SESSION['membership_upgrade'] = $data;
        $new_member_plan_id = $objDB->insert_data("#__member_plans", $data);
        $_SESSION['membership_upgrade']['member_plan_id'] = $new_member_plan_id;
        if ($new_member_plan_id) {
            return $new_member_plan_id;
        } else {
            return false;
        }
    }

    public function approve_upgrade_membership_plan($member_plan_id, $response) {
        global $objDB;
        $memberData = $this->get_member_data($member_plan_id);
        if ($memberData->membership_number == "") {
            $data['status'] = 1;
            $data['type'] = 'a';
            $data['udate'] = time();
            $data['membership_number'] = $this->generate_membership_number($memberData);
            $plan_validity = $this->cal_plan_validity($memberData);
            $data['valid_from'] = $plan_validity['from'];
            $data['valid_to'] = $plan_validity['to'];
            $data['TransactionID'] = $response['TransactionID'];
            $data['PaymentID'] = $response['PaymentID'];
            $data['TransactionResponse'] = serialize($response);
            $affected_rows_member_plans = $objDB->update_data("#__member_plans", "member_plan_id", $data, $member_plan_id);
            $affected_rows_member = $objDB->update_data("#__member", "member_id", $data, $memberData->member_id);
            //print "<pre>Outer:";print_r($memberData);exit;
            if ($affected_rows_member_plans) {
                /* Disable Old Membership */
                $dataX['status'] = 0;
                $dataX['type'] = 'u';
                $affected_rows_member_plansX = $objDB->update_data("#__member_plans", "membership_number", $dataX, $memberData->old_membership_number);
                //print "<pre>Inner:";print_r($memberData);exit;
                $this->setMembershipCommission($memberData, true);
                $this->send_upgrade_membership_email($member_plan_id);
                $this->send_upgrade_membership_sms($member_plan_id);
            }
        }
        return $affected_rows_member_plans;
    }

    public function send_upgrade_membership_email($member_plan_id) {
        global $objDB, $objBase, $mail, $objEncryption;
        $memberData = $this->get_member_data($member_plan_id);
        if ($memberData->email != "") {
            $email_to = $memberData->email;
            $subject = 'Welcome to Youth Hostels Association of India';
            $message = $this->get_renew_membership_email_message($memberData);
            #$cc = 'contact@yhaindia.org';  
            $cc = GLOBAL_CC;
            $bcc = null;
            $objBase->_send_mail($email_to, $subject, $message, $cc, $bcc);
        }
    }

    public function send_upgrade_membership_sms($member_plan_id) {
        global $objDB, $objBase;
        //$memberData = $this->get_member_data($member_plan_id);
        // SMS API Script Start
        // SMS API Script End
        return true;
    }

    private function get_upgrade_membership_email_message($memberData) {
        global $objEncryption, $objMasters;
        $message = '';
        $message = '
		Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />

		Welcome to Youth Hostels Association of India.<br /><br />

		Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>LIFETIME</strong>. <br /><br />

		Your membership card will be dispatched on following address within 15 working days:<br /><br />
		
		' . $memberData->fname . ' ' . $memberData->lname . '<br />
		' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
		' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
		' . $objMasters->getStateName($memberData->state) . '<br />
		' . $memberData->phone . ', ' . $memberData->mobile . '<br /><br />
		
		<br />
		  Please send the photo by e-mail with your temporary membership card details to <a href="mailto:priyank@yhaindia.org" target="_blank"><strong>priyank@yhaindia.org</strong></a> if not uploaded at the time of membership registration.&nbsp;<br />
		  If any membership card lost or stolen there is  Duplicate card fee as under</p>
		<ul type="disc">
		  <li>PVC Card (for Life membership) : Rs.100 (including postage charges)</li>
		  <li>Paper Card (for 1 &amp; 2 years) : Rs.60 (including postage charges)</li>
		</ul>
		<br /><br />
		Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

		To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

		
		You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br />
		<br />
		For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br />
		<br /><br /><br />
		Happy Hostelling,<br />
		Team YHA India
		<br /><br /><br />
		<small><strong>Disclaimer</strong><br />
		This is an auto-generated communication. The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, retransmission, dissemination or other use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender and delete the material from any computer.<br />
		<br />
		By joining YHA India, you are indicating that you have read, understood, and agree to YHA India\'s User Agreement, Terms & Conditions, and Privacy Policies <br />
		</small>            
	   ';
        return $message;
    }

    public function getAssociativeBCList($bc_number) {
        global $objDB;
        $sql = "SELECT tem1 .*   from #__member_plans tem1 LEFT JOIN #__member_plans tem2 on 
		(CASE when  tem2.bc_parent=''  THEN (tem1.bc_parent=tem2.member_plan_id or tem1.member_plan_id=tem2.member_plan_id) ELSE (tem1.bc_parent = tem2.bc_parent or tem1.member_plan_id=tem2.bc_parent ) END) where tem2.membership_number='$bc_number'
		AND tem1.is_blank_card=1 ORDER BY tem1.cdate DESC
		";
        $result = $objDB->setQuery($sql);
        if ($objDB->num_rows($result)) {
            return $rows = $objDB->loadObjectList();
        } else {
            return false;
        }
    }

    public function replaceBC($memberData) {
        global $objDB;
        $data = array();
        if ($memberData) {
            $data = (array) $memberData;
            unset($data['member_plan_id']);
            $data['status'] = 1;
            $data['type'] = 'a';
            $data['cdate'] = time();
            $data['udate'] = time();
            $data['membership_number'] = $this->generate_membership_number($memberData);
            $plan_validity = $this->cal_plan_validity_blankcard($memberData);
            $data['valid_from'] = $plan_validity['from'];
            $data['valid_to'] = $plan_validity['to'];
            $data['bc_parent'] = ($memberData->bc_parent) ? $memberData->bc_parent : $memberData->member_plan_id;

            /*
             *  Admin Login capture start here
             */
            $data['last_updated_by'] = $_SESSION['user']['login']->admin_id;
            $data['last_updated_on'] = time();
            /*
             *  Admin Login capture start end
             */
            $member_plan_id = $objDB->insert_data("#__member_plans", $data);
            //die(">>>".$member_plan_id);
            if ($member_plan_id) {
                $dataX['status'] = 0;
                $dataX['type'] = 'e';
                /*
                 *  Admin Login capture start here
                 */
                $dataX['last_updated_by'] = $_SESSION['user']['login']->admin_id;
                $dataX['last_updated_on'] = time();
                /*
                 *  Admin Login capture start end
                 */
                $affected_rows_Xmember_plans = $objDB->update_data("#__member_plans", "member_plan_id", $dataX, $memberData->member_plan_id);
                $this->send_replace_blankcard_membership_email($data);
            }
        } else {
            return false;
        }
    }

    public function cal_plan_validity_blankcard($data) {
        $validity = array();
        $from = time();
        switch ($data->plan_code) {
            case 'J1':
            case 'S1':
            case 'CJ1':
            case 'CS1':
            case 'I1':
            case 'INS_ABV_12_1':
                if (date('m') > 9)
                    $to = mktime(0, 0, 0, 12, 31, date('Y') + 1);
                else
                    $to = mktime(0, 0, 0, 12, 31, date('Y'));
                break;
            case 'I2':
            case 'S2':
                if (date('m') > 9)
                    $to = mktime(0, 0, 0, 12, 31, date('Y') + 2);
                else
                    $to = mktime(0, 0, 0, 12, 31, date('Y') + 1);
                break;
            case 'L0':
                //$to = mktime(0, 0, 0, 12, 31, date("Y") + 99);
                $to = mktime(0, 0, 0, 12, 31, 2037);
                break;
            case 'I5':
            case 'INS_ABV_12_5':
                if (date('m') > 9)
                    $to = mktime(0, 0, 0, 12, 31, date('Y') + 5);
                else
                    $to = mktime(0, 0, 0, 12, 31, date('Y') + 4);
                break;
            case 'IYTC':
                $to = strtotime("365 days");
                break;
        }
        $validity['from'] = $from;
        $validity['to'] = $to;
        return $validity;
    }

    private function send_replace_blankcard_membership_email($memberData) {
        global $objEncryption, $objMasters;
        $message = '';
        $message = '
		Dear <strong>' . $memberData->fname . ' ' . $memberData->lname . '</strong>,<br /><br />

		Welcome to Youth Hostels Association of India.<br /><br />

		Thanks for your membership with YHA India. Your YHA India membership number is <strong>' . $memberData->membership_number . '</strong> valid till <strong>LIFETIME</strong>. <br /><br />

		Your membership card will be dispatched on following address within 15 working days:<br /><br />
		
		' . $memberData->fname . ' ' . $memberData->lname . '<br />
		' . $memberData->address1 . ' ' . $memberData->address2 . ' ' . $memberData->address3 . '<br />
		' . $objMasters->getCityName($memberData->city) . '-' . $memberData->postal_code . '<br />
		' . $objMasters->getStateName($memberData->state) . '<br />
		' . $memberData->phone . ', ' . $memberData->mobile . '<br /><br />
		
		<br />
		  Please send the photo by e-mail with your temporary membership card details to <a href="mailto:priyank@yhaindia.org" target="_blank"><strong>priyank@yhaindia.org</strong></a> if not uploaded at the time of membership registration.&nbsp;<br />
		  If any membership card lost or stolen there is  Duplicate card fee as under</p>
		<ul type="disc">
		  <li>PVC Card (for Life membership) : Rs.100 (including postage charges)</li>
		  <li>Paper Card (for 1 &amp; 2 years) : Rs.60 (including postage charges)</li>
		</ul>
		<br /><br />
		Your YHA India membership card is an international card valid in more than 80 countries with an access to more than 4500 hostels worldwide. You may visit <a href="' . SITE_URL . '">' . SITE_URL . '</a> to participate in adventure and trekking programs, hostel bookings and much more. <br /><br />

		To know more and get started now, visit <a href="' . SITE_URL . '">' . SITE_URL . '</a><br /><br />

		
		You may also join us on Facebook at <a href="http://www.facebook.com/youthhostelassociationofindia">http://www.facebook.com/youthhostelassociationofindia</a> to be a part of various discussions, travel tips, latest updates and much more <br />
		<br />
		For any further assistance you may write us at contact@yhaindia.org or call us on 011-45999000 between 10.00 am to 5.00 pm on any working day <br />
		<br /><br /><br />
		Happy Hostelling,<br />
		Team YHA India
		<br /><br /><br />
		<small><strong>Disclaimer</strong><br />
		This is an auto-generated communication. The information transmitted is intended only for the person or entity to which it is addressed and may contain confidential and/or privileged material. Any review, retransmission, dissemination or other use of, or taking of any action in reliance upon, this information by persons or entities other than the intended recipient is prohibited. If you received this in error, please contact the sender and delete the material from any computer.<br />
		<br />
		By joining YHA India, you are indicating that you have read, understood, and agree to YHA India\'s User Agreement, Terms & Conditions, and Privacy Policies <br />
		</small>            
	   ';
        if ($memberData->email != "") {
            $email_to = $memberData->email;
            if (!$email_to) {
                $email_to = 'it@yhaindia.org';
            }
            $subject = 'Welcome to Youth Hostels Association of India';
            #$cc = 'contact@yhaindia.org';
            $cc = GLOBAL_CC;
            $bcc = null;
            $objBase->_send_mail($email_to, $subject, $message, $cc, $bcc);
        }
    }

    public function show_membership_plan_validity($valid_from, $valid_to, $plan_code) {
        $validity = "";
        switch ($plan_code) {
            case 'L0':
                $validity = date('F, Y', $valid_from) . " - LIFETIME";
                break;
            default:
                $validity = date('F, Y', $valid_from) . " - " . date('F, Y', $valid_to);
        }
        return $validity;
    }

    public function get_member_info_by_memberhip_code($membership_code) {
        global $objDB;
        $sql = "SELECT b.*,a.email,a.password,a.status as member_status FROM #__member_plans b
        LEFT JOIN #__member a ON b.member_id=a.member_id        
        WHERE b.membership_number='$membership_code' ORDER BY member_plan_id DESC LIMIT 1;";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObject($result);
        } else {
            return false;
        }
    }

    public function get_membership_plan_info($plan_code) {
        global $objDB;
        $sql = "SELECT a.* from #__membership_plans a WHERE plan_code = '$plan_code'";
        $result = $objDB->setQuery($sql);
        if ($objDB->num_rows($result)) {
            return $objDB->loadObject();
        } else {
            return false;
        }
    }

    public function export_login_logs($sql_whr = "") {
        global $objDB, $objBase, $objMasters;
        $sql = "SELECT a.*, b.* FROM #__admin_login_logs a LEFT JOIN #__admin b ON a.log_admin_id=b.admin_id "
                . " WHERE 1 ORDER BY log_id DESC $sql_whr ";
        $result = $objDB->setQuery($sql);
        // echo $objDB->getQuery();die;
        $rows = $objDB->loadObjectList($result);

        $file_name_xls = "Admin_Login_History_" . date("d-m-Y");
        $sheet_name = date('Y-m-d');

        $workbook = $this->loadWorkBookLib();

        $format_bold = $this->workSheetHeadingFormat($workbook);
        $format_gray = $this->workSheetOddFormat($workbook);
        $format_white = $this->workSheetEvenFormat($workbook);

        $worksheet = & $workbook->addWorksheet($sheet_name);
        $worksheet->write(0, 0, "Admin Name", $format_bold);
        $worksheet->write(0, 1, "Login Date/Time", $format_bold);
        $worksheet->write(0, 2, "Logout Date/Time", $format_bold);
        $worksheet->write(0, 3, "IP Address", $format_bold);
        $worksheet->write(0, 4, "Details", $format_bold);

        $i = 1;
        $cnt = $i;
        if ($rows) {
            foreach ($rows as $row) {
                $format = ($i % 2 == 0) ? $format_gray : $format_white;
                $login_date = ($row->log_in_time) ? date("Y-m-d h:i a", $row->log_in_time) : "NA";
                $out_date = ($row->log_out_time) ? date("Y-m-d h:i a", $row->log_out_time) : "NA";
                if ($row->log_text) {
                    $arr_text = json_decode($row->log_text);
                    if ($arr_text) {
                        foreach ($arr_text as $k => $v) {
                            $detail_text .= $k . " : " . $v . "\n";
                        }
                    }
                } else {
                    echo "NA";
                }
                $worksheet->write($i, 0, $row->fname . " " . $row->lname, $format);
                $worksheet->write($i, 1, $login_date, $format);
                $worksheet->write($i, 2, $out_date, $format);
                $worksheet->write($i, 3, $row->log_ip, $format);
                $worksheet->write($i, 4, $detail_text, $format);
                $i++;
            }
        }
        $workbook->send($file_name_xls . '.xls');
        $workbook->close();
        die();
    }
    
    public function get_pvc_card_member_info_by_memberhip_code($membership_code) {
        global $objDB;
        $sql = "SELECT b.*,a.email,a.password,a.status as member_status FROM #__member_plans b
        LEFT JOIN #__member a ON b.member_id=a.member_id        
        WHERE b.membership_number='$membership_code' AND plan_code='L0' ORDER BY member_plan_id DESC LIMIT 1;";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObject($result);
        } else {
            return false;
        }
    }
	
	public function get_member_info_by_memberhip_code_addresslog($membership_code) {
        global $objDB;
        $sql = "SELECT b.*,a.email,a.password,a.status as member_status FROM #__member_plans b
        LEFT JOIN #__member a ON b.member_id=a.member_id        
        WHERE b.membership_number='$membership_code'  ORDER BY member_plan_id DESC LIMIT 1;";
        $result = $objDB->setQuery($sql);
        //die($objDB->getQuery());
        if ($objDB->num_rows($result)) {
            return $objDB->loadObject($result);
        } else {
            return false;
        }
    }
}
$objMembers = new clsMembers();