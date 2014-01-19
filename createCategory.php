<html>
<head><title>Create Category for OMalleyLand Budget</title></head>
<body>
<center>
<h1>Create a OMalleyLand Budget Category</h1>
<br />
<?php

	include 'includes/common.php';
	include 'includes/database.php';
	include 'includes/classes.php';

//has the form been submitted?
if(isset($_POST['name'])) {
	//form submitted
	$name=$_POST['name'];	

	// Connect to server and select database.
	$mysqli = new mysqli("$DB_HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME"); 

	// Insert data into mysql 
	$sql="INSERT INTO " . $TABLE_CATEGORIES . "(name)VALUES('" . $mysqli->real_escape_string($name) . "')";
	$result = $mysqli->query($sql);

	// if successfully insert data into database, displays message "Successful". 
	if($result){
		echo "Successfully Added Category: " . $name . "<br />";
	}

	else {
		echo "ERROR ADDING CATEGORY " . $name . "<br />";
	}

	// close connection 
	$mysqli->close();	
}

?>
	<form action="createCategory.php" method="post">
		Name: <input type="text" name="name" />
		<input type="submit" />
	</form>
</center>
</body>
</html>
