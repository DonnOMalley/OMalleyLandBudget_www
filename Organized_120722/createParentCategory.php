<html>
<body>
<?php

$host="192.168.2.113"; // Host name 
$username="root"; // Mysql username 
$password="Dreck1030J"; // Mysql password 
$db_name="OMalleyLandBudget"; // Database name 
$tbl_name="Categories"; // Table name

//has the form been submitted?
if(isset($_POST['name'])) {
	//form submitted
	$name=$_POST['name'];	

	// Connect to server and select database.
	mysql_connect("$host", "$username", "$password")or die("cannot connect"); 
	mysql_select_db("$db_name")or die("cannot select DB");

	// Insert data into mysql 
	$sql="INSERT INTO $tbl_name(name)VALUES('$name')";
	$result=mysql_query($sql);

	// if successfully insert data into database, displays message "Successful". 
	if($result){
		echo "Successful";
		echo "<BR>";
		echo "<a href='phptest.php'>Back to list page</a>";
	}

	else {
		echo "ERROR";
	}

	// close connection 
	mysql_close();	
}

?>

<form action="createParentCategory.php" method="post">
Name: <input type="text" name="name" />
<input type="submit" />
</form>

</body>
</html>
