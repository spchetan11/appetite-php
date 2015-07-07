<?php
require_once('db_connect.php');
require_once('config.php');
include_once('./php/commons.php');


class DBFunctions {

	private $dbConnect;
	private $mysqli;
	private $commons;
	
	function __construct() {
		
		$this->dbConnect = new DB_Connect();
		$this->mysqli = $this->dbConnect->connect();
		$this->commons = new Commons();
	}

	function __destruct() {
		$this->dbConnect->disconnect();
	}
	
	
	public function setMYSQLTimeZone($timeZone){
		return $this->mysqli->query("SET time_zone = '$timeZone'");
	}
	
	public function getMYSQLTime(){
		$query = "
			SELECT NOW()";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			if($result->num_rows == 1){
				$row = $result-> fetch_array();
				return strtolower($row[0]);
			}
		}
		return false;
	}
	
	
	public function getCategoryName($categoryId){
		$query = "
			SELECT * FROM ".TAB_CATEGORIES." 
			WHERE ".COL_ID." = $categoryId
			LIMIT 1";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			if($result->num_rows == 1){
				$row = $result-> fetch_assoc();
				return strtolower($row[COL_NAME]);
			}
		}
		return false;
	}
	
	public function getCategories($categoryName = null){
		if($categoryName == null) {
			$query = "
				SELECT * FROM ".TAB_CATEGORIES;
		} else {
			$query = "
				SELECT * FROM ".TAB_CATEGORIES."
				WHERE ".COL_NAME." LIKE '$categoryName' 
				LIMIT 1";
		}
		//echo $query;
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$return_result = array();
			while($row = $result-> fetch_assoc()) {
				if($categoryName != null) {
					return $row[COL_ID];
				}
				$return_result[$row[COL_ID]] = strtolower($row[COL_NAME]);
			}
			return $return_result;
		}
		return false;
	}
	
	public function getUpdateCodes($categoryName = null){
		if($categoryName == null) {
			$query = "
				SELECT * FROM ".TAB_UPDATE_KEYS;
		} else {
			$query = "
				SELECT * FROM ".TAB_UPDATE_KEYS."
				WHERE ".COL_CATEGORY_ID." = ".$this->getCategories($categoryName)."
				LIMIT 1";
		}
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$return_result = array();
			while($row = $result->fetch_assoc()) {
				if($categoryName != null) {
					return $row[COL_UPDATE_ID];
				}
				$return_result[$row[COL_CATEGORY_ID]] = strtolower($row[COL_UPDATE_ID]);
			}
			return $return_result;
		}
		return false;
	}
	
	public function doesUserExist($email){
		$query = "
			SELECT * FROM ".TAB_USERS." 
			WHERE 	".COL_EMAIL." = '$email'
			LIMIT 1";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			if($result->num_rows == 1){
				return true;
			}
		}else{
			return true;
		}
		return false;
	}
	
	/*public function getUserDetails($email, $password){
		$query = "
			SELECT * FROM ".TAB_USERS." 
			WHERE 	".COL_EMAIL." = '$email' AND
				".COL_PASSWORD." = '$password'
			LIMIT 1";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$return_result = array();
			if($result->num_rows == 1){
				$row = $result-> fetch_assoc();
				$return_result[COL_USER_ID] = $row[COL_USER_ID];
				//$return_result[COL_NAME] = $row[COL_NAME];
				$return_result[COL_EMAIL] = $row[COL_EMAIL];
				$return_result[COL_DOB] = $row[COL_DOB];
				$return_result[KEY_PWD_HASH] = $password;
				
				/*if(!file_exists(URL_LOGIN."/".$row[COL_IMAGE])){
					continue;
				}
				
				$return_result[COL_IMAGE] = $row[COL_IMAGE];
				
				return $return_result;
			}
		}
		return false;
	}
	*/
	
 	public function getUserDetails($email, $password){
        $query = "
            SELECT * FROM ".TAB_USERS." 
            WHERE     ".COL_EMAIL." = '$email' AND
                ".COL_PASSWORD." = '$password'
            LIMIT 1";
        $result = $this->mysqli->query($query);
        if(!$this->mysqli->connect_errno){
            $return_result = array();
            if($result->num_rows == 1){
                $row = $result-> fetch_assoc();
                $return_result[COL_USER_ID] = $row[COL_USER_ID];
                $return_result[COL_FULL_NAME] = $row[COL_FULL_NAME];    // new
                $return_result[COL_USER_TYPE] = $row[COL_USER_TYPE];    // new
                $return_result[COL_EMAIL] = $row[COL_EMAIL];
                $return_result[COL_DOB] = $row[COL_DOB];
                $return_result[KEY_PWD_HASH] = $password;
                
                /*if(!file_exists(URL_LOGIN."/".$row[COL_IMAGE])){
                    continue;
                }
                
                $return_result[COL_IMAGE] = $row[COL_IMAGE];*/
                
                return $return_result;
            }
        }
        return false;
    }	
	
	function generateNewUserId(){
		$commons = new Commons();
		$randomString = $commons->generateRandomString(10);
		$query = "
			SELECT * FROM ".TAB_USERS." 
			WHERE 	".COL_USER_ID." = '$randomString' ";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			if($result->num_rows == 1){
				return $this->generateNewUserId();
			} else {
				return $randomString;
			}
		}
		return false;
	}
	
	
	
	/*public function updateFacebookDetails($email, $fb_email, $email, $dob, $facebook_id, $full_name){
		$query = "SELECT * FROM ".TAB_USERS." WHERE ".COL_EMAIL." = '$email'";
		$result = $this->mysqli->query($query);
		if($result){
			if($result->num_rows == 1){
				$query = "
					UPDATE ".TAB_USERS." 
					SET ".
						COL_FB_EMAIL." = '$fb_email', ".
						COL_DOB." = '$dob', ".
						COL_FB_ID." = '$facebook_id', ".
						COL_FULL_NAME." = '$full_name'
					WHERE ".
						COL_EMAIL." = '$email'";
				echo $query;
			}
		        	$result = $this->mysqli->query($query);
		}else {
			return false;
		} 
		
		if($result){
			return true;
		}
	        
		return false;
	}*/
	
	public function updateFacebookDetails($email, $fb_email, $dob, $facebook_id, $full_name){
		$query = "
			UPDATE ".TAB_USERS." 
			SET ".
				COL_FB_EMAIL." = '$fb_email', ".
				COL_DOB." = '$dob', ".
				COL_FB_ID." = '$facebook_id', ".
				COL_FULL_NAME." = '$full_name'
			WHERE ".
				COL_EMAIL." = '$email'";
		//echo $query;
		$result = $this->mysqli->query($query);
		return $result;
	}
	
	
	public function register($email, $password, $dob = null){
		$newUserid = $this->generateNewUserId();
		if(!$newUserid)
			return false;
		$query = "
			INSERT INTO ".TAB_USERS." 
			(".COL_USER_ID.",".COL_EMAIL.",".COL_PASSWORD.",".COL_DOB.")
			VALUES
			('$newUserid','$email','$password',".($dob==null?"null":"'$dob'").")";

		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			return true;
		}
		return false;
	}
	
	/*
	
	function generateNewPasswordResetCode(){
		$commons = new Commons();
		$randomString = $commons->generateRandomString(20);
		$query = "
			SELECT * FROM ".TAB_PASSWORD_RESET." 
			WHERE 	".COL_RESET_CODE." = '$randomString' ";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			if($result->num_rows == 1){
				return $this->generateNewUserId();
			} else {
				return $randomString;
			}
		}
		return false;
	}
	
	*/
	
/*	public function forgotPassword($email){
		$query = mysql_query("SELECT * FROM ".TAB_USERS." WHERE ".COL_EMAIL." ='$email'");
		
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$row = $result->fetch_assoc()
			if($result->num_rows == 0)
			  return 0;
			if($return_result->num_rows>1)  
			  return -1;
		
		while($row = $result->fetch_assoc()){
		$return_result=array();
		$return_result[COL_NAME] = $row[COL_NAME];
		$return_result[COL_USER_ID] = $row[COL_USER_ID];
		$query = mysql_query("DELETE FROM ".TAB_PASSWORD_RESET." WHERE ".COL_USER_ID." = '$user_id' ");
		$result = $this->mysqli->query($query);
		//$reset_code = $this->generateRandomString(FOR_PASSWORD_RESET);
		$reset_code = $this->generateNewPasswordResetCode();
		$query = mysql_query("INSERT INTO ".TAB_PASSWORD_RESET." (".COL_USER_ID.", ".COL_RESET_CODE.") VALUES ('$user_id', '$reset_code')");
		$result = $this->mysqli->query($query);
		$return_result["reset_code"] = $reset_code;
		return $return_result;
		}
	}
	
	*/
	
	
	public function getMenuSections(){
		$query = "SELECT * FROM ".TAB_MENU_SECTIONS." WHERE ".COL_IS_DELETED." IS NULL";
		$result = $this->mysqli->query($query);
		//if(!$this->mysqli->connect_errno){
		if($result){
			$return_result = array();
			while($row = $result->fetch_assoc()) {
				$return_result[$row[COL_ID]] = $row[COL_NAME];
			}
			return $return_result;
		}
		return false;
	}
	
	public function getMenuSectionData($menuSectionId){
		$query = "
			SELECT * FROM ".TAB_MENU_DATA."
			WHERE ".COL_SECTION_ID." = $menuSectionId AND ".COL_IS_DELETED." IS NULL
			";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$return_result = array();
			while($row = $result->fetch_assoc()) {
				$menuItem = array();
				$menuItem[COL_ID] = $row[COL_ID];
				$menuItem[COL_TITLE] = $row[COL_TITLE];
				$menuItem[COL_DESCRIPTION] = $row[COL_DESCRIPTION];
				$menuItem[COL_PRICE] = $row[COL_PRICE];
				$menuItem[COL_THUMBNAIL] = null;//$row[COL_THUMBNAIL_IMAGE];
				
				
				if(!file_exists(URL_MENU."/".$row[COL_THUMBNAIL_IMAGE])){
					continue;
				}
				
				$menuItem[COL_THUMBNAIL_IMAGE] = $row[COL_THUMBNAIL_IMAGE];
				array_push($return_result, $menuItem);
			}
			return $return_result;
		}
		return false;
	}
	
	public function getAboutDetails(){
		$query = "SELECT * FROM ".TAB_ABOUT." LIMIT 1";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$return_result = array();
			$row = $result->fetch_assoc();
			$return_result[COL_COMPANY_NAME] = $row[COL_COMPANY_NAME];
			$return_result[COL_EMAIL] = $row[COL_EMAIL];
			$return_result[COL_PHONE] = $row[COL_PHONE];
			$return_result[COL_DESCRIPTION] = $row[COL_DESCRIPTION];
			$return_result[COL_FB_LINK] = $row[COL_FB_LINK];
			
			$location = array();
			$location[COL_LATITUDE] = $row[COL_LATITUDE];
			$location[COL_LONGITUDE] = $row[COL_LONGITUDE];
			$return_result[KEY_LOCATION] = $location;
			
			$return_result[COL_PLACE_TAGS] = explode(",", $row[COL_PLACE_TAGS]);
			$return_result[COL_ADDRESS] = $row[COL_ADDRESS];
			
			$timings = array();
			$timeFrom = array();
			$timeTo = array();
			$timeFrom[COL_TIMING_SUN_FROM] = $row[COL_TIMING_SUN_FROM];
			$timeFrom[COL_TIMING_MON_FROM] = $row[COL_TIMING_MON_FROM];
			$timeFrom[COL_TIMING_TUE_FROM] = $row[COL_TIMING_TUE_FROM];
			$timeFrom[COL_TIMING_WED_FROM] = $row[COL_TIMING_WED_FROM];
			$timeFrom[COL_TIMING_THU_FROM] = $row[COL_TIMING_THU_FROM];
			$timeFrom[COL_TIMING_FRI_FROM] = $row[COL_TIMING_FRI_FROM];
			$timeFrom[COL_TIMING_SAT_FROM] = $row[COL_TIMING_SAT_FROM];
			$timeTo[COL_TIMING_SUN_TO] = $row[COL_TIMING_SUN_TO];
			$timeTo[COL_TIMING_MON_TO] = $row[COL_TIMING_MON_TO];
			$timeTo[COL_TIMING_TUE_TO] = $row[COL_TIMING_TUE_TO];
			$timeTo[COL_TIMING_WED_TO] = $row[COL_TIMING_WED_TO];
			$timeTo[COL_TIMING_THU_TO] = $row[COL_TIMING_THU_TO];
			$timeTo[COL_TIMING_FRI_TO] = $row[COL_TIMING_FRI_TO];
			$timeTo[COL_TIMING_SAT_TO] = $row[COL_TIMING_SAT_TO];
			
			$timings[KEY_FROM] = $timeFrom;
			$timings[KEY_TO] = $timeTo;
			$return_result[KEY_TIMINGS] = $timings;
			return $return_result;
		}
		return false;
	}
	
	public function getEvents($param){
		$sendAll = $param=="all";
		$query = "SELECT * FROM ".TAB_EVENTS.
			" WHERE ".
				($sendAll? " " : " (now() BETWEEN ".COL_ADDED_ON." AND ".COL_EXPIRES_ON.") AND ")
				.COL_IS_DELETED." IS NULL";
		//echo $query;
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$return_result = array();
			while($row = $result->fetch_assoc()) {
				$event = array();
				if(!file_exists(URL_EVENTS."/".$row[COL_IMAGE])){
					continue;
				}
				$event[COL_ID] = $row[COL_ID];
				$event[COL_TITLE] = $row[COL_TITLE];
				//$event[COL_DESCRIPTION] = $row[COL_DESCRIPTION];
				$event[COL_TAG] = $row[COL_TAG];
				//$event[COL_THUMBNAIL] = $row[COL_THUMBNAIL];
				$event[COL_IMAGE] = $row[COL_IMAGE];
				$event[COL_ADDED_ON] = $row[COL_ADDED_ON];
				$event[COL_STARTS_AT] = $row[COL_STARTS_AT];
				$event[COL_EXPIRES_ON] = $row[COL_ADDED_ON];
				
				// to be changed. temp output
				$event[P_EVENT_IMAGES_EXTRAS] = $this->getEventImagesExtras($row[COL_ID]);
				
				array_push($return_result, $event);
			}
			return $return_result;
		}
		return false;
	}
	
	public function getEventImagesExtras($eventId){
		$query = "
			SELECT * FROM ".TAB_EVENT_IMAGES_EXTRAS." 
			WHERE 
				".COL_EVENT_ID." = $eventId AND ".COL_IS_DELETED." IS NULL";
		// echo $query;
		$res = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$return_result = array();
			while($r = $res->fetch_assoc()) {
				//var_dump($r);
				$eventImageData = array();
				//$eventImageData["temp"] =  clean($r[COL_DESCRIPTION]."  __  ".$r[COL_IMAGE]. "  VV  ");
				$eventImageData[COL_ID] = $r[COL_ID];
				$eventImageData[COL_DESCRIPTION] = $r[COL_DESCRIPTION];
				/*if(!file_exists(URL_EVENTS."/".$r[COL_IMAGE])){
					continue;
				}*/
				$eventImageData[COL_IMAGE] = $r[COL_IMAGE];
				array_push($return_result, $eventImageData);
			}
			return $return_result;
		}
		return false;
	}

	
	public function getGallery(){
	
		$query = "SELECT * FROM ".TAB_GALLERY." WHERE ".COL_IS_DELETED." IS NULL";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$return_result = array();
			while($row = $result->fetch_assoc()) {
				$gallery_image = array();
				if(!file_exists(URL_GALLERY."/".$row[COL_FILENAME])){
					continue;
				}
				$gallery_image[COL_ID] = $row[COL_ID];
				$gallery_image[KEY_URL] = $row[COL_FILENAME];
				$gallery_image[COL_TITLE] = $row[COL_TITLE];
				$gallery_image[COL_DESCRIPTION] = $row[COL_DESCRIPTION];
				array_push($return_result, $gallery_image);
			}
			return $return_result;
		}
		return false;
	
	
	}
	
	public function getSpecialSections(){
		$query = "SELECT * FROM ".TAB_SPECIAL_SECTIONS." WHERE ".COL_IS_DELETED." IS NULL";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$return_result = array();
			while($row = $result->fetch_assoc()) {
				$return_result[$row[COL_ID]] = $row[COL_NAME];
			}
			return $return_result;
		}
		return false;
	}
	
		
	public function getSpecialData($specialSectionId){
	 	$query = "SELECT * FROM ".TAB_SPECIAL_DATA."
		WHERE ".COL_SECTION_ID." = $specialSectionId AND
		        '".date('Y-m-d H:i:s')."' BETWEEN ".COL_ADDED_ON." AND ".COL_EXPIRES_ON." AND ".COL_IS_DELETED." IS NULL" ;
		
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$return_result = array();
			while($row = $result->fetch_assoc()) {
			       $special = array();
				$special[COL_ID] = $row[COL_ID];
				$special[COL_NAME] = $row[COL_NAME];
				$special[COL_PRICE] = $row[COL_PRICE];
				$special[COL_ADDED_ON] = $row[COL_ADDED_ON];
				$special[COL_EXPIRES_ON] = $row[COL_EXPIRES_ON];
				array_push($return_result, $special);
			}			
			return $return_result;
		}
		return false;
	}
	
	public function isOfferAssignedToUser($userId, $offerId){
		$query = "
			SELECT * FROM ".TAB_SET_OFFER." 
			WHERE
				".COL_USER_ID." = '$userId' AND
				".COL_OFFER_ID." = $offerId";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			if($result->num_rows >= 1){
				$row = $result->fetch_assoc();
				return $this->getOffers($userId, $offerId, $row[COL_ID]);
				
			} else {
				return false;
			}
		}
		return false;
	}
	
	public function setOffer($userId, $offerId){
		$query = "
			INSERT INTO ".TAB_SET_OFFER."
			(".COL_USER_ID.", ".COL_OFFER_ID.",".COL_ADDED_ON.") values ('$userId',$offerId, NOW())";
			//(".COL_USER_ID.", ".COL_OFFER_ID.",".COL_ADDED_ON.") values ('$userId',$offerId, '".date('Y-m-d H:i:s')."')";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			return true;
		}
		return false;
	}
	
	public function getUserOffer($userId){//, $activeOfferId = null){
		$query = "
			SELECT * FROM ".TAB_SET_OFFER." 
			WHERE
				".COL_USER_ID." = '$userId' AND
				".COL_OFFER_ID." IN (
					SELECT ".COL_OFFER_ID." FROM ".TAB_OFFERS."
					WHERE
						NOW() BETWEEN ".COL_STARTS_AT." AND ".COL_EXPIRES_ON."                        
				) AND ((NOW() <= ADDDATE(".COL_ADDED_ON.", INTERVAL 24 HOUR) AND ".COL_ENDED_ON." IS NULL ) OR (".COL_ENDED_ON." IS NOT NULL AND (NOW() <= ADDDATE(".COL_ADDED_ON.", INTERVAL 24 HOUR) )))";
				                                                                                                                                           //".COL_ENDED_ON."  
				//<= ADDDATE(".COL_ADDED_ON.", INTERVAL 24 HOUR)    
				// in place of NOW()  '".date('Y-m-d H:i:s')."'     
	        //echo $query;
		$result = $this->mysqli->query($query);
		
		//echo '6';
		//if(!$this->mysqli->connect_errno){
		if($result){
			if($result->num_rows >= 1){
				$row = $result->fetch_assoc();
				$offer = $this->getOffers($userId, $row[COL_OFFER_ID], $row[COL_ID]);
				if(!$offer){
					//echo ' - null - ';
					return null;
				}
				//echo '5';
				$set_offer_details = array();
			
				$set_offer_details[COL_ADDED_ON] = $row[COL_ADDED_ON];
				$set_offer_details[COL_ENDED_ON] = $row[COL_ENDED_ON];
				$set_offer_details[COL_WILL_EXPIRE_ON] = date('Y-m-d H:i:s',strtotime('+24 hours', strtotime($row[COL_ADDED_ON])));
				$set_offer_details[P_ACTIVE_OFFER_ID] = $row[COL_ID];
				$offer[KEY_SET_OFFER_DETAILS] =  $set_offer_details;
				return $offer;
				
			} else {
				//echo '7';
				return null;
			}
		}
		//echo '8';
		return null;
	}
	
	public function getOffers($userId, $offerId = null, $activeOfferId = null){
	       // echo "\n\nuid: $userId \t oid: ".($offerId == null ? "null": $offerId)."\n";
	      //  echo $offerId;
		file_put_contents("aa.txt","$userId  $offerId",FILE_APPEND);
		if($offerId != null){//&& $userId != null){
		//echo "\naa";
		$query = "SELECT * FROM ".TAB_OFFERS." ".
					" WHERE ".
						COL_OFFER_ID." NOT IN ".
							"(SELECT ".COL_OFFER_ID. 
							" FROM ".TAB_SET_OFFER.
							" WHERE ".
								COL_USER_ID." = '$userId' AND ".
								"(".
									COL_ENDED_ON." <= ADDDATE(".COL_ADDED_ON.", INTERVAL  24 HOUR ) OR ".
									COL_ENDED_ON." IS NULL ".
								")".
							")";
		
			
		} else {
		//echo "\nbb";
			$query = "SELECT * FROM ".TAB_OFFERS." ".
					" WHERE ".
						//(($userId == null || $userId == 'guest')?COL_OFFER_ID." = $offerId AND " : " ").
						"( NOW() BETWEEN  ".COL_ADDED_ON." AND ".COL_EXPIRES_ON.") AND ".
						COL_IS_DELETED." IS NULL ".
					" ORDER BY rand() ".
					" LIMIT 1";
		}
		//echo $query;
		
		
		/*
		$query = "SELECT * FROM ".TAB_OFFERS.
			($offerId == null ? 
				" WHERE ".COL_OFFER_ID." NOT IN (
					SELECT ".COL_OFFER_ID." 
					FROM ".TAB_SET_OFFER.
					"WHERE ".
						//COL_USER_ID." = '$userId' AND ".
						"(".COL_ENDED_ON." <= ADDDATE(".COL_ADDED_ON.", INTERVAL  24 HOUR ) OR ".COL_ENDED_ON." IS NULL ))" 
				: 
				" WHERE ".COL_OFFER_ID." = ".$offerId)." AND NOW() BETWEEN  ".COL_ADDED_ON." AND ".COL_EXPIRES_ON." AND ".COL_IS_DELETED." IS NULL ORDER BY rand() LIMIT 1";
			
					//echo $query;
			
		*/
		
		$result = $this->mysqli->query($query);
		//if(!$this->mysqli->connect_errno){
		if($result){
			$return_result = array();
			if($result->num_rows > 0){
				//echo '1122';
				while($row = $result->fetch_assoc()) {
	                                if($activeOfferId)$return_result[P_ACTIVE_OFFER_ID] = $activeOfferId;
	                                //$return_result[COL_ID] = $row[COL_ID];
	                                $return_result[COL_OFFER_ID] = $row[COL_OFFER_ID];
					$return_result[COL_NAME] = $row[COL_NAME];
					$return_result[COL_DESCRIPTION] = $row[COL_DESCRIPTION];
					
					if(!file_exists(URL_OFFERS."/".$row[COL_IMAGE])){
					continue;
				}
				
				
					$return_result[COL_IMAGE] = $row[COL_IMAGE];
					$return_result[COL_AVAILABLE_TIME] = $row[COL_AVAILABLE_TIME];
					$return_result[COL_ADDED_ON] = $row[COL_ADDED_ON];
					$return_result[COL_EXPIRES_ON] = $row[COL_EXPIRES_ON];
					$return_result[COL_STARTS_AT] = $row[COL_STARTS_AT];
				}
				
			}
			
			return $return_result;
		} echo $this->mysqli->connect_error;
		return false;
	}
	
	public function redeemOffer($activeOfferId){
		$query = "SELECT * FROM ".TAB_SET_OFFER." WHERE ".COL_ID." = $activeOfferId";
		$result = $this->mysqli->query($query);
		

		//if(!$this->mysqli->connect_errno){
		if($result){
			if($result->num_rows == 0)
			
			return false;
		}else {
			return false;
		} 
		
		
		$query = "
			UPDATE ".TAB_SET_OFFER." 
			SET ".COL_ENDED_ON." = '".date('Y-m-d H:i:s')."' 
			WHERE ".COL_ID." = $activeOfferId";
		//echo $query;
		$result = $this->mysqli->query($query);
		//if(!$this->mysqli->connect_errno){
		if($result){
			return true;
		}
	        
		return false;
	}
	
public function getAllOffers(){
		$query = "SELECT * FROM ".TAB_OFFERS." WHERE ".COL_IS_DELETED." IS NULL";//." where NOW() BETWEEN  ".COL_STARTS_AT." AND ".COL_EXPIRES_ON." ";
		$result = $this->mysqli->query($query);
		//if(!$this->mysqli->connect_errno){
		if($result){
			$return_result = array();
			if($result->num_rows > 0){
				//echo '1122';
				while($row = $result->fetch_assoc()) {
	                                $offer = array();
	                                
	                                $offer[COL_OFFER_ID] = $row[COL_OFFER_ID];
					$offer[COL_NAME] = $row[COL_NAME];
					$offer[COL_DESCRIPTION] = $row[COL_DESCRIPTION];
					$offer[COL_ADDED_ON] = $row[COL_ADDED_ON];
					$offer[COL_EXPIRES_ON] = $row[COL_EXPIRES_ON];
					$offer[COL_STARTS_AT] = $row[COL_STARTS_AT];
					if(!file_exists(URL_OFFERS."/".$row[COL_IMAGE])){
					continue;
				}
				
				        $offer[COL_IMAGE] = $row[COL_IMAGE];
					array_push($return_result, $offer);
				}
				
			}
			
			return $return_result;
		} echo $this->mysqli->connect_error;
		return false;
	}	
	
public function getAllEvents(){
		$query = "SELECT * FROM ".TAB_EVENTS." WHERE ".COL_IS_DELETED." IS NULL";//." where NOW() BETWEEN  ".COL_STARTS_AT." AND ".COL_EXPIRES_ON." ";
		$result = $this->mysqli->query($query);
		//if(!$this->mysqli->connect_errno){
		if($result){
			$return_result = array();
			if($result->num_rows > 0){
				//echo '1122';
				while($row = $result->fetch_assoc()) {
	                                $events = array();
	                                
	                                $events[COL_ID] = $row[COL_ID];
					$events[COL_TITLE] = $row[COL_TITLE];
					//$events[COL_DESCRIPTION] = $row[COL_DESCRIPTION];
					$events[COL_TAG] = $row[COL_TAG];
					$events[COL_ADDED_ON] = $row[COL_ADDED_ON];
					$events[COL_EXPIRES_ON] = $row[COL_EXPIRES_ON];
					$events[COL_STARTS_AT] = $row[COL_STARTS_AT];
					if(!file_exists(URL_EVENTS."/".$row[COL_IMAGE])){
					continue;
				}
				
				        $events[COL_IMAGE] = $row[COL_IMAGE];
				        $events[P_EVENT_IMAGES_EXTRAS] = $this->getEventImagesExtras($row[COL_ID]);
					array_push($return_result, $events);
				}
				
			}
			
			return $return_result;
		} echo $this->mysqli->connect_error;
		return false;
	}	
	
	
}
?>