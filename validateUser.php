<?php

	include 'includes/common.php';
	include 'includes/database.php';
	include "includes/classes.php";

	if(isset($_POST['username']) && isset($_POST['password'])) {
		//Get Username and password as posted to the page
		$username = $_POST['username'];
		$password = $_POST['password'];
		$userID 	= "";

		// Connect to server and select database.
		$mysqli = new mysqli("$HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME");

		// build query to get previous hash value from Database
		$sql = "SELECT id, password_hash FROM " . $TABLE_USERS . " where username = '" . $mysqli->real_escape_string($username)  . "'";

		//attempt query AND evaluate for success
		if($result = $mysqli->query($sql)) {
		
			//should only have 1 row returned - username is a unique field
			if($mysqli->affected_rows == 1) {
				//Assign the password_hash value associated to the username
				while ($row = $result->fetch_array(MYSQLI_BOTH)) {
					$userID = $row['id'];
					$dbHash = $row['password_hash'];
				}
				
				if (crypt($password, $dbHash) == $dbHash) {
					$dbHash = crypt($password); // let the salt be automatically generated
					// Insert data into mysql 
					$sql = "UPDATE " . $TABLE_USERS . " SET password_hash = '" . $mysqli->real_escape_string($dbHash) . "', last_login=UTC_TIMESTAMP where username = '" . $mysqli->real_escape_string($username) . "'";
					$result = $mysqli->query($sql);
					if($mysqli->affected_rows >= 0) {					
						echo $mysqli->real_escape_string($dbHash);			
					}
				}
			}
		}

		// close connection 
		$mysqli->close();	
	}
?>
