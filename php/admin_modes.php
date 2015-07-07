<?php
require_once('./php/config.php');
require_once('./php/admin_config.php');
require_once('./php/db_connect.php');
require_once('./php/commons.php');
require_once('./php/db_functions.php');
require_once('./php/modes.php');
require_once('./php/admin_db_functions.php');
require_once('config.php');

class AdminModes{

	private $dbFunc = null;
	
	function __construct() {
		$this->dbFunc = new AdminDBFunctions();
	}

	function __destruct() {
	}

	public function addMenuSections($data){

		$addMenuSections = $this->dbFunc->addMenuSections($data);
		if($addMenuSections==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_ADDING_MENU_SECTIONS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_ADDING_MENU_SECTIONS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		$data[COL_ID] = $addMenuSections;
		
		return $data;
	}

	public function editMenuSections($data){

		$editMenuSections = $this->dbFunc->editMenuSections($data);
		if($editMenuSections==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_EDITING_MENU_SECTIONS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_EDITING_MENU_SECTIONS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	}      

	public function deleteMenuSections($data){

		$deleteMenuSections = $this->dbFunc->deleteMenuSections($data);
		if($deleteMenuSections==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_DELETING_MENU_SECTIONS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_DELETING_MENU_SECTIONS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	} 

	public function addMenuItems($data){

		$addMenuItems = $this->dbFunc->addMenuItems($data);
		$response = array();
		if($addMenuItems==false){
			$response[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$response[KEY_ERROR_CODE] = E_ERROR_ADDING_MENU_ITEMS;
				$response[KEY_ERROR_MESSAGE] = EM_ERROR_ADDING_MENU_ITEMS;
			}
			return $response;
		}
		
		$response[KEY_STATUS] = R_STATUS_SUCCESS;
		$response[COL_ID] = $addMenuItems[COL_ID];
		$response[COL_THUMBNAIL_IMAGE] = $addMenuItems[COL_THUMBNAIL_IMAGE];
		return $response;
	}    


	public function editMenuItems($data){
		$response = array();
		$editStatus = $this->dbFunc->editMenuItems($data);
		if(!$editStatus){
			$response[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$response[KEY_ERROR_CODE] = E_ERROR_EDITING_MENU_ITEMS;
				$response[KEY_ERROR_MESSAGE] = EM_ERROR_EDITING_MENU_ITEMS;
			}
			return $response;
		}
		$response[KEY_STATUS] = R_STATUS_SUCCESS;
		//$respone[COL_THUMBNAIL_IMAGE] = $editStatus[COL_THUMBNAIL_IMAGE];
		$response["row_status"] = $editStatus;
		return $response;
	}    


	public function deleteMenuItems($data){

		$deleteMenuItems = $this->dbFunc->deleteMenuItems($data);
		if($deleteMenuItems==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_DELETING_MENU_ITEMS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_DELETING_MENU_ITEMS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	} 


//--------------------


	public function addSpecialSections($data){

		$addSpecialSections = $this->dbFunc->addSpecialSections($data);
		if($addSpecialSections==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_ADDING_SPECIAL_SECTIONS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_ADDING_SPECIAL_SECTIONS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		$data[COL_ID] = $addSpecialSections;
		
		
		return $data;
	}

	public function editSpecialSections($data){

		$editSpecialSections = $this->dbFunc->editSpecialSections($data);
		if($editSpecialSections==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_EDITING_SPECIAL_SECTIONS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_EDITING_SPECIAL_SECTIONS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	}      

	public function deleteSpecialSections($data){

		$deleteSpecialSections = $this->dbFunc->deleteSpecialSections($data);
		if($deleteSpecialSections==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_DELETING_SPECIAL_SECTIONS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_DELETING_SPECIAL_SECTIONS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	} 

	public function addSpecialItems($inputData){

		$addStatus = $this->dbFunc->addSpecialItems($inputData);
		if($addStatus==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_ADDING_SPECIAL_ITEMS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_ADDING_SPECIAL_ITEMS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		$data[COL_ID] = $addStatus[COL_ID];
		$data[COL_ADDED_ON] = $addStatus[COL_ADDED_ON];
		return $data;
	}    


	public function editSpecialItems($data){

		$editSpecialItems = $this->dbFunc->editSpecialItems($data);
		if($editSpecialItems==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_EDITING_SPECIAL_ITEMS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_EDITING_SPECIAL_ITEMS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
                $response["row_status"] = $editSpecialItems;
		return $response;
	}    


	public function deleteSpecialItems($data){

		$deleteSpecialItems = $this->dbFunc->deleteSpecialItems($data);
		if($deleteSpecialItems==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_DELETING_SPECIAL_ITEMS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_DELETING_SPECIAL_ITEMS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	}   


//---------------

	public function addEvents($inputData){

		$addStatus = $this->dbFunc->addEvents($inputData);
		if($addStatus==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_ADDING_EVENTS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_ADDING_EVENTS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		$data[COL_ADDED_ON] = $addStatus[COL_ADDED_ON];
		$data[COL_ID] = $addStatus[COL_ID];
		$data[P_EVENT_IMAGES_EXTRAS] = $addStatus[P_EVENT_IMAGES_EXTRAS];
		$data[COL_IMAGE] = $addStatus[COL_IMAGE];
		// echo $addEvents;
		// var_dump($addEvents);
		return $data;
	}    


	public function editEvents($data){
		$response = array();
		$editEvents = $this->dbFunc->editEvents($data);
		if($editEvents==false){
			$response[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$response[KEY_ERROR_CODE] = E_ERROR_EDITING_EVENTS;
				$response[KEY_ERROR_MESSAGE] = EM_ERROR_EDITING_EVENTS;
			}
			return $response;
		}
		
		$response[KEY_STATUS] = R_STATUS_SUCCESS;
		
		$response["edit_status"] = $editEvents;
		return $response;
		
	}    


	public function deleteEvents($data){

		$deleteEvents = $this->dbFunc->deleteEvents($data);
		if($deleteEvents==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_DELETING_EVENTS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_DELETING_EVENTS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	}   

public function deleteEventImagesExtras($data){

		$deleteEventImages = $this->dbFunc->deleteEventImagesExtras($data);
		if($deleteEventImages==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_DELETING_EVENT_IMAGES_EXTRAS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_DELETING_EVENT_IMAGES_EXTRAS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	}
//----------

	public function addOffers($inputData){
		
		$addStatus = $this->dbFunc->addOffers($inputData);
		$data = array();
		if($addStatus==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_ADDING_OFFERS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_ADDING_OFFERS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		$data[COL_ADDED_ON] = $addStatus[COL_ADDED_ON];
		$data[COL_OFFER_ID] = $addStatus[COL_OFFER_ID];
		$data[COL_IMAGE] = $addStatus[COL_IMAGE];
		return $data;
	}    


	public function editOffers($data){

		$editOffers = $this->dbFunc->editOffers($data);
		if($editOffers==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_EDITING_OFFERS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_EDITING_OFFERS;
			}
			return $data;
		}
		
		//$data[KEY_STATUS] = R_STATUS_SUCCESS;
		$response[KEY_STATUS] = R_STATUS_SUCCESS;
		//$response[COL_IMAGE] = $editOffers[COL_IMAGE];
	        $response["row_status"] = $editOffers;
		return $response;
	}    


	public function deleteOffers($data){

		$deleteOffers = $this->dbFunc->deleteOffers($data);
		if($deleteOffers==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_DELETING_OFFERS;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_DELETING_OFFERS;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	}  

//-------

	public function editAbout($data){

		$editAbout = $this->dbFunc->editAbout($data);
		if($editAbout==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_EDITING_ABOUT;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_EDITING_ABOUT;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		//$response["row_status"] = $editAbout;
		return $data;
		
	}    

//----------

	public function addGalleryImages($data){

		$GalleryImages = $this->dbFunc->addGalleryImages($data);
		if($GalleryImages==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_ADDING_GALLERY_IMAGES;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_ADDING_GALLERY_IMAGES;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		$data[COL_ID] = $GalleryImages[COL_ID];
		$data[COL_FILENAME] = $GalleryImages[COL_FILENAME];
		return $data;
	}    


	public function editGalleryImages($data){

		$editGalleryImages = $this->dbFunc->editGalleryImages($data);
		if($editGalleryImages==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_EDITING_GALLERY_IMAGES;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_EDITING_GALLERY_IMAGES;
			}
			return $data;
		}
		
		$response[KEY_STATUS] = R_STATUS_SUCCESS;
		//$response[COL_FILENAME] = $editGalleryImages[COL_FILENAME];
		$response["row_status"] = $editGalleryImages;
		return $response;
	}    


	public function deleteGalleryImages($data){

		$deleteGalleryImages = $this->dbFunc->deleteGalleryImages($data);
		if($deleteGalleryImages==false){
			$data[KEY_STATUS] = R_STATUS_FAILED;
			if(SETTING_ERROR){
				$data[KEY_ERROR_CODE] = E_ERROR_DELETING_GALLERY_IMAGES;
				$data[KEY_ERROR_MESSAGE] = EM_ERROR_DELETING_GALLERY_IMAGES;
			}
			return $data;
		}
		
		$data[KEY_STATUS] = R_STATUS_SUCCESS;
		
		return $data;
	}  

//-------         









}
?>