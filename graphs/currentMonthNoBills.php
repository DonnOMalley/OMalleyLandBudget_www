<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">

  <title>OMalleyLand Budget - Current Month No Bills</title>


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
		    
		 	function drawChart() {";
			 
		 	//Only get list of categories actually used to cut down on processing time
			//Only get list of categories actually used to cut down on processing time
			$result = $mysqli->query("SELECT DISTINCT c.name AS name
									FROM " . $db_name . ".Debits d
									JOIN " . $db_name . ".Categories c ON c.id = d.category_id
									WHERE d.debit_date >= DATE_ADD(LAST_DAY(DATE_ADD(NOW(), INTERVAL - 1 MONTH)), INTERVAL 1 DAY)
										AND c.Name <> 'Bills'
									ORDER BY c.name ASC");

			if($mysqli->affected_rows) {	
				echo "
							var dataColumnChartCurrentMonthNoBills = google.visualization.arrayToDataTable([['Month'";
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
										WHERE d.debit_date >= DATE_ADD(LAST_DAY(DATE_ADD(NOW(), INTERVAL - 1 MONTH)), INTERVAL 1 DAY)
											AND c.Name <> 'Bills'
										GROUP BY DATE_FORMAT(d.debit_date, '%m %Y'), c.name
		                  				ORDER BY c.name ASC");									
			
				$MonthPrinted = 0;
				$categoryHeader = '';
				while($row = $result->fetch_array(MYSQLI_BOTH)) {
					$curMonth = $row['Month'];
					$curCategory = $row['Category'];
				
						if ($MonthPrinted == 0) {
							echo ",['" . $curMonth . "'"; 
							$MonthPrinted = 1;
						}
					echo "," . $row['Total_Spent'];	
				}    
				echo "]]);
					      var optionsColumnChartMonthNoBills = {
					        title: 'Current Month by Category - No Bills',
					        hAxis: {title: 'Month', titleTextStyle: {color: 'red'}}
					      };
			
					      var chartColumnChartNoBills = new google.visualization.ColumnChart(document.getElementById('ColumnChartNoBills_div'));
					      chartColumnChartNoBills.draw(dataColumnChartCurrentMonthNoBills, optionsColumnChartMonthNoBills);";
			}
		echo "}
		</script>";
    
	$mysqli->close();
?>

</head>
<body>

<?php
	if($mobile==1) {
		echo "<div id='ColumnChartNoBills_div' class='mobilegraphdiv'></div>";
	}
	else {
		echo "<div id='ColumnChartNoBills_div' class='graphdiv'></div>";
	}
?>

</body>
</html>
