<html>
	<body>
	<?php

		$host="localhost"; // Host name 
		$username="web_user"; // Mysql username 
		$password="Cinderella"; // Mysql password 
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
			$sql="INSERT INTO $tbl_name(name)VALUES('" . mysql_real_escape_string($name) . "')";
			$result=mysql_query($sql);

			// if successfully insert data into database, displays message "Successful". 
			if($result){
				echo "Successfully Added Category: " . $name . "<br />";
			}

			else {
				echo "ERROR ADDING CATEGORY";
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
