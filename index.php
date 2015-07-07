<?php
require('./php/config.php');
require('./php/db_connect.php');
require('./php/commons.php');
require('./php/db_functions.php');
require('./php/modes.php');
require_once('../swiftmailer-master/lib/swift_required.php');
require_once('../PHPMailer/class.phpmailer.php');
require_once('../PHPExcel/PHPExcel.php');

$modes = new Modes();
$db = new DBFunctions();
$commons = new Commons();
// set to UTC


$db->setMYSQLTimeZone('+05:30') or die('timezone set error');
date_default_timezone_set('Asia/Calcutta') or die('timezone error');


// no echo / print_r / var_dump
$response = array();
$response[KEY_STATUS] = R_STATUS_FAILED;
if(SETTING_ERROR){
	$response[KEY_ERROR_CODE] = E_UNKNOWN;
	$response[KEY_ERROR_MESSAGE] = EM_UNKNOWN;
}

if( !isset($_REQUEST[KEY_MODE]) ){
	$response[KEY_STATUS] = R_STATUS_FAILED;
	if(SETTING_ERROR){
		$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
		$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
	}
	echo json_encode($response);
	exit;
}

foreach($_REQUEST as $key=>$value){
	$_REQUEST[$key] = $commons->clean($value);
}

switch( $_REQUEST[KEY_MODE] ){
case M_INIT:
	if(!isset($_REQUEST[P_PLATFORM_CODE]))
		break;
	$response = $modes->init();
	 
	// if email and password
	
	// functionality here
	
	break;
/*case M_LOGIN:
	// if email and pwd are sent, continue, else exit
	if(isset($_REQUEST[P_EMAIL]) && isset($_REQUEST[P_PASSWORD]) && isset($_REQUEST[P_HAS_EXISTING_SESSION])){
		$email = strtolower($_REQUEST[P_EMAIL]);
		$hasExistingSession = strtolower ($_REQUEST[P_HAS_EXISTING_SESSION]);
		if($hasExistingSession == "true" || $hasExistingSession == "yes" || $hasExistingSession == "1") {
			$password = $_REQUEST[P_PASSWORD];
		} else if($hasExistingSession == "false" || $hasExistingSession == "no" || $hasExistingSession == "0")  {
			$password = md5($_REQUEST[P_PASSWORD]);
			//echo $password;
		}
		$loginData= $modes->login($email, $password);
		if(isset($_REQUEST[P_ADMIN]) && $loginData[KEY_USER_DETAILS][COL_USER_TYPE] != USR_ADMIN){
			$response[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$response[KEY_ERROR_CODE] = E_ACCESS_DENIED;
				$response[KEY_ERROR_MESSAGE] = EM_ACCESS_DENIED;
			}
		} else {
			$response = $loginData;
		}
	} else {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
	break;
*/

case M_LOGIN: // new
    // if email and pwd are sent, continue, else exit
    if(isset($_REQUEST[P_EMAIL]) && isset($_REQUEST[P_PASSWORD]) && isset($_REQUEST[P_HAS_EXISTING_SESSION])){
        $email = strtolower($_REQUEST[P_EMAIL]);
        $hasExistingSession = strtolower ($_REQUEST[P_HAS_EXISTING_SESSION]);
        if($hasExistingSession == "true" || $hasExistingSession == "yes" || $hasExistingSession == "1") {
            $password = $_REQUEST[P_PASSWORD];
        } else if($hasExistingSession == "false" || $hasExistingSession == "no" || $hasExistingSession == "0")  {
            $password = md5($_REQUEST[P_PASSWORD]);
            //echo $password;
        }
        $loginData= $modes->login($email, $password);
        $response = $loginData;
    } else {
        $response[KEY_STATUS] = R_STATUS_FAILED;
        if(SETTING_ERROR){
            $response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
            $response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
        }
    }
    break;
	
case M_VERIFY_EMAIL:
	if(isset($_REQUEST[P_EMAIL])){
		$email = strtolower($_REQUEST[P_EMAIL]);
		$response = $modes->verifyEmail($email);
	} else {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
	break;

case M_REGISTER:
	//require('./php/session_test.php');
	if(isset($_REQUEST[P_EMAIL]) && isset($_REQUEST[P_PASSWORD])){
		$email = strtolower($_REQUEST[P_EMAIL]);
		$password = md5($_REQUEST[P_PASSWORD]);
		//$name = $_REQUEST[P_NAME];
		$dob = null;
		//$email = null;
		if(isset($_REQUEST[P_DOB])){
			$test_date = $_REQUEST[P_DOB];
			$test_arr  = explode('/', $test_date);
			if (count($test_arr) == 3) {
				if (checkdate($test_arr[0], $test_arr[1], $test_arr[2])) {
					$dob = $_REQUEST[P_DOB];
				} else {
					// problem with dates ...
				}
			} else {
				// problem with input ...
			}
		}
		$response = $modes->register($email, $password, $dob);
	} else {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
	break;
	
case M_UPDATE_FACEBOOK_DETAILS:
	require('./php/session_test.php');
	if(isset($_REQUEST[P_EMAIL]) && isset($_REQUEST[P_FB_EMAIL]) && isset($_REQUEST[P_DOB])&& isset($_REQUEST[P_FB_ID])){//&& isset($_REQUEST[P_FULL_NAME])){
		$email = strtolower($_REQUEST[P_EMAIL]);
		$fb_email = strtolower($_REQUEST[P_FB_EMAIL]);
		$facebook_id = $_REQUEST[P_FB_ID];
		$full_name = $_REQUEST[P_FULL_NAME];
		$dob = $_REQUEST[P_DOB];
	       
		$response = $modes->updateFacebookDetails($email, $fb_email, $dob, $facebook_id, $full_name);
	} else {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
	break;
	

case M_FORGOT_PASSWORD: 
	if(isset($_REQUEST[P_EMAIL])) {
	        $email = strtolower($_REQUEST[P_EMAIL]); 
		$response = $modes->triggerForgotPassword($_REQUEST[P_EMAIL]);
	} else {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;
case M_GET_MENU: 
	//require('./php/session_test.php');
	$response = $modes->getMenu();
        break;
        
case M_GET_ABOUT_DETAILS:
	//require('./php/session_test.php');
	$response = $modes->getAboutDetails();
	break;
	
case M_GET_EVENTS:
       // require('./php/session_test.php');
       $param = null;
       if(isset($_REQUEST[P_PARAM])){
       	$param = $_REQUEST[P_PARAM];
       }
        $response = $modes->getEvents($param);
        break;
        
case M_GET_SPECIAL:
       // require('./php/session_test.php');
        $response = $modes->getSpecial();
        break;      
        
case M_GET_OFFERS:
        //require('./php/session_test.php');
        if(isset($_REQUEST[COL_USER_ID])){    
		$userId = $_REQUEST[COL_USER_ID]; 
		$response = $modes->getOffers($userId);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;  
        
case M_GET_GALLERY:
       // require('./php/session_test.php');
        $response = $modes->getGallery();
        break;          
        
case M_SET_OFFER:
        require('./php/session_test.php');
        if(isset($_REQUEST[COL_USER_ID]) && isset($_REQUEST[COL_OFFER_ID])){    
		$userId = $_REQUEST[COL_USER_ID]; 
		$offerId = $_REQUEST[COL_OFFER_ID];
		$response = $modes->setOffer($userId, $offerId);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;  
        
        
case M_REDEEM_OFFER:
        require('./php/session_test.php');
        if(isset($_REQUEST[P_ACTIVE_OFFER_ID])){    
		$activeOfferId = $_REQUEST[P_ACTIVE_OFFER_ID]; 
	        $response = $modes->redeemOffer($activeOfferId);
         } else {
		
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_ACTIVE_OFFER_ID_NOT_PROVIDED;
			$response[KEY_ERROR_MESSAGE] = EM_ACTIVE_OFFER_ID_NOT_PROVIDED;
		}
	}
        break;          
        
case M_GET_ALL_OFFERS:
        require('./php/session_test.php');
        $response = $modes->getAllOffers();
        break;          
        

case M_GET_ALL_EVENTS:
        require('./php/session_test.php');
        $response = $modes->getAllEvents();
        break;           
            

case M_TEST:
	$response["php_time"] = date('Y-m-d H:i:s');
	$response["mysql_time"] = $db->getMYSQLTime();
	break;

default:
	$response[KEY_STATUS] = R_STATUS_FAILED;
	if(SETTING_ERROR){
		$response[KEY_ERROR_CODE] = E_UNKNOWN_SERVICE_REQUEST;
		$response[KEY_ERROR_MESSAGE] = EM_UNKNOWN_SERVICE_REQUEST;
	}
	
	
	break;
}


echo json_encode($response);

/*
function isValidSessionKeyProvided(){
	if(!isset($_REQUEST[P_SESSION_KEY])){
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_NO_SESSION_KEY;
			$response[KEY_ERROR_MESSAGE] = EM_NO_SESSION_KEY;
		}
		return false;
	}
	if($_REQUEST[P_SESSION_KEY] != TEMP_SK){
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INVALID_SESSION_KEY;
			$response[KEY_ERROR_MESSAGE] = EM_INVALID_SESSION_KEY;
		}
		return false;
	}
	return true;
}
*/

?>