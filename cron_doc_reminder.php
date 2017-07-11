<?php
include_once("includes/framework.php");
$current_time = time();
$sql = "SELECT m.email,mp.* FROM #__member m 
        INNER JOIN #__member_plans mp ON m.member_id = mp.member_id
        WHERE mp.status='1' AND (mp.TransactionID > 0) AND mp.type='a' and photograph='' AND plan_code='L0'
        AND (doc_reminder_cnt < 3) and (doc_reminder_date=0 OR ((doc_reminder_date + 432000) < $current_time))
        ORDER BY mp.cdate DESC LIMIT 1;
        ";
$result = $objDB->setQuery($sql);
//echo $objDB->getQuery();die;
$rows = $objDB->loadObjectList();
if ($rows) {
    foreach ($rows as $row) {
        $email_to = $row->email;
        $subject = 'Documents Remider Alert:YHAI';
        $message = '
        <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>YHAI Email</title>
                <link href="http://www.yhaindia.org/templates/email/images/style.css" rel="stylesheet" type="text/css" />
            </head>
            <body>
                <div id="main">
                    <div style="margin-left:10px;"><img src="http://www.yhaindia.org/templates/email/images/header.png" width="700" height="184"></div><div style="margin:20px 0 0 20px; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px; font-weight:bold" align="left">Dear ' . $row->fname . ' ' . $row->lname . ',</div> <br />
                    <div class="main-content" align="left" style="margin:20px 0 0 20px; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px">Greetings from Youth Hostels Association of India,<br /><br /> 
                        Your Temporary YHAI Membership Number is: <strong>' . $row->membership_number . '</strong><br /><br />
                        We have received your request to process the YHAI Lifetime membership card.
                        The membership card process is pending for your passport size photograph. 
                        Kindly submit the same at the earliest. <br /><br />

                        You may send the photograph via email (image size less than 50 kb) at it@yhaindia.org , alternatively,<br />
                        you may post the same at below mentioned address: <br /><br />

                        Membership Department <br />
                        Youth Hostel Association of India 5,<br /> 
                        Nyaya Marg, Chanakyapuri, <br />
                        New Delhi- 110021, India, <br />
                        Phone: 011-45999016, 45999000 <br /><br />

                        Please mention your Name, Temporary YHAI Membership Number and Mobile Number while sending the photograph 
                        through Email/Post. Please ignore if you have already sent your photographs to process your Lifetime 
                        Membership Card. <br /><br /><br /><br />

                        Happy Hostelling,<br /> 
                        Team YHAI<br /><br />
                    </div>
                    <div align="center" style="font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px">
                        Youth Hostels Association of India (YHAI)<br />
                        5, Nyaya Marg, Chanakyapuri, New Delhi-110021, INDIA <br />
                        Tel. 011-45999000, +91-11-26110250 | Tel. +91-11-26871969 | Fax +91-11-26113469<br />
                        Email: contact@yhaindia.org | www.yhaindia.org<br /><br />
                    </div>
                </div>
            </body>
        </html>';
        $cc = 'it@yhaindia.org';        
        if ($objBase->_send_mail($email_to, $subject, $message, $cc, $bcc, null, null, null, false)) {
            $sql_update = "UPDATE #__member_plans SET doc_reminder_cnt = doc_reminder_cnt + 1, doc_reminder_date='$current_time' where member_plan_id='" . $row->member_plan_id . "' ";
            $objDB->query($sql_update);
            $f = @fopen("logs/".date('Ymd')."_doc_reminder.log", 'a+');
            if ($f) {
                @fputs($f, date("m.d.Y g:ia") . "  sent reminder alert to " . $row->email . "\n");
                @fclose($f);
            }
        } else {
            $f = @fopen("logs/".date('Ymd')."_doc_reminder.log", 'a+');
            if ($f) {
                @fputs($f, date("m.d.Y g:ia") . "  fail reminder alert to " . $row->email . "\n");
                @fclose($f);
            }
        }
    }
}
?>