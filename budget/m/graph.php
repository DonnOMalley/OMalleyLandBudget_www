<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">

  <title>OMalleyLand Budget Spending Graphs - mobile</title>
  
<?php 

	$host="localhost"; // Host name 
	$username="web_user"; // Mysql username 
	$password="Cinderella"; // Mysql password 
	$db_name="OMalleyLandBudget"; // Database name 

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
			$endYear = $y;
		}
		else {
			$endMonth = 1;
			$endYear = $endyear + 1;
		}
	}	
	
	$startDate = $startYear . "-" . $startMonth . "-1";
	$endDate = $endYear . "-" . $endMonth . "-1";
	
	$con = mysql_connect($host,$username,$password);
	if (!$con) {
		die('Could not connect: ' . mysql_error());
	}
	
	mysql_select_db($db_name, $con);    
    
  echo "
  	<script type='text/javascript' src='https://www.google.com/jsapi'></script>
		<script type='text/javascript'>
			google.load('visualization', '1', {packages:['corechart']});
			google.setOnLoadCallback(drawChart);
		    
		 	function drawChart() {
	 		 var dataColumnChart = google.visualization.arrayToDataTable([";
			 
			 //Only get list of categories actually used to cut down on processing time
			$result = mysql_query("SELECT DISTINCT c.name AS name
									FROM Debits d
									JOIN Categories c ON c.id = d.category_id
									WHERE d.debit_date > DATE_ADD(LAST_DAY(DATE_ADD(NOW(), INTERVAL - 3 MONTH)), INTERVAL 1 DAY)
									ORDER BY c.name ASC");
									
		  echo "['Month'";
			while($row = mysql_fetch_array($result)) {
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
			 
			$result = mysql_query("SELECT DATE_FORMAT(d.debit_date, '%M %Y') AS Month,
									c.name AS Category,
									sum(d.Amount) AS Total_Spent
									FROM Debits d
									JOIN Categories c ON c.id = d.category_id
									WHERE d.debit_date >= DATE_ADD(LAST_DAY(DATE_ADD(NOW(), INTERVAL - 3 MONTH)), INTERVAL 1 DAY)
									GROUP BY DATE_FORMAT(d.debit_date, '%m %Y'), c.name
                    				ORDER BY DATE_FORMAT(d.debit_date, '%Y') ASC, DATE_FORMAT(d.debit_date, '%m') ASC, c.name ASC");									
			
			$prevMonth = '';
			$i = 0;
			$curCategoryI = -1;
			$categoryHeader = '';
			while($row = mysql_fetch_array($result)) {
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
			        
			        //////////////////////////////////////////////////////////////////////////////////////////////
			        
			        var dataColumnChart2 = google.visualization.arrayToDataTable([";
			 
			 //Only get list of categories actually used to cut down on processing time
			$result = mysql_query("SELECT DISTINCT c.name AS name
									FROM Debits d
									JOIN Categories c ON c.id = d.category_id
									WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))
									ORDER BY c.name ASC");
									
		    echo "['Month'";
			while($row = mysql_fetch_array($result)) {
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
			 
			$result = mysql_query("SELECT DATE_FORMAT(d.debit_date, '%M %Y') AS Month,
									c.name AS Category,
									sum(d.Amount) AS Total_Spent
									FROM Debits d
									JOIN Categories c ON c.id = d.category_id
									WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))
									GROUP BY DATE_FORMAT(d.debit_date, '%m %Y'), c.name
                    				ORDER BY DATE_FORMAT(d.debit_date, '%Y') ASC, DATE_FORMAT(d.debit_date, '%m') ASC, c.name ASC");									
			
			$prevMonth = '';
			$i = 0;
			$curCategoryI = -1;
			$categoryHeader = '';
			while($row = mysql_fetch_array($result)) {
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
			        var optionsColumnChart2 = {
			          title: 'Category Spending by Month',
			          hAxis: {title: 'Month', titleTextStyle: {color: 'red'}}
			        };
			
			        var chartColumnChart2 = new google.visualization.ColumnChart(document.getElementById('ColumnChart2_div'));
			        chartColumnChart2.draw(dataColumnChart2, optionsColumnChart2);
			        
			        
			        /////////////////////////////////
				
				
				
		 		var data1 = google.visualization.arrayToDataTable([";
		        
	$result = mysql_query("SELECT c.name, sum(d.amount) as totalAmount
															FROM Debits d 
															JOIN Categories c on d.category_id = c.id
															WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))
															GROUP BY c.name
															ORDER BY c.name");

	echo "['Category', 'Dollars']";
	$prevName = "";
	$totalAmount = 0;
	$amount = 0;
	while($row = mysql_fetch_array($result)) {
		$name = $row['name'];
		$totalAmount = $row['totalAmount'];
		echo ",['" . $name . "'," . $totalAmount . "]";
	}       
	echo "]);
		var options1 = {
	    title: '$ Spent By Category'
    };

    var chart1 = new google.visualization.PieChart(document.getElementById('chart_div1'));
    chart1.draw(data1, options1);
		      
		var data2 = google.visualization.arrayToDataTable([";
		        
	$result = mysql_query("SELECT c.name, count(*) as count
												FROM Debits d 
												JOIN Categories c on d.category_id = c.id
												WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))
												GROUP BY c.name
												ORDER BY c.name");

	echo "['Category', 'Counts']";
	$prevName = "";
	$totalAmount = 0;
	$amount = 0;
	while($row = mysql_fetch_array($result)) {
		$name = $row['name'];
		$count = $row['count'];
		echo ",['" . $name . "'," . $count . "]";
	}       
	echo "]);
		var options2 = {
			title: 'Category Transaction Counts'
		};
		
		var chart2 = new google.visualization.PieChart(document.getElementById('chart_div2'));
		chart2.draw(data2, options2);
		
		
	/////////////////////////////////
			        
			        
 			var budgetDataColumnChart = google.visualization.arrayToDataTable([['Category','Budget','Spent','Balance']";
		 
		$result = mysql_query("SELECT  c.name as Category
										        ,CAST(IFNULL(Avg_Amount,0) AS DECIMAL(10,2)) as Monthly_Budget
										        ,IFNULL(CurrentSpending.Total_Spent, 0) AS Current_Monthly_Spending
										        ,CAST(IFNULL(Avg_Amount,0) AS DECIMAL(10,2)) - IFNULL(CurrentSpending.Total_Spent, 0) AS Monthly_Balance_Remaining
										FROM (
										    SELECT   Avg(Total_Spent) as Avg_Amount
										            ,category_id 
										    FROM (  SELECT  DATE_FORMAT(`Debits`.`debit_date`, '%m') AS debit_month   
										                    ,DATE_FORMAT(`Debits`.`debit_date`, '%Y') AS debit_year 
										                    ,`Debits`.`category_id`
										                    ,SUM(`Debits`.`amount`) AS Total_Spent
										            FROM `".$db_name."`.`Debits`
										            WHERE debit_date < DATE_FORMAT(now(), '%Y-%m-01 00:00:00')
										            GROUP BY DATE_FORMAT(`Debits`.`debit_date`, '%Y')
										                     ,DATE_FORMAT(`Debits`.`debit_date`, '%m') 
										                     ,`Debits`.`category_id` 
										        ) MonthlyCategoryTotals
										    GROUP BY category_id 
										) Averages
										JOIN `" . $db_name . "`.`Categories` c on c.id = Averages.category_id
										LEFT OUTER JOIN (  SELECT   SUM(`Debits`.`amount`) AS Total_Spent
										                                ,`Debits`.`category_id`
										                        FROM `" . $db_name . "`.`Debits`
										                        WHERE debit_date >= DATE_FORMAT(now(), '%Y-%m-01 00:00:00')
										                        GROUP BY `Debits`.`category_id` 
										                ) CurrentSpending ON CurrentSpending.category_id = Averages.category_id
										ORDER BY c.name");									
		
		while($row = mysql_fetch_array($result)) {
			echo ",['" . 	$row['Category'] . "'," . 
								$row['Monthly_Budget'] . "," . 
								$row['Current_Monthly_Spending'] . "," . 
								$row['Monthly_Balance_Remaining'] . "]";
		}    
		
		$result = mysql_query("SELECT  SUM(Monthly_Budget) AS Total_Monthly_Budget
										        ,SUM(Current_Monthly_Spending) AS Total_Monthly_Spending
										        ,SUM(Monthly_Balance_Remaining) AS Total_Balance_Remaining
										FROM (  SELECT  c.name as Category
										                ,CAST(IFNULL(Avg_Amount,0) AS DECIMAL(10,2)) as Monthly_Budget
										                ,IFNULL(CurrentSpending.Total_Spent, 0) AS Current_Monthly_Spending
										                ,CAST(IFNULL(Avg_Amount,0) AS DECIMAL(10,2)) - IFNULL(CurrentSpending.Total_Spent, 0) AS Monthly_Balance_Remaining
										        FROM (
										            SELECT   Avg(Total_Spent) as Avg_Amount
										                    ,category_id 
										            FROM (  SELECT  DATE_FORMAT(`Debits`.`debit_date`, '%m') AS debit_month   
										                            ,DATE_FORMAT(`Debits`.`debit_date`, '%Y') AS debit_year 
										                            ,`Debits`.`category_id`
										                            ,SUM(`Debits`.`amount`) AS Total_Spent
										                    FROM `OMalleyLandBudget`.`Debits`
										                    WHERE debit_date < DATE_FORMAT(now(), '%Y-%m-01 00:00:00')
										                    GROUP BY DATE_FORMAT(`Debits`.`debit_date`, '%Y')
										                             ,DATE_FORMAT(`Debits`.`debit_date`, '%m') 
										                             ,`Debits`.`category_id` 
										                ) MonthlyCategoryTotals
										            GROUP BY category_id 
										        ) Averages
										        RIGHT OUTER JOIN `OMalleyLandBudget`.`Categories` c on c.id = Averages.category_id
										        LEFT OUTER JOIN (  SELECT   SUM(`Debits`.`amount`) AS Total_Spent
										                                        ,`Debits`.`category_id`
										                                FROM `OMalleyLandBudget`.`Debits`
										                                WHERE debit_date >= DATE_FORMAT(now(), '%Y-%m-01 00:00:00')
										                                GROUP BY `Debits`.`category_id` 
										                        ) CurrentSpending ON CurrentSpending.category_id = Averages.category_id
										    ) Totals");

		while($row = mysql_fetch_array($result)) {
			echo ",['Budget Totals'," . 
						$row['Total_Monthly_Budget'] . "," . 
						$row['Total_Monthly_Spending'] . "," . 
						$row['Total_Balance_Remaining'] . "]";
		}
		 
		echo "]);
		        var budgetOptionsColumnChart = {
		          title: 'Budget/Spending By Category',
		          hAxis: {title: 'Category', titleTextStyle: {color: 'red'}}
		        };
		
		        var budgetColumnChart = new google.visualization.ColumnChart(document.getElementById('budgetColumnChart_div'));
		        budgetColumnChart.draw(budgetDataColumnChart, budgetOptionsColumnChart);
		}
		</script>";
    
	mysql_close($con);
?>
</head>
<body>
	<CENTER>
		<H1><a href="info.php">O'MalleyLand Budget</a></H1>
		<table>
		<tr>
		<td>
		<a href="createDebit.php" target="_blank">Create New Debit(s)</a>
		</td>
		<td>
		<a href="createParentCategory.php" target="_blank">Add New Category</a>
		</td>
		</table>
		
		
		<form method="post" action="graph.php">
			<table border=1>
				<tr>
					<td align="center"><b><u>Start Date</b></u></td>
					<td align="center"><b><u>End Date</b></u></td>
				</tr>
				<tr>
					<td>
						<select name="start_debit_month">        
						<?php 
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
				    	$con = mysql_connect($host,$username,$password);	
							if (!$con) {
								die('Could not connect: ' . mysql_error());
							}

							mysql_select_db($db_name, $con);    
						        
							$result = mysql_query("SELECT DISTINCT YEAR(debit_date) AS Years FROM " . $db_name . ".Debits ORDER BY debit_date DESC;");
																	
							while($row = mysql_fetch_array($result)) {
								$year = $row['Years'];								
								echo "<option value='" . $year . "'";
								if($year==$startYear) {
									echo " selected=selected";
								}
								echo ">" . $year . "</option>";
							}    

							mysql_close($con);
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
				    		$con = mysql_connect($host,$username,$password);	
							if (!$con) {
								die('Could not connect: ' . mysql_error());
							}

							mysql_select_db($db_name, $con);    
						        
							$result = mysql_query("SELECT DISTINCT YEAR(debit_date) AS Years FROM " . $db_name . ".Debits ORDER BY debit_date DESC;");
									
							$showNextYear = true;								
							while($row = mysql_fetch_array($result)) {
								$year = $row['Years'];
								echo "<option value='" . $year . "'";
								if($year==$endYear) {
									echo " selected=selected";
								}
								echo ">" . $year . "</option>";	
								if($year==($startYear + 1)) {
									$showNextYear = false;
								}	
							}       
							if($showNextYear && $m==12) {
								$year = $year + 1;
								echo "<option value='" . $year . "'>" . $year . "</option>";									
							}
							mysql_close($con);
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
				$con = mysql_connect($host,$username,$password);
				if (!$con) {
					die('Could not connect: ' . mysql_error());
				}
			
				mysql_select_db($db_name, $con);
			
				$result = mysql_query("SELECT count(d.ID) as record_count 
																		FROM Debits d
																		WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))");
			
				$transactionCount=0;
				while($row = mysql_fetch_array($result)) {
					$transactionCount = $row['record_count'] - 1;
				}
			
				$result = mysql_query("SELECT sum(d.amount) as total_spent 
																		FROM Debits d
																		WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))");
			
				$totalSpent=0;
				while($row = mysql_fetch_array($result)) {
					$totalSpent = $row['total_spent'];
				}
			
				mysql_close($con);
			
				echo "<i><U># Transactions:</U>&nbsp;&nbsp;</i><B>" . $transactionCount . "</B> :: <i><U>Total $ Spent: </U>&nbsp;&nbsp;</i><b>" . $totalSpent . "</b>";
			?>
		</h4>
		
		<TABLE>
			<TR>
				<TD valign="top">
					<div id='budgetColumnChart_div' style='width: 768px; height: 450px;'></div>
					<div id='ColumnChart_div' style='width: 768px; height: 450px;'></div>
					<div id='ColumnChart2_div' style='width: 768px; height: 450px;'></div>
					<div id='chart_div1' style='width: 768px; height: 450px;'></div>
					<div id='chart_div2' style='width: 768px; height: 450px;'></div>
				</TD>
			</TR>
		</TABLE>
	</CENTER>
</body>
</html>
