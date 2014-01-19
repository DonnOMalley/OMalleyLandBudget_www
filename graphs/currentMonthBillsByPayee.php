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
			$result = $mysqli->query("SELECT DISTINCT p.name AS name
									FROM " . $db_name . ".Debits d
									JOIN " . $db_name . ".Categories c ON c.id = d.category_id
									JOIN " . $db_name . ".Payees p ON p.id = d.payee_id
									WHERE d.debit_date >= DATE_ADD(LAST_DAY(DATE_ADD(NOW(), INTERVAL - 1 MONTH)), INTERVAL 1 DAY)
										AND c.Name = 'Bills'
									ORDER BY p.name ASC");
									
			if($mysqli->affected_rows) {		
				echo "var dataColumnChartCurrentMonthBills = google.visualization.arrayToDataTable([";
				echo "['Month'";
				while($row = $result->fetch_array(MYSQLI_BOTH)) {
					$payeeName = $row['name'];
					if(count($payeeArray) == 0) {
						$payeeArray = array($payeeName);
					}
					else {
						array_push($payeeArray, $payeeName);
					}
					echo ",'" . str_replace("'", "", $payeeName) . "'";
				} 
				  echo "]";
				 
				$result = $mysqli->query("SELECT DATE_FORMAT(d.debit_date, '%M %Y') AS Month,
										p.name AS Payee,
										sum(d.Amount) AS Total_Spent
										FROM " . $db_name . ".Debits d
										JOIN " . $db_name . ".Categories c ON c.id = d.category_id
										JOIN " . $db_name . ".Payees p ON p.id = d.payee_id
										WHERE d.debit_date >= DATE_ADD(LAST_DAY(DATE_ADD(NOW(), INTERVAL - 1 MONTH)), INTERVAL 1 DAY)
											AND c.Name = 'Bills'
										GROUP BY DATE_FORMAT(d.debit_date, '%m %Y'), p.name
		                  				ORDER BY p.name ASC");									
			
				$MonthPrinted = 0;
				$payeeHeader = '';
				while($row = $result->fetch_array(MYSQLI_BOTH)) {
					$curMonth = $row['Month'];
					$curPayee = $row['Payee'];
				
						if ($MonthPrinted == 0) {
							echo ",['" . $curMonth . "'"; 
							$MonthPrinted = 1;
						}
					echo "," . $row['Total_Spent'];	
				}    
				echo "]]);
					      var optionsColumnChartMonthBills = {
					        title: 'Current Month Bills by Payee',
					        hAxis: {title: 'Month', titleTextStyle: {color: 'red'}}
					      };
			
					      var chartColumnChartBills = new google.visualization.ColumnChart(document.getElementById('ColumnChartBills_div'));
					      chartColumnChartBills.draw(dataColumnChartCurrentMonthBills, optionsColumnChartMonthBills);";
			 
			}
		echo "}
		</script>";
    
	$mysqli->close();
?>

</head>
<body>

<?php
	if($mobile==1) {
		echo "<div id='ColumnChartBills_div' class='mobilegraphdiv'></div>";
	}
	else {
		echo "<div id='ColumnChartBills_div' class='graphdiv'></div>";
	}
?>

</body>
</html>
