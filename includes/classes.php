<?php
	
	//Classes to support JSON interaction with MySQL
	//properties must be public for JSON encoding to work.

	class LogWriter {
		private $fileName = "";
		
		function __construct($logFile) {
			if($logFile != "") {
				$this->fileName = $logFile;
			}
			else {
				$this->fileName = '/var/www/OMalleyLand.log';
			}
		}
		
		function writeLog($msg, $overwrite) {
			if(($this->fileName != "") && ($msg != "")) {
				if($overwrite) {
					file_put_contents($this->fileName, $msg . "\r\n");
				}
				else {
					file_put_contents($this->fileName, $msg . "\r\n", FILE_APPEND | LOCK_EX);
				}
			}
		}
	}
	
	class Category {
		public $id = -1;
		public $name = "";
		public $activeStatus = 0;
	}

	class Store {
		public $id = -1;
		public $name = "";
		public $activeStatus = 0;
	}

	class Debit {
		public $purchaser_id = -1;
		public $category_id = -1;
		public $store_id = -1;
		public $debit_date = "";
		public $amount = "";
		public $comment = "";
		public $entry_on = "";
		public $client_id = -1;
	}

	class JSONResult {
		public $result = "FAIL";
		public $syncTimeStamp;

		function __construct() {
			$this->syncTimeStamp = date("Y-m-d H:i:s");
		}
	}

  class CategoryJSONGetResult {
		public $result = "FAIL";
		public $categoryArray;
		
		function __construct() {
			$this->categoryArray = array();
		}		
	}

  class StoreJSONGetResult {
		public $result = "FAIL";
		public $storeArray;		
		
		function __construct() {
			$this->storeArray = array();
		}
	}

  class DebitJSONGetResult {
		public $result = "FAIL";
		public $debitArray;		
		
		function __construct() {
			$this->debitArray = array();
		}
	}	

	class UserValidation {
		function authenticateUser($username, $password) {
			include 'includes/database.php';
			$userAuthLog = new LogWriter("/var/www/userAuth.log");
			$userAuthLog->writeLog("Authenticating " . $username . "...", 1);		
			
			$validUser = false;	
			// Connect to server and select database.
			$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME); 
			$userAuthLog->writeLog("DB Connected ", 0);

			// build query to get previous hash value from Database
			$sql="SELECT password_hash FROM " . $DB_NAME . "." . $TABLE_USERS . " where username = '" . $mysqli->real_escape_string($username)  . "'";

			$userAuthLog->writeLog("SQL = " . $sql, 0);
			
			//attempt query AND evaluate for success
			$result = $mysqli->query($sql);
			$userAuthLog->writeLog("DB Queried", 0);
			$userAuthLog->writeLog("Num Rows = " . $mysqli->affected_rows, 0);

			//should only have 1 row returned - username is a unique field
			if($mysqli->affected_rows == 1) {
				$userAuthLog->writeLog("DB Results=1", 0);

				//Assign the password_hash value associated to the username
				while ($row = $result->fetch_array(MYSQLI_BOTH)) {
					$dbHash = $row['password_hash'];
				}
	
				//if the password posted is confirmed, create a new hash
				if (crypt($password, $dbHash) == $dbHash ) {
					$userAuthLog->writeLog("DB Password Matches", 0);
					//Only process records if user can be validated
					$validUser = true;
				}
				else {			
					$userAuthLog->writeLog("Password MISMATCH", 0);
				}	
			}
			else {			
				$userAuthLog->writeLog("Too many(or none) users", 0);
			}
			// close connection 
			$mysqli->close();			
			return $validUser;
		}
		
		function getUserID($username) {
			include 'includes/database.php';
			$userID = -1;
			$mysqli = new mysqli("$DB_HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME"); 

			// build query to get previous hash value from Database
			$sql="SELECT id FROM " . $DB_NAME . "." . $TABLE_USERS . " where username = '" . $mysqli->real_escape_string($username)  . "'";

			//attempt query AND evaluate for success
			if($result = $mysqli->query($sql)) {

				//should only have 1 row returned - username is a unique field
				if($mysqli->affected_rows == 1) {
	
					//Assign the user id value associated to the username
					while ($row = $result->fetch_array(MYSQLI_BOTH)) {
						$userID = $row['id'];
					}
				}
			}
			$mysqli->close();	
			return $userID;
		}
	}
?>
