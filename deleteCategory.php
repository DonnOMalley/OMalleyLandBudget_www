<html>
<head>
<title>Delete Category</title>
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
	if((isset($_POST['category_id'])) && ($_POST['category_id'] > 0)) {
		//form submitted
		$category_id = $_POST['category_id'];
		
		$sql = "UPDATE " . $TABLE_CATEGORIES . " SET " . $CATEGORY_ACTIVE_STATUS . " = " . $ACTIVE_STATUS_INACTIVE . " updatedTimestamp=UTC_TIMESTAMP where id=" . $category_id;
	
		if(mysql_query($sql)) {
			echo "<h1>Category Removed</H1>";
		}	
		else {
			echo "Error Removing Category <br /><br />";
		}	
	}
	
	echo "<H3> Select a Category to Remove</H3>";
	
	//Create Form for removing categories
	echo "<form name='removeCategoryForm' method='post' action='deleteCategory.php'>";
	
	//get list of categories for combo box
	$query_categories = "SELECT id, name FROM " .  $TABLE_CATEGORIES . " WHERE " . $CATEGORY_ACTIVE_STATUS . " = " . $ACTIVE_STATUS_ACTIVE . " ORDER BY name ASC;";
	
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
	$mysqli->close();
?>
<input type='submit' id=submitButton value='Remove' />
</form>
</center>
</body>
</html>

