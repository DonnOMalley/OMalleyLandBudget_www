<html>
	<head>
		<title>Jeni O'Malley's Web Page	</title>
	</head>
	<body>
	<center><H1> Jeni O'Malley's <br /> Art Store<H1><center>
	
	<?php
	
		//get the image and readme from the query parameters
		$htmlImage = $_GET["img"];
		$readme = $_GET["readme"];
			 
		/* Create the Table for holding the Image and information */ 
		echo "<table border=0 cellpadding=3><tr><td align='center'>";
		echo "<img src='". $htmlImage ."' height='640' width='480' /></td></tr>";				
		$readme_txt = file_get_contents($readme);	
		if(strlen($readme_txt)==0) {
			echo "<tr><td valign='bottom' align='center'>No Information</td>";
		}
		else {
			echo "<tr valign=center><td>" . $readme_txt . "</td>";				
		}
		echo "</tr></table>";	//complete the outer table and last row.	 
?>
		</center>
	</body>	
</html>
