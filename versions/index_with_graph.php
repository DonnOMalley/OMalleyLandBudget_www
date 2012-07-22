<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
  <title>O'Malley Land Budget</title>

  
<?php 
  list($m,$d,$y) = explode("/", date('m/d/Y')); 
	if(isset($_POST['start_debit_month']) && isset($_POST['start_debit_year']) && isset($_POST['end_debit_month']) && isset($_POST['end_debit_year'])){
		$startMonth = $_POST['start_debit_month'];
		$startYear = $_POST['start_debit_year'];
		$endMonth = $_POST['end_debit_month'];
		$endYear = $_POST['end_debit_year'];
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
	
	$con = mysql_connect("192.168.2.113","web_user","Cinderella");
	if (!$con) {
		die('Could not connect: ' . mysql_error());
	}
	
	mysql_select_db("OMalleyLandBudget", $con);    
    
  echo "
  	<script type='text/javascript' src='https://www.google.com/jsapi'></script>
		<script type='text/javascript'>
			google.load('visualization', '1', {packages:['corechart']});
			google.setOnLoadCallback(drawChart);
		    
		 	function drawChart() {
	 		 var dataColumnChart = google.visualization.arrayToDataTable([";
			 
			 //Only get list of categories actually used to cut down on processing time
			$result = mysql_query("SELECT DISTINCT c.name AS name
									FROM OMalleyLandBudget.Debits d
									JOIN OMalleyLandBudget.Categories c ON c.id = d.category_id
									WHERE d.debit_date > DATE_ADD(MAKEDATE(YEAR(NOW()),MONTH(NOW())), INTERVAL -12 MONTH)");
									
		    echo "['Month', ";
			while($row = mysql_fetch_array($result)) {
				$categoryName = $row['name'];
				if(count($categoryArray) == 0) {
					$categoryArray = array($categoryName);
				}
				else {
					array_push($categoryArray, $categoryName);
				}
				echo $categoryName . ", ";
			} 
		    echo "'Total Spent']";
			 
			$result = mysql_query("SELECT DATE_FORMAT(d.debit_date, '%M %Y') AS Month,
									c.name AS Category,
									sum(d.Amount) AS Total_Spent
									FROM OMalleyLandBudget.Debits d
									JOIN OMalleyLandBudget.Categories c ON c.id = d.category_id
									WHERE d.debit_date > DATE_ADD(MAKEDATE(YEAR(NOW()),MONTH(NOW())), INTERVAL -12 MONTH)
									GROUP BY DATE_FORMAT(d.debit_date, '%M %Y'), c.name
                    				ORDER BY DATE_FORMAT(d.debit_date, '%M %Y'), c.name ASC ");									
			
			$prevMonth = '';
			$i = 0;
			$curCategoryI = -1;
			$categoryHeader = '';
			while($row = mysql_fetch_array($result)) {
				$curMonth = $row['Month'];
				$curCategory = $row['Category'];
				
				if($prevMonth != $curMonth) {
					if ($curCategoryI > -1) {
						echo "],"; //write end bracket and comma for previous record
					}
					echo "['" . $curMonth . "'"; //start a new row for the month, 
					$i=0;
					$curCategoryI = -1;
					$zeroEnabled = 1;
					$categoryHeader = $categoryArray[$i];
				}				
				else {
					// $i = $i + 1;
					// if($i < count($categoryArray)) {
						// $categoryHeader = $categoryArray[$i];
					// }
				}
				
				while($categoryHeader != $curCategory) {
					if($curCategoryI==-1){
						//First Category of month not matched
						//Insert a $0 value for that category
						if($zeroEnabled == 1) {
							echo ",0";
						}
						else {
							$zeroEnabled == 1;
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
				
				//Category Header and Current Record Category Match,
				//Write Amount Value from Record
				echo "," . $row['Total_Spent'];				
			}       
			echo "]]);
			        var optionsColumnChart = {
			          title: 'Categories By Month',
			          hAxis: {title: 'Total Spent', titleTextStyle: {color: 'red'}}
			        };
			
			        var chartColumnChart = new google.visualization.ColumnChart(document.getElementById('ColumnChart_div'));
			        chartColumnChart.draw(dataColumnChart, optionsColumnChart);
				
				
				
		 		var data1 = google.visualization.arrayToDataTable([";
		        
	$result = mysql_query("SELECT c.name, sum(d.amount) as totalAmount
															FROM OMalleyLandBudget.Debits d JOIN OMalleyLandBudget.Categories c on d.category_id = c.id
															WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))
															GROUP BY c.name");

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
												FROM OMalleyLandBudget.Debits d JOIN OMalleyLandBudget.Categories c on d.category_id = c.id
												WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))
												GROUP BY c.name");

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
		}
		</script>";
    
	mysql_close($con);
?>
</head>
<body>
	<CENTER>
		<H1>O'MalleyLand Budget</H1>
		<table>
		<tr>
		<td>
		<a href="http://www.omalleyland.com:8088/createDebit.php" target="_blank">Create New Debit(s)</a>
		</td>
		<td>
		<a href="http://www.omalleyland.com:8088/createParentCategory.htm" target="_blank">Add New Category</a>
		</td>
		</table>
		
		
		<form method="post" action="index.php">
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
				    

				    <select name="start_debit_year">
						<?php
				    	$con = mysql_connect("192.168.2.113","web_user","Cinderella");
							if (!$con) {
								die('Could not connect: ' . mysql_error());
							}

							mysql_select_db("OMalleyLandBudget", $con);    
						        
							$result = mysql_query("SELECT DISTINCT YEAR(debit_date) AS Years FROM Debits ORDER BY debit_date DESC;");
																	
							while($row = mysql_fetch_array($result)) {
								$year = $row['Years'];
								echo "<option value='" . $year . "'>" . $year . "</option>";
			
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
				    <select name="end_debit_year">
						<?php
				    	$con = mysql_connect("192.168.2.113","web_user","Cinderella");
							if (!$con) {
								die('Could not connect: ' . mysql_error());
							}

							mysql_select_db("OMalleyLandBudget", $con);    
						        
							$result = mysql_query("SELECT DISTINCT YEAR(debit_date) AS Years FROM Debits ORDER BY debit_date DESC;");
																	
							while($row = mysql_fetch_array($result)) {
								$year = $row['Years'];
								echo "<option value='" . $year . "'>" . $year . "</option>";			
							}       
							if($m==12) {
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
				$con = mysql_connect("192.168.2.113","web_user","Cinderella");
				if (!$con) {
					die('Could not connect: ' . mysql_error());
				}
			
				mysql_select_db("OMalleyLandBudget", $con);
			
				$result = mysql_query("SELECT count(d.ID) as record_count 
																		FROM OMalleyLandBudget.Debits d
																		WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))");
			
				$transactionCount=0;
				while($row = mysql_fetch_array($result)) {
					$transactionCount = $row['record_count'] - 1;
				}
			
				$result = mysql_query("SELECT sum(d.amount) as total_spent 
																		FROM OMalleyLandBudget.Debits d
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
					<TABLE BORDER=1>
						<TR>
							<?php
								$sort = $_GET["sort"];
								$sortType = $_GET["sortType"];
								if($sortType == "asc") {
									$nextSortType = "desc";
								}
								else {
									$nextSortType = "asc";	
								}	
							?>
							<td align="center"><u><b><a href="index.php?sortType=
								<?php 
									if($sort=="") {
										if ($sortType == "") {
											echo "desc";
										}
										else {
											echo $nextSortType;
										}
									} 
									else {
										echo "desc";
									}
								?>
							">Debit Date</a></b></u></td>
							<td align="center"><u><b><a href="index.php?sort=purchaser&sortType=
								<?php 
									if($sort=="purchaser") {
										echo $nextSortType;
									} 
									else {
										echo "asc";
									}
								?>
							">Purchaser</a></b></u></td>
							<td align="center"><u><b><a href="index.php?sort=category&sortType=
								<?php 
									if($sort=="category") {
										echo $nextSortType;
									} 
									else {
										echo "asc";
									}
								?>
							">Category</a></b></u></td>
							<td align="center"><u><b><a href="index.php?sort=amount&sortType=
								<?php 
									if($sort=="amount") {
										echo $nextSortType;
									} 
									else {
										echo "asc";
									}
								?>
							">Amount</a></b></u></td>
							<td align="center"><u><b><a href="index.php?sort=payee&sortType=
								<?php 
									if($sort=="payee") {
										echo $nextSortType;
									} 
									else {
										echo "asc";
									}
								?>
							">Payee</a></b></u></td>
							<td align="center"><u><b>Comment</b></u></td>
							<td align="center"><u><b>Credit Card Purchase</b></u></td>
						</TR>
						<?php $con = mysql_connect("192.168.2.113","web_user","Cinderella");
							if (!$con) {
								die('Could not connect: ' . mysql_error());
							}

							mysql_select_db("OMalleyLandBudget", $con);
							
							$sqlStr = "SELECT DATE_FORMAT(d.debit_date, '%M %e, %Y') as debit_date,
																						CONCAT_WS(' ',p.firstName,p.lastName) AS purchaserName, 
																						c.name, 
																						d.amount, 
																						s.name as payee,
																						d.comment, 
																						CASE d.creditCardPurchase
																							WHEN '0' THEN 'N'
																							ELSE 'Y'
																						END AS credit_card 				 
																		FROM Debits d 
																		LEFT OUTER JOIN Categories c on d.category_id = c.id
																		LEFT OUTER JOIN Purchasers p on p.id = d.purchaser_id
																		LEFT OUTER JOIN Stores s on s.id = d.store_id
																		WHERE ((d.debit_date >= '" . $startDate . "') And (d.debit_date < '" . $endDate . "'))";
							
							$sqlStr = $sqlStr . " ORDER BY ";
							switch($sort){
								case 'purchaser':
									$sqlStr = $sqlStr . "CONCAT_WS(' ',p.firstName,p.lastName) " . $sortType . ", ";
									break;
								case 'category':
									$sqlStr = $sqlStr . "c.name " . $sortType . ", ";
									break;
								case 'amount':
									$sqlStr = $sqlStr . "d.amount " . $sortType . ", ";
									break;
								case 'payee':
									$sqlStr = $sqlStr . "s.name " . $sortType . ", ";
									break;	
								default:
									$sqlStr = $sqlStr  . "d.debit_date " . $sortType . ", ";
							}
							$sqlStr = $sqlStr . "d.entry_on desc";

							$result = mysql_query($sqlStr);

							while($row = mysql_fetch_array($result)) {
								echo "<tr align=top><td nowrap=nowrap>";
								echo $row['debit_date'] . "</td><td>" . $row['purchaserName'] . "</td><td>" . $row['name'] . "</td><td>$" . $row['amount'] . "</td><td>" . $row['payee'] . "</td><td>" . $row['comment'] . "</td><td align=center>" . $row['credit_card'] . "</td>";
								echo "</tr>";
							}

							mysql_close($con);
						?>
					</TABLE>
				</TD>
				<TD valign="top">
					<div id='ColumnChart_div' style='width: 675px; height: 375px;'></div>
					<div id='chart_div1' style='width: 675px; height: 375px;'></div>
					<div id='chart_div2' style='width: 675px; height: 375px;'></div>
				</TD>
			</TR>
		</TABLE>
	</CENTER>
</body>
</html>
