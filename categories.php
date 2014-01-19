<?php

	include 'includes/common.php';
	include 'includes/database.php';
	include 'includes/classes.php';

	if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['json'])) {
		//Get Username and password as posted to the page
		$username = $_POST['username'];
		$password = $_POST['password'];
		$json			= $_POST['json'];
		$jsonResult = new CategoryJSONGetResult();
		$categoryLog = new LogWriter("/var/www/categories.log");
		$categoryLog->writeLog($json, 1);
		
		$validateUser = new UserValidation();
		if($validateUser->authenticateUser($username, $password)) {
		$categoryLog->writeLog("Valid User", 0);
			date_default_timezone_set("UTC"); //Set timezone to UTC
			$jsonDecoded = json_decode($json);
			$requesttype = $jsonDecoded->type;
			if($requesttype==$HTTP_TYPE_POST) {
				$categoryLog->writeLog("HTTP_POST", 0);
			//{"type":"post","user":"DonnO","categoryArray":[{"name":"categoryName","activeStatus":"1"},{"name":"categoryName2","activeStatus":"1"}]}
				$postUser = $jsonDecoded->user;
				if($postUser==$username) {
					$categoryArray = $jsonDecoded->categoryArray;
					if(count($categoryArray)) {
						//build the insert ignore into statment
						$lastUpdate = date('Y-m-d H:i:s');
						$sql = "INSERT IGNORE INTO " . $DB_NAME . "." . $TABLE_CATEGORIES . "(" . $CATEGORY_NAME . "," . $CATEGORY_ACTIVE_STATUS . ", updatedTimestamp)VALUES";
						$values = "";
						foreach ($categoryArray as $categoryItem) {
							if(strlen($values)) {
								$values .= ",('" . $categoryItem->name . "','" . $categoryItem->activeStatus . "','" . $lastUpdate . "')";
							}
							else {
								$values .= "('" . $categoryItem->name . "','" . $categoryItem->activeStatus . "','" . $lastUpdate . "')";
							}
						}		
						$values .= ";";
						$sql .= $values;						
			
						//Connect to Database to insert categories
						$mysqli = new mysqli("$DB_HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME"); 
						if($mysqli->query($sql)) {
							$jsonResult->result = $HTTP_RESPONSE_RESULT_SUCCESS;
						}		
						// close connection 
						$mysqli->close();	
					}			
				}
			}
			elseif($requesttype==$HTTP_TYPE_GET) {
				$categoryLog->writeLog('HTTP_GET', 0);
				$postUser = $jsonDecoded->user;
				if($postUser==$username) {
					$whereClause = "";
					//Get List of categories since last update timestamp
					if(isset($_POST['lastUpdated'])) {
						$lastUpdate = $_POST['lastUpdated'];
						$whereClause = " WHERE updatedTimestamp >= '" . $lastUpdate . "'";
					}
				
					//Connect to Database to get categories
					$mysqli = new mysqli("$DB_HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME"); 
					// if successfully insert data into database, displays message "Successful". 
					$sql = "SELECT * FROM " . $DB_NAME . "." . $TABLE_CATEGORIES . $whereClause . ";";
					$result = $mysqli->query($sql);
				
					if($mysqli->affected_rows > 0) {
					
						while($row = $result->fetch_array(MYSQLI_BOTH)){
							$category = new Category();
							$category->id = $row[$CATEGORY_ID];
							$category->name = $row[$CATEGORY_NAME];
							$category->activeStatus = $row[$CATEGORY_ACTIVE_STATUS];
							array_push($jsonResult->categoryArray, $category);
						}
					}
					// close connection 
					$mysqli->close();	
					$jsonResult->result = $HTTP_RESPONSE_RESULT_SUCCESS;
				}
			}
			elseif($requesttype==$HTTP_TYPE_VERIFY) {
				$categoryLog->writeLog('HTTP_VERIFY', 0);
				$postUser = $jsonDecoded->user;
				if($postUser==$username) {
					$categoryArray = $jsonDecoded->categoryArray;
					if(count($categoryArray)) {
						$whereClause = "";
						//Get List of categories to verify
						if(isset($_POST['lastUpdated'])) {
							$lastUpdate = $_POST['lastUpdated'];
							$whereClause = " WHERE updatedTimestamp >= '" . $lastUpdate . "'";
						}
						$categoryValues = "";
						foreach ($categoryArray as $categoryItem) {
							$categoryName = $categoryItem->name;
							if(strlen($whereClause) && (strlen($categoryValues)==0)) {
								$categoryValues = "'" . $categoryName . "'";
								$categoryValues .= " and name in (" . $categoryValues;
							}
							else if(strlen($categoryValues) == 0){
								$categoryValues = "'" . $categoryName . "'";
								$categoryValues .= " WHERE name in (" . $categoryValues;
							}
							else {
								$categoryValues .= ",'" . $categoryName . "'";
							}
						}	
						if(strlen($categoryValues)) {
							$whereClause .= $categoryValues;
						}
				
						//Connect to Database to get categories
						$mysqli = new mysqli("$DB_HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME"); 
						// if successfully insert data into database, displays message "Successful". 
						$sql = "SELECT * FROM " . $DB_NAME . "." . $TABLE_CATEGORIES . $whereClause . ";";
						$result = $mysqli->query($sql);
				
						if($mysqli->affected_rows > 0) {
					
							while($row = $result->fetch_array(MYSQLI_BOTH)){
								$category = new Category();
								$category->id = $row[$CATEGORY_ID];
								$category->name = $row[$CATEGORY_NAME];
								$category->activeStatus = $row[$CATEGORY_ACTIVE_STATUS];
								array_push($jsonResult->categoryArray, $category);
							}
						}
						// close connection 
						$mysqli->close();	
					}
					$jsonResult->result = $HTTP_RESPONSE_RESULT_SUCCESS;
				}
			}
		}
		else {
			$categoryLog->writeLog('ERROR VALIDATING USER', 0);
		}
	}
	echo json_encode($jsonResult);
?>
