<?php
require_once('config.php');

class Modes{
	
	private $dbFunc = null;
	
	function __construct() {
		$this->dbFunc = new DBFunctions();
	}

	function __destruct() {
	}
	
	public function init(){
		$data = array();
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		$data[KEY_MESSAGE] = "Performing init operation";
		$data[KEY_SERVER_STATUS] = (V_SERVER_STATUS_CODE == V_ONLINE) ? R_STATUS_ONLINE : R_STATUS_OFFLINE;
		$platformCode = $_REQUEST[P_PLATFORM_CODE];
		
		if($platformCode == D_ANDROID){
			$data[KEY_MIN_VERSION] = V_MIN_ANDROID;
			$data[KEY_MAX_VERSION] = V_MAX_ANDROID;
		} else if ($platformCode == D_IOS) {
		        $data[KEY_MIN_VERSION] = V_MIN_IOS;
			$data[KEY_MAX_VERSION] = V_MAX_IOS;
		} else if ($platformCode == D_WEB) {
		        $data[KEY_MIN_VERSION] = V_MIN_WEB;
			$data[KEY_MAX_VERSION] = V_MAX_WEB;
		}
		
		$data[KEY_UPDATE_KEYS] = $this->dbFunc->getUpdateCodes();
		
		//new copied
		$data[KEY_CATEGORIES] = $this->dbFunc->getCategories();
		$data[KEY_UPDATE_KEYS] = $this->dbFunc->getUpdateCodes();
		$data[KEY_BASE_URL] = KEY_BASE_URL_OFFERS;
		//
		
		$data[KEY_PWD_HASH] = $password;
		$data[P_SESSION_KEY] = TEMP_SK;

		
		return $data;
	}
	
	public function login($email, $password){
		$data = array();
		if(!$userDetails = $this->dbFunc->getUserDetails($email, $password)){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_INVALID_CREDENTIALS;
				$data[KEY_ERROR_MESSAGE] = EM_INVALID_CREDENTIALS;
			}
			return $data;
		}
		$data[KEY_USER_DETAILS] = $userDetails;
		
		$data[KEY_CATEGORIES] = $this->dbFunc->getCategories();
		$data[KEY_UPDATE_KEYS] = $this->dbFunc->getUpdateCodes();
		$data[KEY_BASE_URL] = KEY_BASE_URL_OFFERS;
		$data[KEY_EXISTING_OFFER] = $this->dbFunc->getUserOffer($userDetails[COL_USER_ID]);
		$data[KEY_PWD_HASH] = $password;
		//$data[KEY_BASE_URL] = KEY_BASE_URL_LOGIN;
		$data[P_SESSION_KEY] = TEMP_SK;
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	}
	
	public function verifyEmail($email){
		$data = array();
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_INVALID_EMAIL_FORMAT;
				$data[KEY_ERROR_MESSAGE] = EM_INVALID_EMAIL_FORMAT;
			}
			return $data;
		}
		if($this->dbFunc->doesUserExist($email)){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			$data[KEY_ERROR_CODE] = E_USER_ALREADY_EXISTS;
			$data[KEY_ERROR_MESSAGE] = EM_USER_ALREADY_EXISTS;
		} else {
			$data[KEY_STATUS] = R_STATUS_SUCCESS;
			$data[KEY_MESSAGE] = "Email available to register";
		}
		return $data;
	}
	
	public function register($email, $password, $dob = null){
		$data = array();
		if($this->dbFunc->doesUserExist($email)){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_USER_ALREADY_EXISTS;
				$data[KEY_ERROR_MESSAGE] = EM_USER_ALREADY_EXISTS;
			}
			return $data;
		} else {
			$registerStatus = $this->dbFunc->register($email, $password, $dob);
			if($registerStatus){
				$data[KEY_STATUS] = R_STATUS_SUCCESS;
				$data[KEY_PWD_HASH] = $password;
			} else {
				$data[KEY_STATUS] = R_STATUS_FAILED;
				if(SETTING_ERROR){
					$data[KEY_ERROR_CODE] = E_ERROR_REGISTRING_USER;
					$data[KEY_ERROR_MESSAGE] = EM_ERROR_REGISTRING_USER;
				}
			}
		}
		return $data;
	}
	
	
	public function updateFacebookDetails($email, $fb_email, $dob, $facebook_id, $full_name){
		$data = array();
		 
			$updateStatus = $this->dbFunc->updateFacebookDetails($email, $fb_email, $dob, $facebook_id, $full_name);
			if($updateStatus){
				$data[KEY_STATUS] = R_STATUS_SUCCESS;

			} else {
				$data[KEY_STATUS] = R_STATUS_FAILED;
				if(SETTING_ERROR){
					$data[KEY_ERROR_CODE] = E_ERROR_UPDATING_DETAILS;
					$data[KEY_ERROR_MESSAGE] = EM_ERROR_UPDATING_DETAILS;
				}
			}
	
		return $data;
	}
	
	
	
	
	public function triggerForgotPassword($email){
		$data = array();
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_INVALID_EMAIL_FORMAT;
				$data[KEY_ERROR_MESSAGE] = EM_INVALID_EMAIL_FORMAT;
			}
		}else if(!$this->dbFunc->doesUserExist($email)) {
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_USER_DOES_NOT_EXIST;
				$data[KEY_ERROR_MESSAGE] = EM_USER_DOES_NOT_EXIST;
			}
		} else {
			$data[KEY_STATUS] = R_STATUS_SUCCESS;
		}
		/*
		$result = $this->db->forgotPassword($email);
		if($result == -1){
			$data[KEY_ERROR_CODE] = E_UNKNOWN;
			$data[KEY_ERROR_MESSAGE] = EM_UNKNOWN;
			} else if($result == 0){
			$data[KEY_ERROR_CODE] = E_INVALID_USER;
			$data[KEY_ERROR_MESSAGE] = EM_INVALID_USER;
		} else {
			$str = trim(file_get_contents('./php/email_templates/password_reset_template.html'));
			
			$reset_link = "http://192.169.244.18/mobiroster/change_password.php?code=".$return_result["reset_code"];
			$str = str_replace('{reset_link}', $reset_link, $str);
			$str = str_replace('{user_name}', $this->db->getEmployeeGlobalDetails($result["empid"], EMPLOYEE_FULL_NAME), $str);
			$data[KEY_EMAIL_STATUS] = $this->sendMail($email, "Hangout On 20 Preston - Password Reset", $str);
			$data[KEY_STATUS] = R_STATUS_SUCCESS;
		}
		*/
		return $data;
	}
	
	/*
	public function sendMail($to, $sub, $msg, $attachment = null){
		$mail = new PHPMailer(false); 
		$mail->IsSMTP(); 

		try {
			// $mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
			$mail->SMTPAuth   = true;                  // enable SMTP authentication
			$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
			$mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
			$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
			$mail->Username   = "paperclipinnovations@gmail.com";  // GMAIL username
			$mail->Password   = "Paperclip2014";            // GMAIL password
			
			$mail->AddAddress($to, "");
			//$mail->AddBCC("bsreeinf@gmail.com", "Sreenath");
			$mail->SetFrom('paperclipinnovations@gmail.com', 'MobiRoster - Paperclip Innovations');
			
			$mail->Subject = $sub;
			
			$mail->AddEmbeddedImage('./php/email_templates/images/icon.png', 'img_logo');
			if (strpos($msg,'{img_activate}') !== false)
				$mail->AddEmbeddedImage('./php/email_templates/images/activate.png', 'img_activate');
			if (strpos($msg,'{img_banner}') !== false)
				$mail->AddEmbeddedImage('./php/email_templates/images/wide.png', 'img_banner');
			$mail->AddEmbeddedImage('./php/email_templates/images/social_facebook.png', 'img_facebook');
			$mail->AddEmbeddedImage('./php/email_templates/images/social_instagram.png', 'img_instagram');
			$mail->AddEmbeddedImage('./php/email_templates/images/social_twitter.png', 'img_twitter');
			$mail->AddEmbeddedImage('./php/email_templates/images/social_gplus.png', 'img_gplus');
			if (strpos($msg,'{img_store_google}') !== false)
				$mail->AddEmbeddedImage('./php/email_templates/images/store_google.png', 'img_store_google');
			if (strpos($msg,'{img_store_apple}') !== false)
				$mail->AddEmbeddedImage('./php/email_templates/images/store_apple.png', 'img_store_apple');
				
			
			$msg = str_replace('{img_logo}', 'cid:img_logo', $msg);
			if (strpos($msg,'{img_activate}') !== false)
				$msg = str_replace('{img_activate}', 'cid:img_activate', $msg);
			if (strpos($msg,'{img_banner}') !== false)
				$msg = str_replace('{img_banner}', 'cid:img_banner', $msg);
			$msg = str_replace('{img_facebook}', 'cid:img_facebook', $msg);
			$msg = str_replace('{img_instagram}', 'cid:img_instagram', $msg);
			$msg = str_replace('{img_twitter}', 'cid:img_twitter', $msg);
			$msg = str_replace('{img_gplus}', 'cid:img_gplus', $msg);
			if (strpos($msg,'{img_store_google}') !== false)
				$msg = str_replace('{img_store_google}', 'cid:img_store_google', $msg);
			if (strpos($msg,'{img_store_apple}') !== false)
				$msg = str_replace('{img_store_apple}', 'cid:img_store_apple', $msg);
		
			$mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; 
			$mail->MsgHTML($msg);
			
			return $mail->Send();
		
		} catch (phpmailerException $e) {
			//echo $e->errorMessage(); //Pretty error messages from PHPMailer
		} catch (Exception $e) {
			//echo $e->getMessage(); //Boring error messages from anything else!
		}
	}
*/
	
	public function getMenu(){
		$data = array();
		$menu = array();
		$menuSections = $this->dbFunc->getMenuSections();
		if(!$menuSections){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_FETCHING_MENU;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_FETCHING_MENU;
			}
			return $data;
		}
		$ordered_keys = array();
		foreach($menuSections as $menuSectionId => $menuSectionName){
			$order_stub = array();
			$order_stub[COL_NAME] = $menuSectionName;
			$order_stub[COL_ID] = $menuSectionId;
			array_push($ordered_keys, $order_stub);
			$sectionData = $this->dbFunc->getMenuSectionData($menuSectionId);
			$menu[$menuSectionName][COL_ID] = $menuSectionId;
			if(!$sectionData){
				$menu[$menuSectionName][KEY_ITEMS] = array();
			} else {
				$menu[$menuSectionName][KEY_ITEMS] = $sectionData;
			}
		}
		$data[KEY_MENU] = $menu;
		$data["order"] = $ordered_keys;
		$data[KEY_BASE_URL] = KEY_BASE_URL_MENU;
		$data[KEY_UPDATE_KEY] = $this->dbFunc->getUpdateCodes(CATEGORY_MENU);
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		return $data;
	}
	
	public function getAboutDetails(){
		$data = array();
		$about = $this->dbFunc->getAboutDetails();
		if(!$about){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_FETCHING_ABOUT;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_FETCHING_ABOUT;
			}
			return $data;
		}
		$data[KEY_ABOUT] = $about;
		$data[KEY_UPDATE_KEY] = $this->dbFunc->getUpdateCodes(CATEGORY_ABOUT);
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		return $data;
	}
	
	public function getEvents($param = null){
		$data = array();
		$events = array();
		$events = $this->dbFunc->getEvents($param);
		if(!$events){
			try{
				if(sizeof($events) == 0){
					$data[KEY_EVENTS] = array();
					$data[KEY_STATUS] = R_STATUS_SUCCESS;
					return $data;
				}
			} catch(Exception $e) {}
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_FETCHING_EVENTS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_FETCHING_EVENTS;
			}
			return $data;
		}
		
                $data[KEY_EVENTS] = $events;
                $data[KEY_BASE_URL] = KEY_BASE_URL_EVENTS;
                $data[KEY_UPDATE_KEY] = $this->dbFunc->getUpdateCodes(CATEGORY_EVENTS);
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		return $data;
	
	}
	
	public function getSpecial(){
		$data = array();
		$special = array();
		$specialSections = $this->dbFunc->getSpecialSections();
		if(!$specialSections){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_FETCHING_SPECIAL;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_FETCHING_SPECIAL;
			}
			return $data;
		}
		foreach($specialSections as $specialSectionId => $specialSectionName){
			$sectionData = $this->dbFunc->getSpecialData($specialSectionId);
			$special[$specialSectionName][COL_ID] = $specialSectionId;
			if(!$sectionData){
				$special[$specialSectionName][KEY_ITEMS] = array();
			} else {
				$special[$specialSectionName][KEY_ITEMS] = $sectionData;
			}
		}
		$data[KEY_SPECIAL] = $special;
		$data[KEY_UPDATE_KEY] = $this->dbFunc->getUpdateCodes(CATEGORY_SPECIALS);
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		return $data;
	}
	
	public function getGallery(){
		$data = array();
		/*
		$gallery_filenames = scandir(URL_GALLERY);
		unset($gallery_filenames[0]);
		unset($gallery_filenames[1]);
		$urls = array();
		foreach($gallery_filenames as $id=>$img_filepath) {
			array_push($urls, $img_filepath);
		}
		if(!$urls){
			try{
				if(sizeof($urls) == 0){
					$data[KEY_URLS] = array();
					$data[KEY_STATUS] = R_STATUS_SUCCESS;
					return $data;
				}
			} catch(Exception $e) {}
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_FETCHING_GALLERY;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_FETCHING_GALLERY;
			}
			return $data;
		}
		$data[KEY_URLS] = $urls;
		*/
		$gallery_data = $this->dbFunc->getGallery();
		if(!$gallery_data){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_FETCHING_GALLERY;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_FETCHING_GALLERY;
			}
			return $data;
		}
		$data[KEY_DATA] = $gallery_data;
		$data[KEY_BASE_URL] = KEY_BASE_URL_GALLERY;
		$data[KEY_UPDATE_KEY] = $this->dbFunc->getUpdateCodes(CATEGORY_GALLERY);
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		return $data;
	}
	
	public function isOfferAssignedToUser($userId, $offerId){
		return $this->dbFunc->isOfferAssignedToUser($userId, $offerId);
	}
	
	public function setOffer($userId, $offerId){
		$data = array();
		$existingOffer = null;
	if($existingOffer = $this->dbFunc->isOfferAssignedToUser($userId, $offerId)) {
	//isOfferAssignedToUser($userId, $offerId)) {
	//getUserOffer($userId)) {
	
			$data[KEY_STATUS] = R_STATUS_FAILED;
			$data[KEY_EXISTING_OFFER] = $this->dbFunc->getUserOffer($userId);
			//echo '1';
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_OFFER_ALREADY_SET;
				$data[KEY_ERROR_MESSAGE] = EM_OFFER_ALREADY_SET;     
			}
			//echo "exists";
			$data[KEY_BASE_URL] = KEY_BASE_URL_OFFERS;
			return $data;
		} 
//echo '2';
	        if(!$this->dbFunc->setOffer($userId,$offerId)){
	                   $data[KEY_STATUS] = R_STATUS_FAILED;
	                   if(SETTING_ERROR){
	                      $data[KEY_ERROR_CODE] = E_ERROR_SETTING_OFFERS;
			      $data[KEY_ERROR_MESSAGE] = EM_ERROR_SETTING_OFFERS;     
	                   }
	                   //echo '3';
	                   return $data;
	        }
		//$data[KEY_STATUS] = R_STATUS_SUCCESS;
		$data[KEY_BASE_URL] = KEY_BASE_URL_OFFERS;
		$data[KEY_OFFER_SET] = $this->dbFunc->getUserOffer($userId);
		
		if($data[KEY_OFFER_SET]==true)
		{ 
		  $data[KEY_STATUS] = R_STATUS_SUCCESS;
		  }
		  else{
		 $data[KEY_STATUS] = R_STATUS_FAILED;
		 $data[KEY_ERROR_CODE] = E_ERROR_SETTING_OFFERS;
	         $data[KEY_ERROR_MESSAGE] = EM_ERROR_SETTING_OFFERS; 
		 }
		return $data;
	}
	
	public function getOffers($userId){
		$data = array();
		
		$offers = $this->dbFunc->getOffers($userId);
		if(!$offers){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_NO_OFFERS_AVAILABLE;
				$data[KEY_ERROR_MESSAGE] = EM_NO_OFFERS_AVAILABLE;
			}
			return $data;
		}
		
		$data[KEY_OFFERS] = $offers;
		$data[KEY_UPDATE_KEY] = $this->dbFunc->getUpdateCodes(CATEGORY_OFFERS);
		$data[KEY_BASE_URL] = KEY_BASE_URL_OFFERS;
		//if($userId != CONS_GUEST_USER && $userId != '')
			$data[KEY_EXISTING_OFFER] = $this->dbFunc->getUserOffer($userId);
		
		if($data[KEY_EXISTING_OFFER] != null) {
			$data[KEY_STATUS] = R_STATUS_FAILED;
			$data[KEY_ERROR_CODE] = E_OFFER_ALREADY_SET;
			$data[KEY_ERROR_MESSAGE] = EM_OFFER_ALREADY_SET;
		} else {
			$data[KEY_STATUS] = R_STATUS_SUCCESS;
		}
		return $data;
	}
	
	// http://192.169.244.18/hangouts/index.php
	
	
	public function redeemOffer($activeOfferId){
	        $data = array();
	        $redeemOffer = $this->dbFunc->redeemOffer($activeOfferId);
		if($redeemOffer==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_REDEEMING_OFFER;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_REDEEMING_OFFER;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	
	
	}
	
public function getAllOffers(){
		$data = array();
		
		$offers = $this->dbFunc->getAllOffers();
		if(!$offers){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_NO_OFFERS_AVAILABLE;
				$data[KEY_ERROR_MESSAGE] = EM_NO_OFFERS_AVAILABLE;
			}
			return $data;
		}
		
		$data[KEY_OFFERS] = $offers;
		$data[KEY_UPDATE_KEY] = $this->dbFunc->getUpdateCodes(CATEGORY_OFFERS);
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		return $data;
	}
	

public function getAllEvents(){
		$data = array();
		
		$events = $this->dbFunc->getAllEvents();
		if(!$events){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_NO_EVENTS_AVAILABLE;
				$data[KEY_ERROR_MESSAGE] = EM_NO_EVENTS_AVAILABLE;
			}
			return $data;
		}
		
		$data[KEY_EVENTS] = $events;
		$data[KEY_UPDATE_KEY] = $this->dbFunc->getUpdateCodes(CATEGORY_EVENTS);
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		return $data;
	}			
	
	
}

?>