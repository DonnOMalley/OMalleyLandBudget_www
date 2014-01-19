<html>
<head>
<title>Create Debit</title>
<link rel="stylesheet" href="/budget/css/createDebit.css" />
<script type="text/javascript">
function checkAmount(strAmount)
{
   var validChars = "0123456789.";
   var isNumber=true;
   var amountChar;
   var decimalPos
 
	decimalPos = 99999;
   for (i = 0; i < strAmount.length && isNumber == true; i++) { 
      amountChar = strAmount.charAt(i); 
      if(amountChar==".") {
      	decimalPos = i;
      }
      
      if(i > (decimalPos + 2)) {
      	isNumber = false;
      	break;
      }
      else if (validChars.indexOf(amountChar) == -1) {
         isNumber = false;
         break;
      }
   }
   return isNumber;   
}
   
function validateForm()
{  
	var amount=document.forms["createDebitForm"]["amount"].value;
	var categoryId=document.forms["createDebitForm"]["category_id"].value;
	var payeeId=document.forms["createDebitForm"]["payee_id"].value;
	var userId=document.forms["createDebitForm"]["user_id"].value;
  
	if(categoryId==null || categoryId == 0) {
		alert("Category Must Be Selected");
		return false;
  }
  
	if(userId==null || userId == 0) {
		alert("A Purchaser Must Be Selected");
		return false;
  }
		
	if(amount==null || amount==0) {
		alert("A Debit Amount Must Be Entered");
		return false;
  }
    
  var strAmount = amount.toString(); 
  if (!checkAmount(strAmount)) {
		alert("Invalid Amount Entered. Amount must be in the form of #.## with up to 6 digits preceeding the decimal place");
		return false;
  }
  
	if(payeeId==null || payeeId == 0) {
		alert("A Payee Must Be Selected");
		return false;
  }
}
</script>
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
	
	//Create Form for entering debit data
	echo "<form name='createDebitForm' method='post' action='createDebit.php' onsubmit='return validateForm()'>";
	
	//get list of categories for combo box
	$query_categories = "SELECT id, name FROM " . $TABLE_CATEGORIES . " WHERE " . $CATEGORY_ACTIVE_STATUS . " = " . $ACTIVE_STATUS_ACTIVE . " ORDER BY name ASC;";
	
	//attempt query AND evaluate for success
	if($result = $mysqli->query($query_categories)) {
		if($mysqli->affected_rows > 0) {
			//create combo box with results
			echo "<div class='styled-select'><select name='category_id'>";
			echo "<option value=0>[SELECT CATEGORY]</option>";
			
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
<div id="inputArea">
<br />Debit Date (yyyy-mm-dd)<br /><input type='text' name='debit_date' value="<?php echo date('Y-m-d'); ?>"/>
<?php	
	//get list of Purchasers for combo box
	$query_purchasers = "SELECT id, username FROM " . $TABLE_USERS . ";";
	
	//attempt query AND evaluate for success
	if($result = $mysqli->query($query_purchasers)) {
		if($mysqli->affected_rows > 0) {
			//create combo box with results
			echo "<div class='styled-select'><select name='user_id'>";
			echo "<option value=0>[SELECT PURCHASER]</option>";
			
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
<br />Amount ($#####.##)<br /><input type='text' name='amount' /></div><div id='inputArea2'>
<?php	
	//get list of Purchasers for combo box
	$query_stores = "SELECT id, name FROM " . $TABLE_STORES . " WHERE " . $STORE_ACTIVE_STATUS . " = " . $ACTIVE_STATUS_ACTIVE . " ORDER BY case name when 'Other' then 'aaaaa' else name end asc;";
	
	//attempt query AND evaluate for success
	if($result = $mysqli->query($query_stores)) {
		if($mysqli->affected_rows > 0) {
			//create combo box with results
			echo "<div class='styled-select'><select name='payee_id'>";
			echo "<option value=0>[SELECT PAYEE]</option>";
			
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
	$mysqli->close();
?>
<br />Comment<br /><input type='text' name='comment' /></div>
<br />

<input type='submit' id=submitButton value='Post Debit' />
</form>
</center>
</body>
</html>

