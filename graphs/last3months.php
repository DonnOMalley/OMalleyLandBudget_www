<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">

  <title>OMalleyLand Budget - Last 3 Months</title>


<style>
  .graphdiv {
    width: 1300px;
    height: 650px;
  }
  .mobilegraphdiv {
		width: 768px;
		height: 450px;
  }
</style>

<?php 

	include '../includes/common.php';
	include '../includes/database.php';
	include '../includes/classes.php';
	
	list($m,$d,$y) = explode("/", date('m/d/Y')); 
	if(isset($_POST['start_debit_month']) && isset($_POST['start_debit_year']) && isset($_POST['end_debit_month']) && isset($_POST['end_debit_year'])){
		$startMonth = $_POST['start_debit_month'];
		$startYear = $_POST['start_debit_year'];
		$endMonth = $_POST['end_debit_month'];
		$endYear = $_POST['end_debit_year'];
	}
	else if (($_GET['start_debit_month'] != "") && ($_GET['start_debit_year'] != "") && ($_GET['end_debit_month'] != "") && ($_GET['end_debit_year'] != "")){
		$startMonth = $_GET['start_debit_month'];
		$startYear = $_GET['start_debit_year'];
		$endMonth = $_GET['end_debit_month'];
		$endYear = $_GET['end_debit_year'];
		}
	else {
		$startMonth = $m;
		$startYear = $y;
		
		if($startMonth < 12) {
			$endMonth = $startMonth + 1;
			$endYear = $startYear;
		}
		else {
			$endMonth = 1;
			$endYear = $startYear + 1;
		}
	}	
	
	if($_GET['mobile'] == "") {
		$mobile=0;
	}
	else {
		$mobile = $_GET['mobile'];
	}
	
	$startDate = $startYear . "-" . $startMonth . "-1";
	$endDate = $endYear . "-" . $endMonth . "-1";
	
	$mysqli = new mysqli("$DB_HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME");  

  echo "
  	<script type='text/javascript' src='https://www.google.com/jsapi'></script>
		<script type='text/javascript'>
			google.load('visualization', '1', {packages:['corechart']});
			google.setOnLoadCallback(drawChart);
		    
		 	function drawChart() {
	 		 var dataColumnChart = google.visualization.arrayToDataTable([";
			 
			 //Only get list of categories actually used to cut down on processing time
			$result = $mysqli->query("SELECT DISTINCT c.name AS name
									FROM " . $db_name . ".Debits d
									JOIN " . $db_name . ".Categories c ON c.id = d.category_id
									WHERE d.debit_date > DATE_ADD(LAST_DAY(DATE_ADD(NOW(), INTERVAL - 3 MONTH)), INTERVAL 1 DAY)
										AND c.Name <> 'Bills'
									ORDER BY c.name ASC");
									
		    echo "['Month'";
			while($row = $result->fetch_array(MYSQLI_BOTH)) {
				$categoryName = $row['name'];
				if(count($categoryArray) == 0) {
					$categoryArray = array($categoryName);
				}
				else {
					array_push($categoryArray, $categoryName);
				}
				echo ",'" . $categoryName . "'";
			} 
		    echo "]";
			 
			$result = $mysqli->query("SELECT DATE_FORMAT(d.debit_date, '%M %Y') AS Month,
									c.name AS Category,
									sum(d.Amount) AS Total_Spent
									FROM " . $db_name . ".Debits d
									JOIN " . $db_name . ".Categories c ON c.id = d.category_id
									WHERE d.debit_date >= DATE_ADD(LAST_DAY(DATE_ADD(NOW(), INTERVAL - 3 MONTH)), INTERVAL 1 DAY)
										AND c.Name <> 'Bills'
									GROUP BY DATE_FORMAT(d.debit_date, '%m %Y'), c.name
                    				ORDER BY DATE_FORMAT(d.debit_date, '%Y') ASC, DATE_FORMAT(d.debit_date, '%m') ASC, c.name ASC");									
			
			$prevMonth = '';
			$i = 0;
			$curCategoryI = -1;
			$categoryHeader = '';
			while($row = $result->fetch_array(MYSQLI_BOTH)) {
				$curMonth = $row['Month'];
				$curCategory = $row['Category'];
				
				if($prevMonth != $curMonth) {
					if(strlen($prevMonth) > 0) {
						//write any remaining 0's as needed
						while($i < (count($categoryArray) - 1)) {
							echo ",0";
							$i = $i + 1;
						}
					}
					if ($curCategoryI > -1) {
						echo "]"; //write end bracket and comma for previous record
					}
					echo ",['" . $curMonth . "'"; //start a new row for the month, 
					$i=0;
					$curCategoryI = -1;
					$zeroEnabled = 1;
					$categoryHeader = $categoryArray[$i];
					$prevMonth = $curMonth;
				}	
				
				while($categoryHeader != $curCategory) {
					if($curCategoryI==-1){
						//First Category of month not matched
						//Insert a $0 value for that category
						if($zeroEnabled == 1) {
							echo ",0";
						}
						else {
							$zeroEnabled = 1;
						}
						//advance Category Header counter/value 
						$i = $i + 1;
						$categoryHeader = $categoryArray[$i];	
					}
					else if($curCategoryI == $i) {
						//Category from record has advanced to another column
						$curCategoryI = -1; //reset this to unknown to loop through to find match
						$zeroEnabled = 0;
					}
					
				}
				$curCategoryI = $i;
				//Category Header and Current Record Category Match,
				//Write Amount Value from Record
				echo "," . $row['Total_Spent'];	
			}     
			while($i < (count($categoryArray) - 1)) {
				echo ",0";
				$i = $i + 1;
			}  
			echo "]]);
			        var optionsColumnChart = {
			          title: 'Last 3 Months by Category',
			          hAxis: {title: 'Month', titleTextStyle: {color: 'red'}}
			        };
			
			        var chartColumnChart = new google.visualization.ColumnChart(document.getElementById('ColumnChart_div'));
			        chartColumnChart.draw(dataColumnChart, optionsColumnChart);
		}
		</script>";
    
	$mysqli->close();
?>

</head>
<body>

<?php
	if($mobile==1) {
		echo "<div id='ColumnChart_div' class='mobilegraphdiv'></div>";
	}
	else {
		echo "<div id='ColumnChart_div' class='graphdiv'></div>";
	}
?>

</body>
</html>
