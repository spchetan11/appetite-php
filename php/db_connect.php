<?php
require_once 'config.php';

class DB_Connect {
	private $con = null;
	
	function __construct() {
	}
	
	function __destruct() {
	}
	
	public function connect() {
		$con = mysqli_connect(MYSQL_SERVERNAME, MYSQL_UNAME, MYSQL_PWD, MYSQL_DB);
		if(mysqli_connect_errno($con)) {
			return false;
		}
		return $con;
	}
	
	public function disconnect() {
		// mysqli_close($con);
	}
	
} 
?>