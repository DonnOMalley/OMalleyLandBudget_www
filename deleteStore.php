<html>
<head>
<title>Delete Store</title>
</head>
<body>

<center>

<?php

	include 'includes/common.php';
	include 'includes/database.php';
	include 'includes/classes.php';

	// Connect to server and select database.
	$mysqli = new mysqli("$HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME");

	//has the form been submitted?
	if((isset($_POST['payee_id'])) && ($_POST['payee_id'] > 0)) {
		//form submitted
		$payee_id = $_POST['payee_id'];
		
		$sql = "UPDATE " . $TABLE_STORES . " SET " . $STORE_ACTIVE_STATUS . " = " . $ACTIVE_STATUS_INACTIVE . ", updatedTimestamp=UTC_TIMESTAMP where id=" . $payee_id;
	
		if($mysqli->query($sql)) {
			echo "<h1>Payee Removed</H1>";
		}	
		else {
			echo $mysqli->error . "<br />";
			echo "Error Removing Payee (" . $payee_id . ") <br />" . $sql . "<br />";
		}	
	}
	
	echo "<H3> Select a Payee to Remove</H3>";
	
	//Create Form for entering debit data
	echo "<form name='removeStoreForm' method='post' action='deleteStore.php'>";
	
	//get list of Payees for combo box
	$query_stores = "SELECT id, name FROM Payees ORDER BY case name when 'Other' then 'aaaaa' else name end asc;";
	
	//attempt query AND evaluate for success
	if($result = $mysqli->query($query_stores)) {
		if($mysqli->affected_rows > 0) {
			//create combo box with results
			echo "<div class='styled-select'><select name='payee_id'>";
			echo "<option value=0>[SELECT PAYEE]</option>";
			
			//loop through payees and create option tag
			while ($row = $result->fetch_array(MYSQLI_BOTH)) {
				echo "<option value=" . $row[id] . ">" . $row[name] . "</option>";
			}
			
			//end the combo box
			echo "</select></div>";
		}
		else {
			//create combo box with no results
			echo "<div class='styled-select'><select name='payee_id'>";
			echo "<option>-- NO PAYEES --</option>";
			echo "</select></div>";
		}
	}			
	$mysqli->close();
?>
<input type='submit' id=submitButton value='Remove' />
</form>
</center>
</body>
</html>

