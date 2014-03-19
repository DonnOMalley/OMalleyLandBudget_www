<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">

  <title>OMalleyLand Budget Spending Details</title>

<style>
  .graphdiv {
    width: 675px;
    height: 375px;
  }
</style>
  
<?php 

	include 'includes/common.php';
	include 'includes/database.php';
	include 'includes/classes.php';

	// Connect to server and select database.
	$mysqli = new mysqli("$HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME");
	
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
	
	$startDate = $startYear . "-" . $startMonth . "-1";
	$endDate = $endYear . "-" . $endMonth . "-1";

  echo "
  	<script type='text/javascript' src='https://www.google.com/jsapi'></script>
		<script type='text/javascript'>
			google.load('visualization', '1', {packages:['corechart']});
			google.setOnLoadCallback(drawChart);
		    
		 	function drawChart() {
	 		 var dataColumnChartLast3MonthsNoBills = google.visualization.arrayToDataTable([";
			 
			 //Only get list of categories actually used to cut down on processing time
			$result = $mysqli->query("SELECT DISTINCT c.name AS name
									FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
									JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c ON c.id = d.category_id
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
									FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
									JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c ON c.id = d.category_id
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
			        var optionsColumnChartLast3MonthsNoBills = {
			          title: 'Last 3 Months by Category',
			          hAxis: {title: 'Month', titleTextStyle: {color: 'red'}}
			        };
			
			        var chartColumnChartLast3MonthsNoBills = new google.visualization.ColumnChart(document.getElementById('ColumnChartLast3MonthsNoBills_div'));
			        chartColumnChartLast3MonthsNoBills.draw(dataColumnChartLast3MonthsNoBills, optionsColumnChartLast3MonthsNoBills);
			        
			        //////////////////////////////////////////////////////////////////////////////////////////////
			        ";
			 
			//Only get list of categories actually used to cut down on processing time
			$result = $mysqli->query("SELECT DISTINCT c.name AS name
									FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
									JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c ON c.id = d.category_id
									WHERE d.debit_date >= DATE_ADD(LAST_DAY(DATE_ADD(NOW(), INTERVAL - 1 MONTH)), INTERVAL 1 DAY)
										AND c.Name <> 'Bills'
									ORDER BY c.name ASC");

			if($mysqli->affected_rows > 0) {	
				echo "var dataColumnChartCurrentMonthNoBills = google.visualization.arrayToDataTable([['Month'";
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
									FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
									JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c ON c.id = d.category_id
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
			else {
				echo "
							document.getElementById('ColumnChartNoBills_div').setAttribute('style','height:0px');
							";
			}
			        
			echo"//////////////////////////////////////////////////////////////////////////////////////////////
			";       		 
			 
			 //Only get list of categories actually used to cut down on processing time
			$result = $mysqli->query("SELECT DISTINCT p.name AS name
									FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
									JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c ON c.id = d.category_id
									JOIN " . $DB_NAME . "." . $TABLE_STORES . " p ON p.id = d.payee_id
									WHERE d.debit_date >= DATE_ADD(LAST_DAY(DATE_ADD(NOW(), INTERVAL - 1 MONTH)), INTERVAL 1 DAY)
										AND c.Name = 'Bills'
									ORDER BY p.name ASC");
									
			if($mysqli->affected_rows > 0) {		
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
															FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
															JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c ON c.id = d.category_id
															JOIN " . $DB_NAME . "." . $TABLE_STORES . " p ON p.id = d.payee_id
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
			else {
				echo "
							document.getElementById('ColumnChartBills_div').setAttribute('style','height:0px');
							";
			}
			echo "
							//////////////////////////////////////////////////////////////////////////////////////////////
			        
			        var dataColumnChartCategorySpendingByMonth = google.visualization.arrayToDataTable([";
			 
			 //Only get list of categories actually used to cut down on processing time
			$result = $mysqli->query("SELECT DISTINCT c.name AS name
									FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
									JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c ON c.id = d.category_id
									WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))
										AND c.Name <> 'Bills'
									ORDER BY c.name ASC");
									
		    echo "['Month'";
			while($row = $result->fetch_array(MYSQLI_BOTH)) {
				$categoryName = $row['name'];
				if(count($categoryArray2) == 0) {
					$categoryArray2 = array($categoryName);
				}
				else {
					array_push($categoryArray2, $categoryName);
				}
				echo ",'" . $categoryName . "'";
			} 
		    echo "]";
			 
			$result = $mysqli->query("SELECT DATE_FORMAT(d.debit_date, '%M %Y') AS Month,
									c.name AS Category,
									sum(d.Amount) AS Total_Spent
									FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
									JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c ON c.id = d.category_id
									WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))
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
						while($i < (count($categoryArray2) - 1)) {
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
					$categoryHeader = $categoryArray2[$i];
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
						$categoryHeader = $categoryArray2[$i];	
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
			while($i < (count($categoryArray2) - 1)) {
				echo ",0";
				$i = $i + 1;
			}  
			echo "]]);
			        var optionsColumnChartCategorySpendingByMonth = {
			          title: 'Category Spending by Month',
			          hAxis: {title: 'Month', titleTextStyle: {color: 'red'}}
			        };
			
			        var chartColumnChartCategorySpendingByMonth = new google.visualization.ColumnChart(document.getElementById('ColumnChartCategorySpendingByMonth_div'));
			        chartColumnChartCategorySpendingByMonth.draw(dataColumnChartCategorySpendingByMonth, optionsColumnChartCategorySpendingByMonth);
			        
			        
			        /////////////////////////////////
				
		 		var dataPieChartCategoryDollars = google.visualization.arrayToDataTable([";
		        
	$result = $mysqli->query("SELECT c.name, sum(d.amount) as totalAmount
												FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
												JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c ON c.id = d.category_id
												WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))
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
		var optionsPieChartCategoryDollars = {
	    title: '$ Spent By Category'
    };

    var chartPieChartCategoryDollars = new google.visualization.PieChart(document.getElementById('PieChartCategoryDollars_div'));
    chartPieChartCategoryDollars.draw(dataPieChartCategoryDollars, optionsPieChartCategoryDollars);
    
    /////////////////////////////////////////////////////////////////////////////
    ";
		        
	$result = $mysqli->query("SELECT c.name, sum(d.amount) as totalAmount
												FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
												JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c ON c.id = d.category_id
												WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "')
													AND (c.name <> 'Bills'))
												GROUP BY c.name");															
			        				
	if($mysqli->affected_rows > 0) {	
		echo "var dataPieChartCategoryDollarsNoBills = google.visualization.arrayToDataTable([['Category', 'Dollars']";
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
		  chartPieChartCategoryDollarsNoBills.draw(dataPieChartCategoryDollarsNoBills, optionsPieChartCategoryDollarsNoBills);";
  }
  else {
  		echo "
  				document.getElementById('CategoryDollarsNoBills_div').setAttribute('style','height:0px');
  			";
  }

	echo "    /////////////////////////////////////////////////////////////////////////////
		      
		var dataPieChartCategoryCounts = google.visualization.arrayToDataTable([";
		        
	$result = $mysqli->query("SELECT c.name, count(*) as count
												FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
												JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c ON c.id = d.category_id
												WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))
												GROUP BY c.name");

	echo "['Category', 'Counts']";
	$prevName = "";
	$totalAmount = 0;
	$amount = 0;
	while($row = $result->fetch_array(MYSQLI_BOTH)) {
		$name = $row['name'];
		$count = $row['count'];
		echo ",['" . $name . "'," . $count . "]";
	}       
	echo "]);
		var optionsPieChartCategoryCounts = {
			title: 'Category Transaction Counts'
		};
		
		var chartPieChartCategoryCounts = new google.visualization.PieChart(document.getElementById('PieChartCategoryCounts_div'));
		chartPieChartCategoryCounts.draw(dataPieChartCategoryCounts, optionsPieChartCategoryCounts);
		}
		</script>";
    
	$mysqli->close();
?>

</head>
<body>
	<CENTER>
		<H1><a href="http://www.omalleyland.com">O'MalleyLand Budget</a></H1>
		<table>
		<tr>
		<td>
		<a href="http://www.omalleyland.com/createDebit.php" target="_blank">Create New Debit(s)</a>
		</td>
		<td>
		<a href="http://www.omalleyland.com/createCategory.php" target="_blank">Create New Category</a>
		</td>
		<td>
		<a href="http://www.omalleyland.com/createPayee.php" target="_blank">Create New Payee</a>
		</td>
		</table>
		
		
		<form method="post" action="info.php">
			<table border=1>
				<tr>
					<td align="center"><b><u>Start Date</b></u></td>
					<td align="center"><b><u>End Date</b></u></td>
				</tr>
				<tr>
					<td>
						<select name="start_debit_month">        
						<?php 
					    	$mysqli = new mysqli("$HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME");
							$loop = 1;
							while($loop <= 12) {
								echo "<option value='" . $loop . "'";
								if($loop==$startMonth) {
									echo " selected=selected";
								}
								echo ">";
							
								switch($loop) {
									case 1:
										echo "January";
										break;
									case 2:
										echo "February";
										break;
									case 3:
										echo "March";
										break;
									case 4:
										echo "April";
										break;
									case 5:
										echo "May";
										break;
									case 6:
										echo "June";
										break;
									case 7:
										echo "July";
										break;
									case 8:
										echo "August";
										break;
									case 9:
										echo "September";
										break;
									case 10:
										echo "October";
										break;
									case 11:
										echo "November";
										break;
									case 12:
										echo "December";
										break;
								}
							
								echo "</option>"; 					
								$loop = $loop + 1;
							}
						?>
				    </select>
				    
						/ 1 / 

				    <select name="start_debit_year">
						<?php
						
							$result = $mysqli->query("SELECT MAX(YEAR(debit_date)) as MaxYear FROM " . $DB_NAME . "." . $TABLE_DEBITS . ";");
							while($row = $result->fetch_array(MYSQLI_BOTH)) {
								$maxyear = $row['MaxYear'];
								if($maxyear == $y) {
									echo "<option value='" . ($y + 1) . "'>" . ($y + 1) . "</option>";
								}
							}
						        
							$result = $mysqli->query("SELECT DISTINCT YEAR(debit_date) AS Years FROM " . $DB_NAME . "." . $TABLE_DEBITS . " ORDER BY debit_date DESC;");
																	
							$startYearFound = False;
							while($row = $result->fetch_array(MYSQLI_BOTH)) {
								$year = $row['Years'];								
								echo "<option value='" . $year . "'";
								if($year==$startYear) {
									$startYearFound = True;
									echo " selected=selected";
								}
								echo ">" . $year . "</option>";
							}
							//In the event there are no transactions for the year (ex: Jan 1)    
							if(!$startYearFound) {
								echo "<option value='" . $startYear . "' selected=selected>" . $startYear . "</option>";
							}
						?>
				    </select>					
					</td>
					<td>					
						<select name="end_debit_month">       
						<?php 
							$loop = 1;
							while($loop <= 12) {
								echo "<option value='" . $loop . "'";
								if($loop==$endMonth) {
									echo " selected=selected";
								}
								echo ">";
							
								switch($loop) {
									case 1:
										echo "January";
										break;
									case 2:
										echo "February";
										break;
									case 3:
										echo "March";
										break;
									case 4:
										echo "April";
										break;
									case 5:
										echo "May";
										break;
									case 6:
										echo "June";
										break;
									case 7:
										echo "July";
										break;
									case 8:
										echo "August";
										break;
									case 9:
										echo "September";
										break;
									case 10:
										echo "October";
										break;
									case 11:
										echo "November";
										break;
									case 12:
										echo "December";
										break;
								}
							
								echo "</option>"; 					
								$loop = $loop + 1;
							}
						?>
				    </select>
				    
						/ 1 / 

				    <select name="end_debit_year">
						<?php
						
							$result = $mysqli->query("SELECT MAX(YEAR(debit_date)) as MaxYear FROM " . $DB_NAME . "." . $TABLE_DEBITS . ";");
							while($row = $result->fetch_array(MYSQLI_BOTH)) {
								$maxyear = $row['MaxYear'];
								if($maxyear == $y) {
									echo "<option value='" . ($y + 1) . "'>" . ($y + 1) . "</option>";
								}
							}
						        
							$result = $mysqli->query("SELECT DISTINCT YEAR(debit_date) AS Years FROM " . $DB_NAME . "." . $TABLE_DEBITS . " ORDER BY debit_date DESC;");
															
							while($row = $result->fetch_array(MYSQLI_BOTH)) {
								$year = $row['Years'];
								echo "<option value='" . $year . "'";
								if($year==$endYear) {
									echo " selected=selected";
								}
								echo ">" . $year . "</option>";	
							}  
							
						?>
				    </select>					
					</td>
				</tr>
				<tr>
					<td colspan=2 align="center"><input value="Update" type="submit"></td>
				</tr>
			<table>
		</form>
		<h4>
			<?php 
			
				$result = $mysqli->query("SELECT count(1) as record_count 
															FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
															WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))");
			
				$transactionCount=0;
				while($row = $result->fetch_array(MYSQLI_BOTH)) {
					$transactionCount = $row['record_count'] - 1;
					if($transactionCount < 0) {
						$transactionCount = 0;
					}
				}
			
				$result = $mysqli->query("SELECT sum(d.amount) as total_spent 
															FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d
															WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))");
			
				$totalSpent=0;
				while($row = $result->fetch_array(MYSQLI_BOTH)) {
					$totalSpent = $row['total_spent'] + 0;
				}
			
				echo "<i><U># Transactions:</U>&nbsp;&nbsp;</i><B>" . $transactionCount . "</B> :: <i><U>Total Spent: </U>&nbsp;&nbsp;</i><b>$" . $totalSpent . "</b>";
			?>
		</h4>
		
		<TABLE>
			<TR>
				<TD valign="top">
					<TABLE BORDER=1>
						<TR>
							<?php
								$sort = $_GET["sort"];
								if(isset($_GET["sortType"])) {	
									$sortType = $_GET["sortType"];
									if($sortType == "asc") {
										$nextSortType = "desc";
									}
									else {
										$nextSortType = "asc";	
									}
								}
								else {
									$nextSortType = "asc";
									$sortType = "desc";
								}								
								echo "<td align=center><u><b><a href='info.php?sortType=";
								if($sort=="") {
									echo $nextSortType;
								}
								else {
									echo "asc";
								} 
								echo "&start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear .  "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear;
								echo "'>Debit Date</a></b></u></td>";
								echo "<td align=center><u><b><a href='info.php?sort=user&sortType="; 
								if($sort=="user") {	
									echo $nextSortType;
								} 
								else {
									echo "asc";
								}
								echo "&start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear .  "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear;
								echo "'>User</a></b></u></td>";
								echo "<td align=center><u><b><a href='info.php?sort=category&sortType=";
								if($sort=="category") {
									echo $nextSortType;
								} 
								else {
									echo "asc";
								}
								echo "&start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear .  "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear;
								echo "'>Category</a></b></u></td>";
								echo "<td align=center><u><b><a href='info.php?sort=amount&sortType=";
								if($sort=="amount") {
									echo $nextSortType;
								} 
								else {
									echo "asc";
								}
								echo "&start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear .  "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear;
								echo "'>Amount</a></b></u></td>";
								echo "<td align=center><u><b><a href='info.php?sort=payee&sortType=";
								if($sort=="payee") {
									echo $nextSortType;
								} 
								else {
									echo "asc";
								}
								echo "&start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear .  "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear;
								echo "'>Payee</a></b></u></td>";
								echo "<td align=center><u><b>Comment</b></u></td>";
							?>
						</TR>
							<?php
							
							$sqlStr = "SELECT DATE_FORMAT(d.debit_date, '%M %e, %Y') as debit_date,
																						u.username as UserName, 
																						c.name, 
																						d.amount, 
																						p.name as Payee,
																						d.comment			 
																		FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d 
																		LEFT OUTER JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c on d.category_id = c.id
																		LEFT OUTER JOIN " . $DB_NAME . "." . $TABLE_USERS . " u on u.id = d.user_id
																		LEFT OUTER JOIN " . $DB_NAME . "." . $TABLE_STORES . " p on p.id = d.payee_id
																		WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))";
							
							$sqlStr = $sqlStr . " ORDER BY ";
							switch($sort){
								case 'user':
									$sqlStr = $sqlStr . "u.username " . $sortType . ", ";
									break;
								case 'category':
									$sqlStr = $sqlStr . "c.name " . $sortType . ", ";
									break;
								case 'amount':
									$sqlStr = $sqlStr . "d.amount " . $sortType . ", ";
									break;
								case 'payee':
									$sqlStr = $sqlStr . "p.name " . $sortType . ", ";
									break;	
								default:
									$sqlStr = $sqlStr  . "d.debit_date " . $sortType . ", ";
							}
							$sqlStr = $sqlStr . "d.entry_on desc";

							$result = $mysqli->query($sqlStr);
			
							while($row = $result->fetch_array(MYSQLI_BOTH)) {
								echo "<tr align=top><td nowrap=nowrap>";
								echo $row['debit_date'] . "</td><td>" . $row['UserName'] . "</td><td>" . $row['name'] . "</td><td>$" . $row['amount'] . "</td><td>" . $row['Payee'] . "</td><td>" . $row['comment'] . "</td>";
								echo "</tr>";
							}

							$mysqli->close();
						?>
					</TABLE>
				</TD>
				<TD valign="top">
					<a href='graphs/last3months.php' target=_blank>
						<div id='ColumnChartLast3MonthsNoBills_div' class='graphdiv'></div>
					</a>
					<a href='graphs/currentMonthNoBills.php' target=_blank>
						<div id='ColumnChartNoBills_div' class="graphdiv"></div>
					</a>
					<a href='graphs/currentMonthBillsByPayee.php' target=_blank>
						<div id='ColumnChartBills_div' class="graphdiv"></div>
					</a>
					<?php
					echo"
					<a href='graphs/categorySpendingByMonth.php?start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear . "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear . "' target=_blank>"
					?>
						<div id='ColumnChartCategorySpendingByMonth_div' class="graphdiv"></div>
					</a>
					<?php
					echo"
					<a href='graphs/dollarsSpentByCategoryNoBills.php?start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear . "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear . "' target=_blank>"
					?>
						<div id='PieChartCategoryDollarsNoBills_div' class="graphdiv"></div>
					</a>
					<?php
					echo"
					<a href='graphs/dollarsSpentByCategory.php?start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear . "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear . "' target=_blank>"
					?>
						<div id='PieChartCategoryDollars_div' class="graphdiv"></div>
					</a>
					<div id='PieChartCategoryCounts_div' class="graphdiv"></div>
				</TD>
			</TR>
		</TABLE>
	</CENTER>
</body>
</html>
