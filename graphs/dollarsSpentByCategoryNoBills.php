<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">

  <title>OMalleyLand Budget - Category Spending By Month - No Bills</title>


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
			        
				var dataPieChartCategoryDollarsNoBills = google.visualization.arrayToDataTable([";
		        
			$result = $mysqli->query("SELECT c.name, sum(d.amount) as totalAmount
																	FROM " . $db_name . ".Debits d JOIN " . $db_name . ".Categories c on d.category_id = c.id
																	WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "')
																		AND (c.name <> 'Bills'))
																	GROUP BY c.name");

			echo "['Category', 'Dollars']";
			$prevName = "";
			$totalAmount = 0;
			$amount = 0;
			while($row = $result->fetch_array(MYSQLI_BOTH)) {
				$name = $row['name'];
				$totalAmount = $row['totalAmount'];
				echo ",['" . $name . "'," . $totalAmount . "]";
			}       
			echo "]);
				var optionsPieChartCategoryDollarsNoBills = {
					title: '$ Spent By Category - No Bills'
				};

				var chartPieChartCategoryDollarsNoBills = new google.visualization.PieChart(document.getElementById('PieChartCategoryDollarsNoBills_div'));
				chartPieChartCategoryDollarsNoBills.draw(dataPieChartCategoryDollarsNoBills, optionsPieChartCategoryDollarsNoBills);
		};
		</script>";
    
	$mysqli->close();
?>

</head>
<body>

<?php
	if($mobile==1) {
		echo "<div id='PieChartCategoryDollarsNoBills_div' class='mobilegraphdiv'></div>";
	}
	else {
		echo "<div id='PieChartCategoryDollarsNoBills_div' class='graphdiv'></div>";
	}
?>

</body>
</html>
