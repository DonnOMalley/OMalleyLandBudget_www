<html>
<body>
<center>
<?php

	include 'includes/common.php';
	include 'includes/database.php';
	include 'includes/classes.php';

	if(isset($_POST['username']) && isset($_POST['password'])) {
		$username = $_POST['username'];
		$password = $_POST['password'];

		// A higher "cost" is more secure but consumes more processing power
		$cost = 10;

		// Create a random salt
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');

		// Prefix information about the hash so PHP knows how to verify it later.
		// "$2a$" Means we're using the Blowfish algorithm. The following two digits are the cost parameter.
		$salt = sprintf("$2a$%02d$", $cost) . $salt;

		// Hash the password with the salt
		$hash = crypt($password, $salt);

		// Connect to server and select database.
		$mysqli = new mysqli("$HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME");

		// Insert data into mysql 
		$sql="INSERT INTO " . $TABLE_USERS . "(username, password_hash)VALUES('" . $mysqli->real_escape_string($username) . "', '" . $mysql->real_escape_string($hash) . "')";
		$result = $mysqli->query($sql);

		// if successfully insert data into database, displays message "Successful". 
		if($result){
			echo "Successfully Added User: " . $username . "<br />" . "<br />";
		}

		else {
			echo "ERROR ADDING USER :: " . $username . " :: "  . $mysql->error() . "<br />" . "<br />";
		} 

		// close connection 
		$mysqli->close();	
	}
?>
<form name="create_user_form" action="createUser.php" method="post">
	<table>
		<tr>
			<td align=right>Username:</td>
			<td align=left><input type="text" name="username" /></td>
		</tr>
		<tr>
			<td align=right>Password:</td>
			<td align=left><input type="password" name="password" /></td>
		</tr>
		<tr>
			<td colspan=2 align=center><input type="Submit" value="Create" /></td>
		</tr>
	</table>
</form>
</center>
</body>
</html>
