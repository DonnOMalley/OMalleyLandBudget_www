<html>
<head>
<title>Create Debit</title>

<link rel="stylesheet" href="/css/createDebit.css" />

</head>
<body>

<center>

<?php

	include 'includes/common.php';
	include 'includes/database.php';
	include 'includes/classes.php';

// Connect to server and select database.
	$mysqli = new mysqli("$DB_HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME"); 

	//has the form been submitted?
	if((isset($_POST['category_id'])) && ($_POST['category_id'] > 0)) {
		//form submitted
		$debit_date = $_POST['debit_date'];
		$user_id = $_POST['user_id'];
		$category_id = $_POST['category_id'];
		$amount = $_POST['amount'];
		$payee_id = $_POST['payee_id'];
		$comment = $_POST['comment'];
		
		$sql = "INSERT into " . $TABLE_DEBITS . " (debit_date, category_id, amount, comment, user_id, payee_id) VALUES ('" . $debit_date . "', '" . $category_id . "', '" . $amount . "', '" . $mysqli->real_escape_string($comment) . "', '" . $user_id . "', '" . $payee_id . "')";
	
		if($mysqli->query($sql)) {
			echo "<h1>DEBIT ENTERED</H1>";
			echo "$" . $amount . " Spent on " . $debit_date;
		}	
		else {
			echo "Error Entering Debit <br /><br />";
		}	
	}
?>
	<form name="createDebitForm" method="post" action="createDebit.php" onsubmit="return validateForm();">
	<br />

<table>
	<thead>
	<th colspan=2>
		<h3>Create Debit</h3>
	</th>
	</thead>
	<tbody>
		<tr>
			<th align=right>Debit Date:</th>
			<td>				
				<?php
					date_default_timezone_set('America/Chicago');
					list($m,$d,$y) = explode("/", date('m/dd/Y')); 
					$minDate = "";
					if($m==1) {
						$minDate = ($y - 1) . "-12-01";
					}
					else {
						if($m < 11) {
						$minDate = $y . "-0" . ($m-1) . "-01";
						}
						else {
						$minDate = $y . "-" . ($m-1) . "-01";
						}
					}
					echo "<input type='date' name='debit_date' id='debit_date' min='" . $minDate . "' required value='" . date('Y-m-d') . "'/>";
				?>
			</td>
		</tr>
		<tr>
			<th align=right>Category:</th>
			<td>
				<?php			
					//get list of categories for combo box
					$query_categories = "SELECT id, name FROM " . $TABLE_CATEGORIES . " WHERE " . $CATEGORY_ACTIVE_STATUS . " = " . $ACTIVE_STATUS_ACTIVE . " ORDER BY name ASC;";
	
					//attempt query AND evaluate for success
					if($result = $mysqli->query($query_categories)) {
						if($mysqli->affected_rows > 0) {
							//create combo box with results
							echo "<div class='styled-select'><select name='category_id' required>";
							echo "<option value=>[SELECT CATEGORY]</option>";
			
							//loop through categories and create option tag
							while ($row = $result->fetch_array(MYSQLI_BOTH)) {
								echo "<option value=" . $row[id] . ">" . $row[name] . "</option>";
							}
			
							//end the combo box
							echo "</select></div>";
						}
						else {
							//create combo box with no results
							echo "<select name='category_id'>n";
							echo "<option>-- NO CATEGORIES --</option>";
							echo "</select>";
						}
					}			
				?>
			</td>
		</tr>
		<tr>
			<th align=right>Payee:</th>
			<td>
				<?php		
					$query_stores = "SELECT id, name FROM " . $TABLE_STORES . " WHERE " . $STORE_ACTIVE_STATUS . " = " . $ACTIVE_STATUS_ACTIVE . " ORDER BY case name when 'Other' then 'aaaaa' else name end asc;";
	
					//attempt query AND evaluate for success
					if($result = $mysqli->query($query_stores)) {
						if($mysqli->affected_rows > 0) {
							//create combo box with results
							echo "<div class='styled-select'><select name='payee_id' required>";
							echo "<option value=>[SELECT PAYEE]</option>";
			
							//loop through categories and create option tag
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
				?>
			</td>
		</tr>
		<tr>
			<th align=right>Purchaser:</th>
			<td>
				<?php		
					//get list of Purchasers for combo box
					$query_purchasers = "SELECT id, username FROM " . $TABLE_USERS . ";";
	
					//attempt query AND evaluate for success
					if($result = $mysqli->query($query_purchasers)) {
						if($mysqli->affected_rows > 0) {
							//create combo box with results
							echo "<div class='styled-select'><select name='user_id' required>";
							echo "<option value=>[SELECT PURCHASER]</option>";
			
							//loop through categories and create option tag
							while ($row = $result->fetch_array(MYSQLI_BOTH)) {
								echo "<option value=" . $row[id] . ">" . $row[username] . "</option>";
							}
			
							//end the combo box
							echo "</select></div>";
						}
						else {
							//create combo box with no results
							echo "<div class='styled-select'><select name='user_id'>";
							echo "<option>-- NO PURCHASERS --</option>";
							echo "</select></div>";
						}
					}	
				?>
			</td>
		</tr>
		<tr>
			<th align=right>Amount:</th>
			<td>
				<b>$</b><input type='number' name='amount' required min='0.01' max='9999.99' step="0.01"/>
			</td>
		</tr>
		<tr>
			<th align=right>Comment:</th>
			<td><input type='text' name='comment' /></td>
		</tr>
	</tbody>
</table>

<?php
	$mysqli->close();
?>

<input type='submit' id=submitButton value='Post Debit' />
</form>
</center>
</body>
</html>

