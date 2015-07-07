<?php
require_once('./php/config.php');
require_once('./php/admin_config.php');
require_once('./php/db_connect.php');
require_once('./php/commons.php');
require_once('./php/db_functions.php');
require_once('./php/modes.php');
require_once('./php/admin_modes.php');
require_once('db_connect.php');
require_once('config.php');
include_once('./php/commons.php');

class AdminDBFunctions{

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

public function addMenuSections($data){
                $name = $data[COL_NAME];
		$query = "
			INSERT INTO ".TAB_MENU_SECTIONS."
			(".COL_NAME.") values ('$name')";
		$result = $this->mysqli->query($query);
		// print_r($this->mysqli);
		//$return_result = array();
				if($result){
			$return_result = $this->mysqli->insert_id;
			
			return $return_result;
		}
		return false;
	}
	
public function editMenuSections($data){

/* // iterate $data
for($i=0; $i<count($data); $i++) // this is faster than foreach
{

    foreach($data[$i] as $key => $value){
    $sql[] = (is_numeric($value)) ? "`$key` = $value" : "`$key` = '" . mysql_real_escape_string($value) . "'";
    }
    $sqlclause = implode(",",$sql);
    $query = "INSERT INTO `json_table` SET $sqlclause";
} // for i */
                 $id = $data[COL_ID];
       $name = $data[COL_NAME];
       $query = "SELECT * FROM ".TAB_MENU_SECTIONS." WHERE ".COL_ID." = $id";
        $result = $this->mysqli->query($query);


        //if(!$this->mysqli->connect_errno){
        if($result){
            if($result->num_rows == 1)
            {
                $query = "UPDATE ".TAB_MENU_SECTIONS." SET ".COL_NAME." = '$name' WHERE ".COL_ID."=$id";
                $result2 = $this->mysqli->query($query);    
            }        
        
        }else {
            return false;
        } 
        
        
        if(!$this->mysqli->connect_errno){
            return true;
        }
        return false;
	}	
	
public function deleteMenuSections($data){
                
               $id = $data[COL_ID];
               $query = "SELECT * FROM ".TAB_MENU_SECTIONS." WHERE ".COL_ID." = $id";
        $result = $this->mysqli->query($query);
        

        //if(!$this->mysqli->connect_errno){
        if($result){
            if($result->num_rows == 0)
            
            return false;
        }else {
            return false;
        } 
        
        $query = "
            UPDATE ".TAB_MENU_SECTIONS."
            SET ".COL_IS_DELETED." = NOW() WHERE ".COL_ID." = $id";
        $result1 = $this->mysqli->query($query);

        $query2 = "
            UPDATE ".TAB_MENU_DATA."
            SET ".COL_IS_DELETED." = NOW() WHERE ".COL_SECTION_ID." = $id";
        $result2 = $this->mysqli->query($query2);
        if($result1 && $result2){
            return true;
        }
        return false;
    }
	
public function addMenuItems($data){

	$stringItems = [COL_TITLE, COL_DESCRIPTION];
	$nonStringItems = [COL_PRICE, COL_SECTION_ID];
	$file = [COL_THUMBNAIL_IMAGE];
	
	$colNamesToAdd = array();
	$colValuesToAdd = array();
	$nonStringItemsCount = 0;
	$stringItemsCount = 0;
	foreach($data as $colName => $value){
		if(in_array($colName, $stringItems)){
			//echo "String: $colName = $value \n";
			$value = "'$value'";
			$stringItemsCount++;
		} else if(in_array($colName, $nonStringItems)){
			$value = $value;
			if($colName == COL_PRICE && ! floatval($value)){
				$value = "null";
			}
			$nonStringItemsCount++;
		}
		if(in_array($colName, $file)){
		//$colNamesAndValuesToUpdate = array();
			$filename = $value;
			if($filename == '' || strtolower($filename) == 'null') {
					$isImageDeleted = true;
					//array_push($colNamesAndValuesToUpdate, " $colName = null ");		
					$colName = null;			
					continue;
				}
			$info = pathinfo($_FILES[$filename]['name']);
		        // print_r($info);
			$ext = $info['extension']; // get the extension of the file
			//check file exists
			$file_index = 0;
			do{
				$newname = "$filename".($file_index++ == 0 ? "." : "_".$file_index.".").$ext;
				$target = ".".KEY_BASE_URL_MENU.'/'.$newname;
			}while(is_file($target));
			//echo "1_".$_FILES[$filename]['name']."  \n2.".$filename."  \n3.".$newname."  \n4.".$ext."\n";
			$target = ".".KEY_BASE_URL_MENU.'/'.$newname;
			$value = "'$newname'";
			move_uploaded_file($_FILES[$filename]['tmp_name'], $target);
		}
		array_push($colNamesToAdd, " $colName ");
		array_push($colValuesToAdd, " $value ");
	}
	if(sizeof($colNamesToAdd) == 0){
		return false;
	}
	if($stringItemsCount == 0)
		return false;
	if($nonStringItemsCount == 0){
		//continue;
	}
	$query = "INSERT INTO ".TAB_MENU_DATA." (".implode(", ", $colNamesToAdd).") VALUES (".implode(", ", $colValuesToAdd).")";
	$query."\n";
	$result = $this->mysqli->query($query);
	if(!$this->mysqli->connect_errno){
	        $return_result = array();
		$return_result[COL_THUMBNAIL_IMAGE] = $newname.".".$ext;
		$return_result[COL_ID] = $this->mysqli->insert_id;
		return $return_result;
	} else
		return false;
	
}
	
public function editMenuItems($data){
	$debug = true;
	$row_status = array();
	
	$stringItems = [COL_TITLE, COL_DESCRIPTION];
	$nonStringItems = [COL_PRICE];
	$nonUpdatableItems = [COL_ID];
	$file = [COL_THUMBNAIL_IMAGE];
	
	$items = $data["items"];     
	for($i = 0; $i < sizeof($items); $i++){
		$item = $items[$i];
		$id = $item[COL_ID];
		
		// Check if id exists
		$oldFile = null;
		$query = "SELECT * FROM ".TAB_MENU_DATA." WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		if($result){
			if($result->num_rows != 1) {
				$row_status[$id] = false;
				continue;
			}else{
				$row = $result->fetch_array();
				
				$oldFile = $row[COL_THUMBNAIL_IMAGE];
			}
		}
		$colNamesAndValuesToUpdate = array();
		$row_status[$id]["menu_images"] = array();
		if($item !=null)
		foreach($item as $colName => $value){
		
			
			if(in_array($colName, $stringItems)){
				//echo "String: $colName = $value \n";
				$value = "'$value'";
			} else if(in_array($colName, $nonStringItems)){
				$value = $value;
			}
			if(in_array($colName, $file)){
				$filename = $value;
				if($filename == 'null'){ /*|| $_FILES[$filename]['name'] == '' || strtolower($_FILES[$filename]['name']) == 'null'*/
					$isImageDeleted = true;
					array_push($colNamesAndValuesToUpdate, " $colName = null ");
					continue;
				}else{
					$info = pathinfo($_FILES[$filename]['name']);
				        // print_r($info);
					$ext = $info['extension']; // get the extension of the file
					//check file exists
					$file_index = 0;
					do{
						$newname = "$filename".($file_index++ == 0 ? "." : "_".$file_index.".").$ext;
						$target = ".".KEY_BASE_URL_MENU.'/'.$newname;
					}while(is_file($target));
					//echo "1_".$_FILES[$filename]['name']."  \n2.".$filename."  \n3.".$newname."  \n4.".$ext."\n";
					$target = ".".KEY_BASE_URL_MENU.'/'.$newname;
					$value = "'$newname'";
					move_uploaded_file($_FILES[$filename]['tmp_name'], $target);

				}
				
				
				
			}
			if(in_array($colName, $nonUpdatableItems)){
				//skip id, thumb
				continue;
			}
			
			
			
			
			array_push($colNamesAndValuesToUpdate, " $colName = $value ");
		}
		
		
		
		if(sizeof($colNamesAndValuesToUpdate) == 0){
			// check if 
			continue;
		}
		// delete existing thumb
		$query = "UPDATE ".TAB_MENU_DATA." SET ".implode(", ", $colNamesAndValuesToUpdate)." WHERE ".COL_ID." = $id";
		//echo $query;
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){

			//$row_status["status"] = true;
			//$row_status[COL_THUMBNAIL_IMAGE] = $newname;
			$oldFilePath = ".".KEY_BASE_URL_MENU.'/'.$oldFile;
			$stub = array();
			$stub["image_id"] = $id;
			$stub["image_name"] = $newname;
			array_push($row_status[$id]["menu_images"], $stub);	
			if($oldFile != null && is_file($oldFilePath)){
				unlink($oldFilePath);
				/*if(!unlink($oldFilePath))
					echo "del failed : $oldFilePath";
				else	echo "del success: $oldFilePath";
				*/
			
			}
		} else
			$row_status[$id] = false;
		
	}
	return $row_status;
}	
	
		
	
public function deleteMenuItems($data){
                 
                $id = $data[COL_ID];
                $query = "SELECT * FROM ".TAB_MENU_DATA." WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		

		//if(!$this->mysqli->connect_errno){
		if($result){
            if($result->num_rows == 0)
            
            return false;
        }else {
            return false;
        } 
		
		$query = "
			UPDATE ".TAB_MENU_DATA."
			SET ".COL_IS_DELETED." = NOW() WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			return true;
		}
		return false;
	}	
	

//--------------

public function addSpecialSections($data){
                $name = $data[COL_NAME];
		$query = "
			INSERT INTO ".TAB_SPECIAL_SECTIONS."
			(".COL_NAME.") values ('$name')";
		$result = $this->mysqli->query($query);
		// print_r($this->mysqli);
		//$return_result = array();
				if(!$this->mysqli->connect_errno){
			$return_result = $this->mysqli->insert_id;
			
			return $return_result;
		}
		return false;
	}
	
public function editSpecialSections($data){
                $id = $data[COL_ID];
                $name = $data[COL_NAME];
                $query = "SELECT * FROM ".TAB_SPECIAL_SECTIONS." WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		

		//if(!$this->mysqli->connect_errno){
		if($result){
            if($result->num_rows == 1)
            {
                $query = "UPDATE ".TAB_SPECIAL_SECTIONS." SET ".COL_NAME." = '$name' WHERE ".COL_ID."=$id";
                $result2 = $this->mysqli->query($query);    
            }        
        
        }else {
            return false;
        } 
        
        
        if(!$this->mysqli->connect_errno){
            return true;
        }
        return false;
	}	
	
public function deleteSpecialSections($data){
                 
               $id = $data[COL_ID];
               $query = "SELECT * FROM ".TAB_SPECIAL_SECTIONS." WHERE ".COL_ID." = $id";
        $result = $this->mysqli->query($query);
        

        //if(!$this->mysqli->connect_errno){
        if($result){
            if($result->num_rows == 0)
            
            return false;
        }else {
            return false;
        } 
        
        $query = "
            UPDATE ".TAB_SPECIAL_SECTIONS."
            SET ".COL_IS_DELETED." = NOW() WHERE ".COL_ID." = $id";
        $result1 = $this->mysqli->query($query);

        $query2 = "
            UPDATE ".TAB_SPECIAL_DATA."
            SET ".COL_IS_DELETED." = NOW() WHERE ".COL_SECTION_ID." = $id";
        $result2 = $this->mysqli->query($query2);
        if($result1 && $result2){
            return true;
        }
        return false;
	}
	
public function addSpecialItems($data){
         
                $name = $data[COL_NAME];
                $price = $data[COL_PRICE];
                
                $expires_on = $data[COL_EXPIRES_ON];
                $section_id = $data[COL_SECTION_ID];
                
                
		$query = "
			INSERT INTO ".TAB_SPECIAL_DATA."
			(".COL_NAME.",".COL_PRICE.",".COL_EXPIRES_ON.",".COL_SECTION_ID.") values ('$name',$price,'$expires_on',$section_id)";
		$result = $this->mysqli->query($query);
		$return_result = array();
		$newId = 0;
		if(!$this->mysqli->connect_errno){
			$newId = $this->mysqli->insert_id;
			$return_result[COL_ID] = $newId;
		}
			
			$query2 = "
		SELECT ".COL_ADDED_ON."
		FROM ".TAB_SPECIAL_DATA." WHERE ".COL_ID." = $newId";
		$result = $this->mysqli->query($query2);
		if($result){
			if($result->num_rows==1){
				$row = $result->fetch_assoc();
				$return_result[COL_ADDED_ON] = $row[COL_ADDED_ON];
			}
			return $return_result;
		}
		return false;
	}
	
public function editSpecialItems($data){
               /* $id = $data[COL_ID];
                $name = $data[COL_NAME];
                $price = $data[COL_PRICE];
                
                $expires_on = $data[COL_EXPIRES_ON];
                $section_id = $data[COL_SECTION_ID];*/
                
                 
               	$row_status = array();
	
	$stringItems = [COL_NAME, COL_EXPIRES_ON];
	$nonStringItems = [COL_PRICE];
	$nonUpdatableItems = [COL_ID];
	
	$items = $data["items"];     
	for($i = 0; $i < sizeof($items); $i++){
		$item = $items[$i];
		$id = $item[COL_ID];
		
		// Check if id exists
		$query = "SELECT * FROM ".TAB_SPECIAL_DATA." WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		if($result){
			if($result->num_rows != 1) {
				$row_status[$id] = false;
				continue;
			}
		}
		$colNamesAndValuesToUpdate = array();
		foreach($item as $colName => $value){
			if(in_array($colName, $stringItems)){
				//echo "String: $colName = $value \n";
				$value = "'$value'";
			} /*else if(in_array($colName, $nonStringItems)){
				echo "Non-string: $colName = $value \n";
			}*/
			if(in_array($colName, $nonUpdatableItems)){
				//skip id, thumb
				continue;
			}
			array_push($colNamesAndValuesToUpdate, " $colName = $value ");
		}
		if(sizeof($colNamesAndValuesToUpdate) == 0){
			// check if 
			continue;
		}
		$query = "UPDATE ".TAB_SPECIAL_DATA." SET ".implode(", ", $colNamesAndValuesToUpdate)." WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$row_status[$id] = true;
		} else
			$row_status[$id] = false;
		
	}
	return $row_status;
}	

	
		
	
public function deleteSpecialItems($data){
                 
                $id = $data[COL_ID];
                $query = "SELECT * FROM ".TAB_SPECIAL_DATA." WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		

		//if(!$this->mysqli->connect_errno){
		if($result){
            if($result->num_rows == 0)
            
            return false;
        }else {
            return false;
        } 
		
		$query = "
			UPDATE ".TAB_SPECIAL_DATA."
			SET ".COL_IS_DELETED." = NOW() WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			return true;
		}
		return false;
	}
	
	
//------------------

			
public function addEvents($data){
	$debug = false;
        $stringItems = [COL_TITLE, COL_TAG, COL_DESCRIPTION, COL_STARTS_AT, COL_EXPIRES_ON];
	$file = [COL_IMAGE];
	
	$colNamesToAdd = array();
	$colValuesToAdd = array();
	if($debug) echo "Start of Main query\n";
	foreach($data as $colName => $value){
		if(in_array($colName, $stringItems)){
			if($debug) echo "String: $colName = $value \n";
			$value = "'$value'";
			array_push($colNamesToAdd, " $colName ");
			array_push($colValuesToAdd, " $value ");
		} 
		if(in_array($colName, $file)){
			//$colNamesAndValuesToUpdate = array();
			$filename = $value;
			if($filename == '' || strtolower($filename) == 'null') {
					$isImageDeleted = true;
					//array_push($colNamesAndValuesToUpdate, " $colName = null ");		
					$colName = null;			
					continue;
				}
			$info = pathinfo($_FILES[$filename]['name']);
			$ext = $info['extension']; // get the extension of the file
			//check file exists
			$file_index = 0;
			$target = "";
			$newname = "";
			do{
				$newname = "$filename".($file_index++ == 0 ? "." : "_".$file_index.".").$ext;
				$target = ".".KEY_BASE_URL_EVENTS.'/'.$newname;
			}while(is_file($target));
			if($debug) echo "File: $newname = ".$_FILES[$filename]['name']."\n";
			$target = ".".KEY_BASE_URL_EVENTS.'/'.$newname;
			$value = "'$newname'";
			move_uploaded_file($_FILES[$filename]['tmp_name'], $target);
			array_push($colNamesToAdd, " $colName ");
			array_push($colValuesToAdd, " $value ");
		}
	}
	
	if(sizeof($colNamesToAdd) == 0){
		return false;
	}
	
	$query = "INSERT INTO ".TAB_EVENTS." (".implode(", ", $colNamesToAdd).") VALUES (".implode(", ", $colValuesToAdd).")";
	if($debug) echo "Main query : $query \n";
	$result = $this->mysqli->query($query);
	if($this->mysqli->connect_errno){
		return false;
	}
	if($debug) echo "End of Main query\n";
	
	$event_id = $this->mysqli->insert_id;
	$return_result = array();
	$return_result[COL_IMAGE] = $newname;
	$return_result[COL_ID] = $event_id;
	
	$query = "SELECT * FROM ".TAB_EVENTS." WHERE ".COL_ID." = ".$return_result[COL_ID];
	$result = $this->mysqli->query($query);
	$row = $result->fetch_array();
	$return_result[COL_ADDED_ON] = $row[COL_ADDED_ON];
	
	
	if($debug) print_r($return_result);
	
	$return_result[P_EVENT_IMAGES_EXTRAS] = array();
	
	
	if($debug) echo "\n\nStart of Sub queries\n";
	if($data[P_EVENT_IMAGES_EXTRAS] != null)
	for($i = 0; $i < sizeof($data[P_EVENT_IMAGES_EXTRAS]); $i++){
		$colNamesToAdd = array();
		$colValuesToAdd = array();
		$filename = "";
		foreach($data[P_EVENT_IMAGES_EXTRAS][$i] as $colName => $value){
			if(in_array($colName, $stringItems)){
				if($debug) echo "String: $colName = $value \n";
				$value = "'$value'";
				if($colName==COL_DESCRIPTION){
				  $temp_desc_variable="'$value'";
				  }
			} 
			if(in_array($colName, $file)){
				$filename = $value;
				$info = pathinfo($_FILES[$filename]['name']);
				$ext = $info['extension']; // get the extension of the file
				//check file exists
				$file_index = 0;
				$target = "";
				$newname = "";
				do{
					$newname = "$filename".($file_index++ == 0 ? "." : "_".$file_index.".").$ext;
					$target = ".".KEY_BASE_URL_EVENTS.'/'.$newname;
				}while(is_file($target));
				if($debug) echo "File: $newname = ".$_FILES[$filename]['name']."\n";
				$target = ".".KEY_BASE_URL_EVENTS.'/'.$newname;
				$value = "'$newname'";
				move_uploaded_file($_FILES[$filename]['tmp_name'], $target);
			}
			array_push($colNamesToAdd, " $colName ");
			array_push($colValuesToAdd, " $value ");
		}
		if(sizeof($colNamesToAdd) == 0){
			return false;
		}
		$query = "INSERT INTO ".TAB_EVENT_IMAGES_EXTRAS." (".COL_EVENT_ID.", ".implode(", ", $colNamesToAdd).") VALUES ($event_id, ".implode(", ", $colValuesToAdd).")";
		$result = $this->mysqli->query($query);
		if($debug) echo "Sub query : $query \n";
		if($this->mysqli->connect_errno){
			continue;
		}
		$extra_image_id = $this->mysqli->insert_id;
		$stub = array();
		$stub["id"] = $extra_image_id;
		$stub["image"] = $newname;
		$stub["description"] = str_replace("'", "" , $temp_desc_variable);
		array_push($return_result[P_EVENT_IMAGES_EXTRAS], $stub);
		//$return_result = array();
		//$return_result[COL_IMAGE] = $newname;
		//$return_result[COL_ID] = $this->mysqli->insert_id;
		//return $return_result;
	}
	
	if($debug) echo "Final return : \n";
	if($debug) print_r($return_result);
	return $return_result;
}
	
public function editEvents($data){

	$row_status = array();		       
	$stringItems = [COL_TITLE, COL_TAG, COL_STARTS_AT, COL_EXPIRES_ON, COL_DESCRIPTION];
	$nonUpdatableItems = [COL_ID, COL_EVENT_ID];
	$arrayItems = [P_EVENT_IMAGES_EXTRAS];
	$file = [COL_IMAGE];		
	$items = $data["events"];
	
	for($i = 0; $i < sizeof($items); $i++){
		$item = $items[$i];
		$id = $item[COL_ID];
		$row_status[$id]["status"] = false;
		$row_status[$id]["images_status"] = array();
		$row_status[$id][P_EVENT_IMAGES_EXTRAS] = array();
		//$event_id = $item[COL_EVENT_ID];
		
		// Check if id exists
		$query = "SELECT * FROM ".TAB_EVENTS." WHERE ".COL_ID." = $id";		
		$result = $this->mysqli->query($query);
		if($result){
			if($result->num_rows != 1) {
				$row_status[$id] = false;
				continue;
			}
		}
		$colNamesAndValuesToUpdate = array();
		if($item !=null)
		foreach($item as $colName => $value){
			if(in_array($colName, $stringItems)){
				//echo "String: $colName = $value \n";
				$value = "'$value'";
				array_push($colNamesAndValuesToUpdate, " $colName = $value ");
			} else if(in_array($colName, $file)){
				$filename = $value;
				if($filename == 'null'){ /*|| $_FILES[$filename]['name'] == '' || strtolower($_FILES[$filename]['name']) == 'null'*/
					$isImageDeleted = true;
					array_push($colNamesAndValuesToUpdate, " $colName = null ");
					continue;
				}
				$value = "'".$this->fileUploadData($filename, KEY_BASE_URL_EVENTS)."'";				
				array_push($colNamesAndValuesToUpdate, " $colName = $value ");
			}
			 else if(in_array($colName, $arrayItems)){
			 	//echo "_in_";
				if($item[P_EVENT_IMAGES_EXTRAS] != null)
				for($j = 0; $j < sizeof($item[P_EVENT_IMAGES_EXTRAS]); $j++){
					$colNamesAndValuesToUpdate1 = array();
					$colNamesToAdd = array();
					$colValuesToAdd = array();
					$filename = "";
					//echo "_i_";
					$image_id = null;
					foreach($item[P_EVENT_IMAGES_EXTRAS][$j] as $colName => $value){
						if(in_array($colName, $stringItems)){
							$value = "'$value'";
							array_push($colNamesAndValuesToUpdate1, " $colName = $value ");	
							array_push($colNamesToAdd, " $colName ");
							array_push($colValuesToAdd, " $value ");						
						} else if(in_array($colName, $file)){
							$filename = $value;
							$value = "'".$this->fileUploadData($filename, KEY_BASE_URL_EVENTS)."'";
							array_push($colNamesAndValuesToUpdate1, " $colName = $value ");	
							array_push($colNamesToAdd, " $colName ");
							array_push($colValuesToAdd, " $value ");						
						} else if($colName == "id"){
							$image_id = $value;
								
						}
					 }
					if(sizeof($colNamesAndValuesToUpdate1) == 0 &&  $image_id != null && sizeof($colNamesToAdd) == 0){
						return false;
					}
					if($image_id != null){
						$query = "UPDATE ".TAB_EVENT_IMAGES_EXTRAS." SET ".implode(", ",$colNamesAndValuesToUpdate1)." WHERE ".COL_ID." = $image_id";
						$result = $this->mysqli->query($query);	
						if($result){
							$row_status[$id]["images_status"][$image_id] = true;
						} else{
							$row_status[$id]["images_status"][$image_id] = false;
						}
					} else{
						// adding extra image in edit mode since id is not specipied, i.e. it's a new extra image
						//$query = "INSERT INTO ".TAB_EVENT_IMAGES_EXTRAS." SET ".implode(", ",$colNamesAndValuesToUpdate1)." WHERE ".COL_ID." = $image_id";
						array_push($colNamesToAdd, " ".COL_EVENT_ID." ");
						array_push($colValuesToAdd, " $id ");	
						$query = "INSERT INTO ".TAB_EVENT_IMAGES_EXTRAS." (".implode(", ", $colNamesToAdd).") VALUES (".implode(", ", $colValuesToAdd).")";
						$result = $this->mysqli->query($query);	
						if($result){
							$extra_image_id = $this->mysqli->insert_id;
							$stub = array();
							$stub["image_id"] = $extra_image_id;
							$stub["image_name"] = $filename;
							array_push($row_status[$id][P_EVENT_IMAGES_EXTRAS], $stub);
						}
					}
					//echo $query;
					
				}
			}
		}
	
		if(sizeof($colNamesAndValuesToUpdate) == 0){
			// check if 
			continue;
		}
		$query = "UPDATE ".TAB_EVENTS." SET ".implode(", ",$colNamesAndValuesToUpdate)." WHERE ".COL_ID." = $id";
		//echo $query;
		$result = $this->mysqli->query($query);
		if($this->mysqli->connect_errno){
			$row_status[$id]["status"] = false;
		}else	$row_status[$id]["status"] = true;
	}
        return $row_status;
}	
	
		
	
public function deleteEvents($data){
                 
                $id = $data[COL_ID];
                $query = "SELECT * FROM ".TAB_EVENTS." WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		

		//if(!$this->mysqli->connect_errno){
		if($result){
            if($result->num_rows == 0)
            
            return false;
        }else {
            return false;
        } 
		
		$query = "
			UPDATE ".TAB_EVENTS."
			SET ".COL_IS_DELETED." = NOW() WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			return true;
		}
		return false;
	}
	
public function deleteEventImagesExtras($data){
                 
                $id = $data[COL_ID];
                $query = "SELECT * FROM ".TAB_EVENT_IMAGES_EXTRAS." WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		

		//if(!$this->mysqli->connect_errno){
		if($result){
            if($result->num_rows == 0)
            
            return false;
        }else {
            return false;
        } 
		
		$query = "
			UPDATE ".TAB_EVENT_IMAGES_EXTRAS."
			SET ".COL_IS_DELETED." = NOW() WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			return true;
		}
		return false;
	}
		
//-------------------		
	
public function addOffers($data){
                 
	$stringItems = [COL_NAME, COL_DESCRIPTION, COL_STARTS_AT, COL_EXPIRES_ON];
	$file = [COL_IMAGE];
	$return_result = array();
	$colNamesToAdd = array();
	$colValuesToAdd = array();
	$stringItemsCount = 0;
	foreach($data as $colName => $value){
		if(in_array($colName, $stringItems)){
			//echo "String: $colName = $value \n";
			$value = "'$value'";
			$stringItemsCount++;
		} 
		if(in_array($colName, $file)){
			//$colNamesAndValuesToUpdate = array();
			$filename = $value;
			if($filename == '' || strtolower($filename) == 'null') {
					$isImageDeleted = true;
					//array_push($colNamesAndValuesToUpdate, " $colName = null ");		
					$colName = null;			
					continue;
				}
			//echo $filename;
			$info = pathinfo($_FILES[$filename]['name']);
		        //print_r($info);
			$ext = $info['extension']; // get the extension of the file
			//check file exists
			$file_index = 0;
			$target = "";
			$newname = "";
			do{
				$newname = "$filename".($file_index++ == 0 ? "." : "_".$file_index.".").$ext;
				$target = ".".KEY_BASE_URL_OFFERS.'/'.$newname;
			}while(is_file($target));
			//echo "1_".$_FILES[$filename]['name']."  \n2.".$filename."  \n3.".$newname."  \n4.".$ext."\n";
			$target = ".".KEY_BASE_URL_OFFERS.'/'.$newname;
			$value = "'$newname'";
			move_uploaded_file($_FILES[$filename]['tmp_name'], $target);
		}
		array_push($colNamesToAdd, " $colName ");
		array_push($colValuesToAdd, " $value ");
		}
		if(sizeof($colNamesToAdd) == 0){
			return false;
		}
		if($stringItemsCount == 0)
			return false;
		$query = "INSERT INTO ".TAB_OFFERS." (".implode(", ", $colNamesToAdd).") VALUES (".implode(", ", $colValuesToAdd).")";
		//echo $query."\n";
		$result = $this->mysqli->query($query);
		$newId = 0;
		if(!$this->mysqli->connect_errno){
			$return_result[COL_OFFER_ID] = $this->mysqli->insert_id;
			$query = "SELECT * FROM ".TAB_OFFERS." WHERE ".COL_OFFER_ID." = ".$return_result[COL_OFFER_ID];
			$result = $this->mysqli->query($query);
			$row = $result->fetch_array();
			$return_result[COL_ADDED_ON] = $row[COL_ADDED_ON];
			
			$return_result[COL_IMAGE] = $newname;			
			return $return_result;
		} else
			return false;	
		
              /*  $query2 = "
		SELECT ".COL_ADDED_ON."
		FROM ".TAB_OFFERS." WHERE ".COL_OFFER_ID." = $newId";
		echo $query;
		$result = $this->mysqli->query($query2);
		if($result){
			if($result->num_rows==1){
				$row = $result->fetch_assoc();
				$return_result[COL_ADDED_ON] = $row[COL_ADDED_ON];
			}
			return $return_result;
		}*/	
		
}
	
public function editOffers($data){
                /*$offerid = $data[COL_OFFER_ID];
                $name = $data[COL_NAME];
                $description = $data[COL_DESCRIPTION];
                $starts_at = $data[COL_STARTS_AT];
                $expires_on = $data[COL_EXPIRES_ON];*/
                
                $row_status = array();
	
		$stringItems = [COL_NAME, COL_DESCRIPTION, COL_STARTS_AT, COL_EXPIRES_ON];
		$nonUpdatableItems = [COL_OFFER_ID];
		$file = [COL_IMAGE];
		
		$items = $data["offers"];     
		for($i = 0; $i < sizeof($items); $i++){
		$item = $items[$i];
		$id = $item[COL_OFFER_ID];
		
		// Check if id exists
		$oldFile = null;
		$query = "SELECT * FROM ".TAB_OFFERS." WHERE ".COL_OFFER_ID." = $id";
		$result = $this->mysqli->query($query);
		if($result){
			if($result->num_rows != 1) {
				$row_status[$id] = false;
				continue;
			}else{
				$row = $result->fetch_array();
				
				$oldFile = $row[COL_IMAGE];
			}
		}
		$row_status[$id]["offer_images"] = array();
		$colNamesAndValuesToUpdate = array();
		if($item !=null)
		foreach($item as $colName => $value){
			if(in_array($colName, $stringItems)){
				//echo "String: $colName = $value \n";
				$value = "'$value'";
			} 
			if(in_array($colName, $file)){
				$filename = $value;
				if($filename == 'null'){ /*|| $_FILES[$filename]['name'] == '' || strtolower($_FILES[$filename]['name']) == 'null'*/
					$isImageDeleted = true;
					array_push($colNamesAndValuesToUpdate, " $colName = null ");
					continue;
				}
				$info = pathinfo($_FILES[$filename]['name']);
			        // print_r($info);
				$ext = $info['extension']; // get the extension of the file
				//check file exists
				$file_index = 0;
				do{
					$newname = "$filename".($file_index++ == 0 ? "." : "_".$file_index.".").$ext;
					$target = ".".KEY_BASE_URL_OFFERS.'/'.$newname;
				}while(is_file($target));
				//echo "1_".$_FILES[$filename]['name']."  \n2.".$filename."  \n3.".$newname."  \n4.".$ext."\n";
				$target = ".".KEY_BASE_URL_OFFERS.'/'.$newname;
				$value = "'$newname'";
				move_uploaded_file($_FILES[$filename]['tmp_name'], $target);
			}
			if(in_array($colName, $nonUpdatableItems)){
				//skip id, thumb
				continue;
			}
			array_push($colNamesAndValuesToUpdate, " $colName = $value ");
		}
		if(sizeof($colNamesAndValuesToUpdate) == 0){
			// check if 
			continue;
		}
		// delete existing thumb
		$query = "UPDATE ".TAB_OFFERS." SET ".implode(", ", $colNamesAndValuesToUpdate)." WHERE ".COL_OFFER_ID." = $id";
		//echo $query;
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$row_status[$id]["status"] = true;
			//$row_status[COL_IMAGE] = $newname;	
			$oldFilePath = ".".KEY_BASE_URL_OFFERS.'/'.$oldFile;
			if($oldFile != null && is_file($oldFilePath)){
				unlink($oldFilePath);
				/*if(!unlink($oldFilePath))
					echo "del failed : $oldFilePath";
				else	echo "del success: $oldFilePath";
				*/
			}
			$stub = array();
			$stub["image_id"] = $id;
			$stub["image_name"] = $newname;
			array_push($row_status[$id]["offer_images"], $stub);	
		} else
			$row_status[$id] = false;
		
	}
	return $row_status;
                	
}	
	
		
	
public function deleteOffers($data){
                 
                $offerid = $data[COL_OFFER_ID];
                $query = "SELECT * FROM ".TAB_OFFERS." WHERE ".COL_OFFER_ID." = $offerid";
		$result = $this->mysqli->query($query);
		

		//if(!$this->mysqli->connect_errno){
		if($result){
            if($result->num_rows == 0)
            
            return false;
        }else {
            return false;
        } 
		
		$query = "
			UPDATE ".TAB_OFFERS."
			SET ".COL_IS_DELETED." = NOW() WHERE ".COL_OFFER_ID." = $offerid";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			return true;
		}
		return false;
	}
	
//----------------	
	
public function editAbout($data){
              
	    $row_status = array();
	    
	    $stringItems = [COL_DESCRIPTION, COL_PHONE, COL_EMAIL, COL_FB_LINK, COL_ADDRESS, COL_COMPANY_NAME];
	    $arrayItems = [COL_PLACE_TAGS];
	    $objectItems = ["location", "timings"];
	    //$location = [COL_LATITUDE, COL_LONGITUDE];
	    //$placetags = [COL_PLACE_TAGS];
	    $about_data = $data;//["about"];     
	    $colNamesAndValuesToUpdate = array();
	    foreach($about_data as $colName => $value){
        
        /*if(in_array($colName, $placetags)){
            //$value = "'$value'"; 
            $value = implode(", ",'$value');
        }*/
        $query_part = "";
        if(in_array($colName, $stringItems)){
            $query_part = "$colName = '$value'";
             }else if(in_array($colName, $arrayItems)){
                 $query_part = "$colName = '".implode(", ",$value)."'";
             }else if(in_array($colName, $objectItems)){    
                 $query_parts = array();
                 foreach($value as $cName => $cVal){
                     array_push($query_parts, "$cName = '$cVal'");
                 }
                 $query_part = implode(", ", $query_parts);
             }
             array_push($colNamesAndValuesToUpdate, " $query_part ");
    }
        /*if(in_array($colName, $nonUpdatableItems)){
            
            continue;
        }*/
    
    
    if(sizeof($colNamesAndValuesToUpdate) == 0){
        // check if 
        continue;
    }
    $query = "UPDATE ".TAB_ABOUT." SET ".implode(", ", $colNamesAndValuesToUpdate)."";
    //echo $query;
    $result = $this->mysqli->query($query);
    if($this->mysqli->connect_errno)
        return false;
    return true;
    
}
	
	
//-------------------		
	
public function addGalleryImages($data){

//-----
        $stringItems = [COL_TITLE, COL_DESCRIPTION];
	$file = [COL_FILENAME];
	
	$colNamesToAdd = array();
	$colValuesToAdd = array();
	$stringItemsCount = 0;
	foreach($data as $colName => $value){
		if(in_array($colName, $stringItems)){
			//echo "String: $colName = $value \n";
			$value = "'$value'";
			$stringItemsCount++;
		} 
		if(in_array($colName, $file)){
			//$colNamesAndValuesToUpdate = array();
			$filename = $value;
			if($filename == '' || strtolower($filename) == 'null') {
					$isImageDeleted = true;
					//array_push($colNamesAndValuesToUpdate, " $colName = null ");		
					$colName = null;			
					continue;
				}
			//echo $filename;
			$info = pathinfo($_FILES[$filename]['name']);
		        //print_r($info);
			$ext = $info['extension']; // get the extension of the file
			//check file exists
			$file_index = 0;
			$target = "";
			$newname = "";
			do{
				$newname = "$filename".($file_index++ == 0 ? "." : "_".$file_index.".").$ext;
				$target = ".".KEY_BASE_URL_GALLERY.'/'.$newname;
			}while(is_file($target));
			//echo "1_".$_FILES[$filename]['name']."  \n2.".$filename."  \n3.".$newname."  \n4.".$ext."\n";
			$target = ".".KEY_BASE_URL_GALLERY.'/'.$newname;
			$value = "'$newname'";
			move_uploaded_file($_FILES[$filename]['tmp_name'], $target);
		}
		array_push($colNamesToAdd, " $colName ");
		array_push($colValuesToAdd, " $value ");
		}
		if(sizeof($colNamesToAdd) == 0){
			return false;
		}
		if($stringItemsCount == 0)
			return false;
		$query = "INSERT INTO ".TAB_GALLERY." (".implode(", ", $colNamesToAdd).") VALUES (".implode(", ", $colValuesToAdd).")";
		//echo $query."\n";
		$result = $this->mysqli->query($query);
		$newId = 0;
		if(!$this->mysqli->connect_errno){
		$return_result = array();
			$return_result[COL_ID] = $this->mysqli->insert_id;
			$query = "SELECT * FROM ".TAB_GALLERY." WHERE ".COL_ID." = ".$return_result[COL_ID];
			$result = $this->mysqli->query($query);
			$row = $result->fetch_array();
			$return_result[COL_FILENAME] = $row[COL_FILENAME];
			return $return_result;
		} else
			return false;	
}
//-----
         
/*        $title = $data[COL_TITLE];
        $description = $data[COL_DESCRIPTION];
        $filename = $data[COL_FILENAME];
        
        $info = pathinfo($_FILES[$filename]['name']);
     //   print_r($info);
	$ext = $info['extension']; // get the extension of the file
	$newname = "$filename.".$ext; 
	//echo "1_".$_FILES[$filename]['name']."  \n2.".$filename."  \n3.".$newname."  \n4.".$ext."\n";
	$target = ".".KEY_BASE_URL_GALLERY.'/'.$newname;
	//move_uploaded_file($_FILES[$filename]['tmp_name'], $target);
	$move_status = move_uploaded_file($_FILES[$filename]['tmp_name'], $target);
	if(!$move_status)
		return -1;
        $query = "
		INSERT INTO ".TAB_GALLERY."
		(".COL_TITLE.",".COL_DESCRIPTION.", ".COL_FILENAME.") values ('$title', '$description' , '$newname')";
	$result = $this->mysqli->query($query);
	if(!$this->mysqli->connect_errno){
		$return_result = $this->mysqli->insert_id;
		return $return_result;
	}	
	return false;*/

	
	public function editGalleryImages($data){
   //---
        $row_status = array();
	
	$stringItems = [COL_TITLE, COL_DESCRIPTION];
	$nonUpdatableItems = [COL_ID];
	$file = [COL_FILENAME];
	
	$items = $data["images"];     
	for($i = 0; $i < sizeof($items); $i++){
		$item = $items[$i];
		$id = $item[COL_ID];
		
		
		// Check if id exists
		$oldFile = null;
		$query = "SELECT * FROM ".TAB_GALLERY." WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		if($result){
			if($result->num_rows != 1) {
				$row_status[$id] = false;
				continue;
			}else{
				$row = $result->fetch_array();
				
				$oldFile = $row[COL_FILENAME];
			}
		}
		$row_status[$id]["gallery_images"] = array();
		$colNamesAndValuesToUpdate = array();
		if($item !=null)
		foreach($item as $colName => $value){
			if(in_array($colName, $stringItems)){
				//echo "String: $colName = $value \n";
				$value = "'$value'";
			} 
			if(in_array($colName, $file)){
				$filename = $value;
				if($filename == 'null'){ /*|| $_FILES[$filename]['name'] == '' || strtolower($_FILES[$filename]['name']) == 'null'*/
					$isImageDeleted = true;
					array_push($colNamesAndValuesToUpdate, " $colName = null ");
					continue;
				}
				$info = pathinfo($_FILES[$filename]['name']);
			        // print_r($info);
				$ext = $info['extension']; // get the extension of the file
				//check file exists
				$file_index = 0;
				do{
					$newname = "$filename".($file_index++ == 0 ? "." : "_".$file_index.".").$ext;
					$target = ".".KEY_BASE_URL_GALLERY.'/'.$newname;
				}while(is_file($target));
				//echo "1_".$_FILES[$filename]['name']."  \n2.".$filename."  \n3.".$newname."  \n4.".$ext."\n";
				$target = ".".KEY_BASE_URL_GALLERY.'/'.$newname;
				$value = "'$newname'";
				move_uploaded_file($_FILES[$filename]['tmp_name'], $target);
			}
			if(in_array($colName, $nonUpdatableItems)){
				//skip id, thumb
				continue;
			}
			array_push($colNamesAndValuesToUpdate, " $colName = $value ");
		}
		if(sizeof($colNamesAndValuesToUpdate) == 0){
			// check if 
			continue;
		}
		// delete existing thumb
		$query = "UPDATE ".TAB_GALLERY." SET ".implode(", ", $colNamesAndValuesToUpdate)." WHERE ".COL_ID." = $id";
		//echo $query;
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			$row_status[$id]["status"] = true;
			//$row_status[COL_FILENAME] = $newname;
			$oldFilePath = ".".KEY_BASE_URL_GALLERY.'/'.$oldFile;
			if($oldFile != null && is_file($oldFilePath)){
				unlink($oldFilePath);
				/*if(!unlink($oldFilePath))
					echo "del failed : $oldFilePath";
				else	echo "del success: $oldFilePath";
				*/
	
			}
			$stub = array();
			$stub["image_id"] = $id;
			$stub["image_name"] = $newname;
			array_push($row_status[$id]["gallery_images"], $stub);	
		} else
			$row_status[$id] = false;
		
	}
	return $row_status;
}	
	
		
	
public function deleteGalleryImages($data){
                 
                $id = $data[COL_ID];
                $query = "SELECT * FROM ".TAB_GALLERY." WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		

		
		if($result){
            if($result->num_rows == 0)
            
            return false;
        }else {
            return false;
        } 
		
		$query = "
			UPDATE ".TAB_GALLERY."
			SET ".COL_IS_DELETED." = NOW() WHERE ".COL_ID." = $id";
		$result = $this->mysqli->query($query);
		if(!$this->mysqli->connect_errno){
			return true;
		}
		return false;
	}
	
	private function fileUploadData($filename, $baseURL){
		$info = pathinfo($_FILES[$filename]['name']);
		$ext = $info['extension']; // get the extension of the file
		//check file exists
		$file_index = 0;
		$target = "";
		$newname = "";
		do{
			$newname = "$filename".($file_index++ == 0 ? "." : "_".$file_index.".").$ext;
			$target = ".".KEY_BASE_URL_EVENTS.'/'.$newname;
		}while(is_file($target));
		
		$target = ".".$baseURL.'/'.$newname;
		
		move_uploaded_file($_FILES[$filename]['tmp_name'], $target);
		return $newname;
	}
//----------------	
	
					
			
	

}
?>