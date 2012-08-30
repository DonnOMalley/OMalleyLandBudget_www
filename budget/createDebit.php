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
  
	if(categoryId==null || categoryId == 0) {
		alert("Category Must Be Selected");
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
}
</script>
</head>
<body>

<center>

<?php

$host="localhost"; // Host name 
$username="web_user"; // Mysql username 
$password="Cinderella"; // Mysql password 
$db_name="OMalleyLandBudget"; // Database name 

// Connect to server and select database.
mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
mysql_select_db("$db_name")or die("cannot select DB");

	//has the form been submitted?
	if((isset($_POST['category_id'])) && ($_POST['category_id'] > 0)) {
		//form submitted
		$debit_date = $_POST['debit_date'];
		$purchaser_id = $_POST['purchaser_id'];
		$category_id = $_POST['category_id'];
		$amount = $_POST['amount'];
		$store_id = $_POST['store_id'];
		$comment = $_POST['comment'];
		if($_POST['credit_card'] == 'Yes') {
			$credit_card = 1;
		}
		else {
			$credit_card = 0;
		}
		
		$sql = "INSERT into Debits (debit_date, category_id, amount, comment, creditCardPurchase, purchaser_id, store_id) VALUES ('$debit_date', '$category_id', '$amount', '$comment', '$credit_card', '$purchaser_id', '$store_id')";
	
		if(mysql_query($sql)) {
			echo "<h1>DEBIT ENTERED</H1>";
			echo "$" . $amount . " Spent on " . $debit_date;
		}		
	}
	
	//Create Form for entering debit data
	echo "<form name='createDebitForm' method='post' action='createDebit.php' onsubmit='return validateForm()'>";
	
	//get list of categories for combo box
	$query_categories = "SELECT id, name FROM Categories WHERE parent_category_id IS NULL ORDER BY name ASC;";
	
	//attempt query AND evaluate for success
	if($result = mysql_query($query_categories)) {
		if($success = mysql_num_rows($result) > 0) {
			//create combo box with results
			echo "<div class='styled-select'><select name='category_id'>";
			echo "<option value=0>[SELECT CATEGORY]</option>";
			
			//loop through categories and create option tag
			while ($row = mysql_fetch_array($result)) {
				echo "<option value=$row[id]>$row[name]</option>";
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
	$query_purchasers = "SELECT id, firstName, lastName FROM Purchasers;";
	
	//attempt query AND evaluate for success
	if($result = mysql_query($query_purchasers)) {
		if($success = mysql_num_rows($result) > 0) {
			//create combo box with results
			echo "<div class='styled-select'><select name='purchaser_id'>";
			echo "<option value=0>[SELECT PURCHASER]</option>";
			
			//loop through categories and create option tag
			while ($row = mysql_fetch_array($result)) {
				echo "<option value=$row[id]>$row[firstName] $row[lastName]</option>";
			}
			
			//end the combo box
			echo "</select></div>";
		}
		else {
			//create combo box with no results
			echo "<div class='styled-select'><select name='purchaser_id'>";
			echo "<option>-- NO PURCHASERS --</option>";
			echo "</select></div>";
		}
	}	
?>
<br />Amount ($#####.##)<br /><input type='text' name='amount' /></div><div id='inputArea2'>
<?php	
	//get list of Purchasers for combo box
	$query_stores = "SELECT id, name FROM Stores ORDER BY case name when 'Other' then 'aaaaa' else name end asc;";
	
	//attempt query AND evaluate for success
	if($result = mysql_query($query_stores)) {
		if($success = mysql_num_rows($result) > 0) {
			//create combo box with results
			echo "<div class='styled-select'><select name='store_id'>";
			echo "<option value=0>[SELECT PAYEE]</option>";
			
			//loop through categories and create option tag
			while ($row = mysql_fetch_array($result)) {
				echo "<option value=$row[id]>$row[name]</option>";
			}
			
			//end the combo box
			echo "</select></div>";
		}
		else {
			//create combo box with no results
			echo "<div class='styled-select'><select name='store_id'>";
			echo "<option>-- NO PAYEES --</option>";
			echo "</select></div>";
		}
	}		
?>
<br />Comment<br /><input type='text' name='comment' /></div>
<!--<br />Credit Card Purchase<div class="checkbox"><input type="checkbox" name="credit_card" value="Yes" width=250 /></div>-->
<br />

<input type='submit' id=submitButton value='Post Debit' />
</form>
</center>
</body>
</html>

