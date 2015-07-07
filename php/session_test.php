<?php 
if(!isset($_REQUEST[P_SESSION_KEY])){
	if(SETTING_ERROR){
		$response[KEY_ERROR_CODE] = E_NO_SESSION_KEY;
		$response[KEY_ERROR_MESSAGE] = EM_NO_SESSION_KEY;
	}
	echo json_encode($response);
	exit;
}
if($_REQUEST[P_SESSION_KEY] != TEMP_SK){
	if(SETTING_ERROR){
		$response[KEY_ERROR_CODE] = E_INVALID_SESSION_KEY;
		$response[KEY_ERROR_MESSAGE] = EM_INVALID_SESSION_KEY;
	}
	echo json_encode($response);
	exit;
}
?>