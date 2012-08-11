<html>
	<head>
		<title>Jeni O'Malley's Web Page	</title>
	</head>
	<body>
	<center><H1> Jeni O'Malley's <br /> Art Store<H1><center>
	

<?php
		//Initialize helper varialbes
		$file_parts=array();	//Array for holidng the name split on the periods(.)
		$ext='';  //will hold the file extension for comparison with allowed types
		$readme = ""; //name of readme file that holds art information
		$column = 0;  //for keeping track of number of columns created
		$MAX_COLUMNS = 4;	//Max Number of columns to use for the HTML <Table>
		$orig_directory = "art";    																	//image and text folder inside of the relative web path		
		$allowed_types= array('jpg','jpeg','gif','png'); //Only support web images 
		 
		$dir_handle = @opendir($orig_directory);  //open the directory with the images
		if ($dir_handle > 1) { //Check to make sure the folder opened 
			 
			/* Initialize the column count to allow a nice table and then 
					Create the beginning of the Table HTML */ 
			echo "<table border=0 cellpadding=3><tr>";

			//Loop through all files in the 
			while ($file = @readdir($dir_handle))
			{
					//echo "<br />checking file: " . $file;
					/* Skipping the system files: */
					if($file=='.' || $file == '..') continue;
			 
					$file_parts = explode('.',$file);    //This splits the file name at the periods(.)
					$ext = strtolower(array_pop($file_parts)); //convert the extension to lowercase for easy comparison while 'pop'ing the extension out of the array
					 
					/* If the file extension is allowed: */
					if(in_array($ext,$allowed_types)) {
						
						$source = $orig_directory . "/" . $file;
						$readme = explode(".", $file);
						$readme = $readme[0] . ".txt";
						
						$readme = $orig_directory . "/" . $readme;
											
						//if $column count has reached 4, reset and start a new row
						if ($column >= $MAX_COLUMNS) {
							$column = 0;
							echo "</tr><tr>"; //complete last row and create the start of a new one.
						} 
						//create new column with an inner table for holding the image and information from associated .txt file.
						//The readme and image file paths are passed to the viewimage page for better detail.
						echo "<td width='25%'><table  width='100%'><tr><td valign='bottom' align='center'><a href='viewimage.php?img=". urlencode($source) ."&readme=" . urlencode($readme) . "' target='_blank'><img src='". $source ."' height='75' width='75' /></a></td></tr>";				
						$readme_txt = file_get_contents($readme);	
						if(strlen($readme_txt)==0) {
							//echo "0";
							echo "<tr><td valign='bottom' align='center'>No Information</td></tr></table></td>";
						}
						else {
							echo "<tr valign=center><td>" . $readme_txt . "</td></tr></table></td>";				
						}
						$column += 1;
					}
			}
			 
			/* Closing the opened directory */
			@closedir($dir_handle);
			
			echo "</tr></table>";	//complete the outer table and last row.
		 
		}
?>
	</center>
	</body>	
</html>
