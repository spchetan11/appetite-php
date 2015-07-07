<?php
require_once('./php/config.php');
require_once('./php/admin_config.php');
require_once('./php/db_connect.php');
require_once('./php/commons.php');
require_once('./php/db_functions.php');
require_once('./php/modes.php');
require_once('./php/admin_modes.php');
require_once('./php/admin_db_functions.php');

$modes = new Modes();
$db = new DBFunctions();
$admin_modes = new AdminModes();
$admin_db = new AdminDBFunctions();
$commons = new Commons();
// set to UTC


$db->setMYSQLTimeZone('+08:00');
date_default_timezone_set('Australia/Perth') or die('timezone error');


// no echo / print_r / var_dump
$response = array();
$response[KEY_STATUS] = R_STATUS_FAILED;
if(SETTING_ERROR){
	$response[KEY_ERROR_CODE] = E_UNKNOWN;
	$response[KEY_ERROR_MESSAGE] = EM_UNKNOWN;
}

foreach($_REQUEST as $key=>$value){
	$_REQUEST[$key] = $commons->clean($value);
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

switch( $_REQUEST[KEY_MODE] ){
case M_INIT:
	if(!isset($_REQUEST[P_PLATFORM_CODE]))
		break;
	$response = $modes->init();
	 
	// if email and password
	
	// functionality here
	
	break;
case M_LOGIN:
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
		if($loginData[KEY_USER_DETAILS][COL_USER_TYPE] != USR_ADMIN){
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


 	


case M_ADD_MENU_SECTIONS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        
		
		$response = $admin_modes->addMenuSections($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;  
        
case M_EDIT_MENU_SECTIONS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	       
		$response = $admin_modes->editMenuSections($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;  
        
case M_DELETE_MENU_SECTIONS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	  
		$response = $admin_modes->deleteMenuSections($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;     
        
        

case M_ADD_MENU_ITEMS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        
	    //    var_dump($data);
		$response = $admin_modes->addMenuItems($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;  
        
case M_EDIT_MENU_ITEMS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	      
		
		$response = $admin_modes->editMenuItems($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;          
                     
        

case M_DELETE_MENU_ITEMS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        
	
		$response = $admin_modes->deleteMenuItems($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;      
        


//------------------


case M_ADD_SPECIAL_SECTIONS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	       
		$response = $admin_modes->addSpecialSections($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;  
        
case M_EDIT_SPECIAL_SECTIONS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	    
		$response = $admin_modes->editSpecialSections($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;  
        
case M_DELETE_SPECIAL_SECTIONS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        
		
		$response = $admin_modes->deleteSpecialSections($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;     
        
        

case M_ADD_SPECIAL_ITEMS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        
	    //    var_dump($data);
		$response = $admin_modes->addSpecialItems($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;  
        
case M_EDIT_SPECIAL_ITEMS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	    
		$response = $admin_modes->editSpecialItems($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;          
                     
        

case M_DELETE_SPECIAL_ITEMS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        
		
		$response = $admin_modes->deleteSpecialItems($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;   
        
        
        
//---------------



case M_ADD_EVENTS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        
	    //    var_dump($data);
		$response = $admin_modes->addEvents($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;  
        
case M_EDIT_EVENTS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	       
		$response = $admin_modes->editEvents($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;          
                     
        

case M_DELETE_EVENTS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	       
		$response = $admin_modes->deleteEvents($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;   
 
 case M_DELETE_EVENT_IMAGES_EXTRAS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	       
		$response = $admin_modes->deleteEventImagesExtras($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;         
//--------------


case M_ADD_OFFERS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        
	    //    var_dump($data);
		$response = $admin_modes->addOffers($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;  
        
case M_EDIT_OFFERS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	       
		
		$response = $admin_modes->editOffers($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;          
                     
        

case M_DELETE_OFFERS: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        
		
		$response = $admin_modes->deleteOffers($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;      
        
//-----------



case M_EDIT_ABOUT: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);  
		$response = $admin_modes->editAbout($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;           
        
        
        
//-----------


case M_ADD_GALLERY_IMAGES: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){  
		//print_r($_REQUEST);  
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        if(!$data){$response=array(); $response['status']="Invalid json";}else
	        //var_dump($data);
		$response = $admin_modes->addGalleryImages($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;  
        
case M_EDIT_GALLERY_IMAGES: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        
		
		$response = $admin_modes->editGalleryImages($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;          
                     
case M_DELETE_GALLERY_IMAGES: 
	require('./php/session_test.php');
	
	if(isset($_REQUEST[DATA]) ){    
	        $data = json_decode(stripslashes($_REQUEST[DATA]), true);   
	        
		$response = $admin_modes->deleteGalleryImages($data);
        } else  {
		$response[KEY_STATUS] = R_STATUS_FAILED;
		if(SETTING_ERROR){
			$response[KEY_ERROR_CODE] = E_INSUFFICIENT_PARAMS;
			$response[KEY_ERROR_MESSAGE] = EM_INSUFFICIENT_PARAMS;
		}
	}
        break;                    

           
        














case M_GET_ALL_OFFERS:
        require('./php/session_test.php');
        $response = $modes->getAllOffers();
        break; 
        

         
            



default:
	$response[KEY_STATUS] = R_STATUS_FAILED;
	if(SETTING_ERROR){
		$response[KEY_ERROR_CODE] = E_UNKNOWN_SERVICE_REQUEST;
		$response[KEY_ERROR_MESSAGE] = EM_UNKNOWN_SERVICE_REQUEST;
	}
	print_r($_FILES);
	print_r(pathinfo($_FILES['asdf']['name']));
	
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