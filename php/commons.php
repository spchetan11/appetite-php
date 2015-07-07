<?php

class Commons{

	/*private $db;
	function __construct($db) {
		$this->db = $db;
	}
	*/
	function __construct() {
	}
	
	function __destruct() {
	}

	public function clean($str, $encode_ent = false) {
		$str = @trim($str);
		if($encode_ent) {
			$str = htmlentities($str);
		}
		if(version_compare(phpversion(),'4.3.0') >= 0) {
			if(get_magic_quotes_gpc()) {
				$str = stripslashes($str);
			}
			if(@mysql_ping()) {
				$str = mysql_real_escape_string($str);
			} else {
				$str = addslashes($str);
			}
		} else {
			if(!get_magic_quotes_gpc()) {
				$str = addslashes($str);
			}
		}
		return $str;
	}
	
	/*
	
	public function generateRandomString($length = 0) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		
		switch($for){
			
			case FOR_PASSWORD_RESET:
				$length  = 20;
				break;
			default:
				return '';
		}
		
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		$rs = false;
		switch($for){
			
			case FOR_PASSWORD_RESET:
				$rs = mysql_query("SELECT * FROM ".TAB_PASSWORD_RESET." WHERE ".COL_RESET_CODE." = '$randomString'");
				break;
		}

		if($rs){
			if(mysql_num_rows($rs) == 0)
				return $randomString;
			else	return $this->generateRandomString($for, $company_id);
		} else
			return '';
	
}
	*/



	public function generateRandomString($length = 0) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		
		
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
}
}
?>