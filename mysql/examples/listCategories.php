<html>
<body>
<table border=1>
<tr>
<td>Category ID</td>
<td>Category</td>
</tr>
<?php
$con = mysql_connect("192.168.2.113","root","Dreck1030J");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

mysql_select_db("OMalleyLandBudget", $con);

$result = mysql_query("SELECT * FROM Categories");

while($row = mysql_fetch_array($result))
  {
  echo "<tr><td>";
  echo $row['id'] . "</td><td>" . $row['name'];
  echo "</td></tr>";
  }

mysql_close($con);
?>
</table>
</body>
</html>
