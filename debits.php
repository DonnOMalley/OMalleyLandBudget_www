<?php

	include 'includes/common.php';
	include 'includes/database.php';
	include 'includes/classes.php';

	if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['json'])) {
		
		//Get Username and password as posted to the page
		$username = $_POST['username'];
		$password = $_POST['password'];
		$json			= $_POST['json'];
		$jsonResult = new DebitJSONGetResult();
		$debitLog = new LogWriter("/var/www/debit.log");
		$debitLog->writeLog($json, 1);
		
		$validateUser = new UserValidation();
		if($validateUser->authenticateUser($username, $password)) {
			date_default_timezone_set("UTC"); //Set timezone to UTC
			$userID = $validateUser->getUserID($username);
			$jsonDecoded 	= json_decode($json);
			$requesttype 	= $jsonDecoded->type;
			$postUser 		= $jsonDecoded->user;
			//$postUserID		= $jsonDecoded->userID;
			
			//echo "user validated :: " .  $postUser . "=" . $userID;
			
			if($userID > -1) {
				$mysqli = new mysqli("$HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME");
				//echo "user authenticated<br><br>";
				if($requesttype==$HTTP_TYPE_POST) {
				//{"type":"post","user":"DonnO","userID":"1","debitArray":[{"name":"categoryName","activeStatus":"1"},{"name":"categoryName2","activeStatus":"1"}]}
												
					$debitArray = $jsonDecoded->debitArray;
					if(count($debitArray)) {
						//build the insert ignore into statment
						$entryOn = date('Y-m-d H:i:s');
						$sql = "INSERT IGNORE INTO " . $DB_NAME . "." . $TABLE_DEBITS . "(user_id, category_id, payee_id, debit_date, amount, comment, entry_on, client_id)VALUES";
						$values = "";
						$whereClause = " WHERE (user_id = '" . $userID . "')";
						$clientIDs = "";
						foreach ($debitArray as $debitItem) {
							$debitLog->writeLog('', 0);
							$debitLog->writeLog('Entry On=' . $entryOn, 0);
							$debitLog->writeLog('useriD=' . $userID, 0);
							$debitLog->writeLog('Server_Category_ID=' . $debitItem->serverCategoryID, 0);
							$debitLog->writeLog('Server Store ID=' . $debitItem->serverStoreID, 0);
							$debitLog->writeLog('Debit Date=' . $debitItem->debitDate, 0);
							$debitLog->writeLog('Debit Amount=' . $debitItem->debitAmount, 0);
							$debitLog->writeLog('Comment=' . $mysqli->real_escape_string($debitItem->comment), 0);
							$debitLog->writeLog('client_id=' . $debitItem->client_id, 0);
						
							if(strlen($values)) {
								$values .= ",('" . $userID . "','" . $debitItem->serverCategoryID . "','" . $debitItem->serverStoreID . "','" . $debitItem->debitDate . "','" . $debitItem->debitAmount . "','" . $mysqli->real_escape_string($debitItem->comment) . "','" . $entryOn . "','" . $debitItem->client_id . "')";
							}
							else {
								$values .= "('" . $userID . "','" . $debitItem->serverCategoryID . "','" . $debitItem->serverStoreID . "','" . $debitItem->debitDate . "','" . $debitItem->debitAmount . "','" . $mysqli->real_escape_string($debitItem->comment) . "','" . $entryOn . "','" . $debitItem->client_id . "')";
							}							
							
							if(strlen($clientIDs)) {
								$clientIDs .= ",'" . $debitItem->client_id . "'";
							}
							else {
								$clientIDs .= " and (client_id in ('" . $debitItem->client_id . "'";
							}							
						}		
						$whereClause .= $clientIDs . "))";
						$values .= ";";
						$sql .= $values;		
						
						//echo "SQL = " . $sql . "<BR><BR>";		
						//echo "Where Clause = " . $whereClause;	
		
						//Only Attempt insert if IDs have been sent
						if(strlen($clientIDs)) {		
							//Connect to Database to insert categories
							
							//echo "Insert SQL = " . $sql;
							
							if($mysqli->query($sql)) {
								$jsonResult->result = $HTTP_RESPONSE_RESULT_SUCCESS;
							
								//echo "Rebuilding SQL String";		
						
								//populate json array to tell the client what data was posted if insert succeeded
								$sql = "SELECT * FROM " . $DB_NAME . "." . $TABLE_DEBITS . $whereClause . ";";
								//echo "SQL = " . $sql;
								
								$debitLog->writeLog('SQL :: ' . $sql, 0);
								$result = $mysqli->query($sql);
		
								if($mysqli->affected_rows > 0) {
									$debitLog->writeLog('Rows were inserted', 0);
									//echo "Rows Found to return";
			
									while($row = $result->fetch_array(MYSQLI_BOTH)){
										$debitLog->writeLog('Writing Debit Record to Result JSON', 0);
										$debit = new Debit();
										$debit->purchaser_id = $row['user_id'];
										$debit->category_id = $row['category_id'];
										$debit->store_id = $row['payee_id'];
										$debit->debit_date = $row['debit_date'];
										$debit->amount = $row['amount'];
										$debit->comment = $row['comment'];
										$debit->entry_on = $row['entry_on'];
										$debit->client_id = $row['client_id'];
										array_push($jsonResult->debitArray, $debit);
									}
								}
								else {	
									$debitLog->writeLog('No rows were inserted', 0);
								}
							}	
						}
						// close connection 
						$mysqli->close();	
					}		
				}
				elseif($requesttype==$HTTP_TYPE_GET) { //No need to actual sync up Debits so just return true
					$jsonResult->result = $HTTP_RESPONSE_RESULT_SUCCESS;
				}
			}
		}
	}
	$debitLog->writeLog('Result JSON ::' . json_encode($jsonResult), 0);
	echo json_encode($jsonResult);
?>
