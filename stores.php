<?php

	include 'includes/common.php';
	include 'includes/database.php';
	include "includes/classes.php";

	if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['json'])) {
		//Get Username and password as posted to the page
		$username = $_POST['username'];
		$password = $_POST['password'];
		$json			= $_POST['json'];
		$jsonResult = new StoreJSONGetResult();
		
		$validateUser = new UserValidation();
		if($validateUser->authenticateUser($username, $password)) {
			$mysqli = new mysqli("$HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME");
			date_default_timezone_set("UTC"); //Set timezone to UTC
			$jsonDecoded = json_decode($json);
			$requesttype = $jsonDecoded->type;
			if($requesttype==$HTTP_TYPE_POST) {
				$postUser = $jsonDecoded->user;
				if($postUser==$username) {
					$storeArray = $jsonDecoded->storeArray;
					if(count($storeArray)) {
						//build the insert ignore into statment
						$lastUpdate = date('Y-m-d H:i:s');
						$sql = "INSERT IGNORE INTO " . $DB_NAME . "." . $TABLE_STORES . "(" . $STORE_NAME . "," . $STORE_ACTIVE_STATUS . ", updatedTimestamp)VALUES";
						$values = "";
						foreach ($storeArray as $storeItem) {
							if(strlen($values)) {
								$values .= ",('" . $storeItem->name . "','" . $storeItem->activeStatus . "','" . $lastUpdate . "')";
							}
							else {
								$values .= "('" . $storeItem->name . "','" . $storeItem->activeStatus . "','" . $lastUpdate . "')";
							}
						}		
						$values .= ";";
						$sql .= $values;						
			
						//Connect to Database to insert stores
						if($mysqli->query($sql)) {
							$jsonResult->result = $HTTP_RESPONSE_RESULT_SUCCESS;
						}		
					}			
				}
			}
			elseif($requesttype==$HTTP_TYPE_GET) {
				$postUser = $jsonDecoded->user;
				if($postUser==$username) {
					$whereClause = "";
					//Get List of stores since last update timestamp
					if(isset($_POST['lastUpdated'])) {
						$lastUpdate = $_POST['lastUpdated'];
						$whereClause = " WHERE updatedTimestamp >= '" . $lastUpdate . "'";
					}
				
					//Connect to Database to get stores
					// if successfully insert data into database, displays message "Successful". 
					$sql = "SELECT * FROM " . $DB_NAME . "." . $TABLE_STORES . $whereClause . ";";
					$result = $mysqli->query($sql);
				
					if($mysqli->affected_rows > 0) {
					
						while($row = $result->fetch_array(MYSQLI_BOTH)){
							$store = new Store();
							$store->id = $row[$STORE_ID];
							$store->name = $row[$STORE_NAME];
							$store->activeStatus = $row[$STORE_ACTIVE_STATUS];
							array_push($jsonResult->storeArray, $store);
						}
					}
					// close connection 
					$jsonResult->result = $HTTP_RESPONSE_RESULT_SUCCESS;
				}
			}
			elseif($requesttype==$HTTP_TYPE_VERIFY) {
				$postUser = $jsonDecoded->user;
				if($postUser==$username) {
					$storeArray = $jsonDecoded->storeArray;
					if(count($storeArray)) {
						$whereClause = "";
						//Get List of stores to verify
						if(isset($_POST['lastUpdated'])) {
							$lastUpdate = $_POST['lastUpdated'];
							$whereClause = " WHERE updatedTimestamp >= '" . $lastUpdate . "'";
						}
						$storeValues = "";
						foreach ($storeArray as $storeItem) {
							$storeName = $storeItem->name;
							if(strlen($whereClause) && (strlen($storeValues)==0)) {
								$storeValues = "'" . $storeName . "'";
								$storeValues .= " and name in (" . $storeValues;
							}
							else if(strlen($storeValues) == 0){
								$storeValues = "'" . $storeName . "'";
								$storeValues .= " WHERE name in (" . $storeValues;
							}
							else {
								$storeValues .= ",'" . $storeName . "'";
							}
						}	
						if(strlen($storeValues)) {
							$whereClause .= $storeValues;
						}
						
						// if successfully insert data into database, displays message "Successful". 
						$sql = "SELECT * FROM " . $DB_NAME . "." . $TABLE_STORES . $whereClause . ";";
						$result = $mysqli->query($sql);
				
						if($mysqli->affected_rows > 0) {
					
							while($row = $result->fetch_array(MYSQLI_BOTH)){
								$store = new Store();
								$store->id = $row[$STORE_ID];
								$store->name = $row[$STORE_NAME];
								$store->activeStatus = $row[$STORE_ACTIVE_STATUS];
								array_push($jsonResult->storeArray, $store);
							}
						}
					}
					$jsonResult->result = $HTTP_RESPONSE_RESULT_SUCCESS;
				}
				// close connection 
				$mysqli->close();	
			}
		}
	}
	echo json_encode($jsonResult);
?>
