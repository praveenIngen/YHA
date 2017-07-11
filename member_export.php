<?php
@session_name("YHAI");
@session_start();
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');
@date_default_timezone_set("Asia/Calcutta");
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT ^ E_WARNING);
ini_set("display_errors", 1);

/*
 * Set Memory Limit
 */
ini_set('memory_limit', '1024M');


define('PATH_BASE', dirname(__FILE__) . DIRECTORY_SEPARATOR );
require_once PATH_BASE . '/includes/defines.php';

// Installation check, and check on removal of the install directory.
if ($_SERVER['REMOTE_ADDR'] != "182.71.119.110") {
    //include(PATH_BASE . "/maintenance.php");
    //die;
}

if (!file_exists(PATH_CONFIGURATION . '/configuration.php')) {
    die('No configuration file found.');
}
//  imports library .
//
require_once PATH_BASE . '/configuration.php';
$Config = new Config();
define('TABLE_PREFIX', $Config->dbprefix);
include_once(PATH_COMMON . '/Entity.php');
include_once(PATH_LIBRARIES . '/db.class.php');
include_once(PATH_LIBRARIES . '/base.class.php');
include_once(PATH_LIBRARIES . '/object.class.php');
include_once(PATH_LIBRARIES . '/input.class.php');
include_once(PATH_LIBRARIES . '/output.class.php');
include_once(PATH_LIBRARIES . '/request.class.php');
include_once(PATH_LIBRARIES . '/response.class.php');
include_once(PATH_LIBRARIES . '/string.class.php');
include_once(PATH_LIBRARIES . '/uri.class.php');
include_once(PATH_LIBRARIES . '/encryption.class.php');
include_once(PATH_LIBRARIES . '/paginator.class.php');
include_once(PATH_LIBRARIES . '/upload.class.php');
require_once(PATH_LIBRARIES . "/class.phpmailer.php");
include_once(PATH_LIBRARIES . '/users.class.php');
include_once(PATH_LIBRARIES . '/masters.class.php');
include_once(PATH_LIBRARIES . '/pages.class.php');
include_once(PATH_LIBRARIES . '/menu.class.php');
include_once(PATH_LIBRARIES . '/banners.class.php');
include_once(PATH_LIBRARIES . '/newsletter.class.php');
include_once(PATH_LIBRARIES . '/membership-plans.class.php');
 include_once(PATH_LIBRARIES . '/members.class.php');
include_once(PATH_LIBRARIES . '/album.class.php');
include_once(PATH_LIBRARIES . '/print-tvc.class.php');
 include_once(PATH_LIBRARIES . '/programme.class.php');
//include_once(PATH_LIBRARIES . '/ebs.testing.class.php'); // Testing Enviroment For Payment
include_once(PATH_LIBRARIES . '/ebs.class.php');
include_once(PATH_LIBRARIES . '/whats.class.php');
include_once(PATH_LIBRARIES . '/gallery.class.php');
include_once(PATH_LIBRARIES . '/pressrelease.class.php');
include_once(PATH_LIBRARIES . '/source.class.php');
require_once(PATH_LIBRARIES . '/tcpdf/config/lang/eng.php');
require_once(PATH_LIBRARIES . '/tcpdf/tcpdf.php');
require_once(PATH_LIBRARIES . '/pdf.class.php');
require_once(PATH_LIBRARIES . '/reports.class.php');
require_once(PATH_LIBRARIES . '/print.class.php');
require_once(PATH_LIBRARIES . '/mobileapi.class.php');
require_once(PATH_LIBRARIES . '/message_service.class.php');
$objDB = new DBConnection($Config->host, $Config->user, $Config->password, $Config->db);
//redirect to maintenece
//$objBase->Redirect('maintenance.php');


/** Include PHPExcel */
//include_once('/libraries/programme.class.php');
require_once PATH_BASE.'/libraries/PHPExcel/Classes/PHPExcel.php';
require_once PATH_BASE.'/libraries/PHPExcel/gloableExcel.php';
//$objProgramme = new clsProgramme();
 $objProgramme->export_members_backUp();
?>