<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">

  <title>OMalleyLand Budget Spending Details - mobile</title>
  <?php 

		//Constants for DB Connectivity
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
	?>
</head>
<body>
	<CENTER>
		<H1><a href="info.php">O'MalleyLand Budget</a></H1>
		<h6>Best Viewed in Landscape</h6>
		<table>
		<tr>
		<td>
		<a href="createDebit.php" target="_blank">Create New Debit(s)</a>
		</td>
		<td>
		<a href="createParentCategory.php" target="_blank">Add New Category</a>
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
				    	$con = mysql_connect($host,$username,$password);
							if (!$con) {
								die('Could not connect: ' . mysql_error());
							}

							mysql_select_db($db_name, $con);    
						        
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
				    	$con = mysql_connect($host,$username,$password);
							if (!$con) {
								die('Could not connect: ' . mysql_error());
							}

							mysql_select_db($db_name, $con);    
						        
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
							<td align="center"><u><b><a href="info.php?sortType=
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
									echo "&start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear .  "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear
								?>
							">Debit Date</a></b></u></td>
							<td align="center"><u><b><a href="info.php?sort=purchaser&sortType=
								<?php 
									if($sort=="purchaser") {
										echo $nextSortType;
									} 
									else {
										echo "asc";
									}
									echo "&start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear .  "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear
								?>
							">Purchaser</a></b></u></td>
							<td align="center"><u><b><a href="info.php?sort=category&sortType=
								<?php 
									if($sort=="category") {
										echo $nextSortType;
									} 
									else {
										echo "asc";
									}
									echo "&start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear .  "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear
								?>
							">Category</a></b></u></td>
							<td align="center"><u><b><a href="info.php?sort=amount&sortType=
								<?php 
									if($sort=="amount") {
										echo $nextSortType;
									} 
									else {
										echo "asc";
									}
									echo "&start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear .  "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear
								?>
							">Amount</a></b></u></td>
							<td align="center"><u><b><a href="info.php?sort=payee&sortType=
								<?php 
									if($sort=="payee") {
										echo $nextSortType;
									} 
									else {
										echo "asc";
									}
									echo "&start_debit_month=" . $startMonth . "&start_debit_year=" . $startYear .  "&end_debit_month=" . $endMonth . "&end_debit_year=" . $endYear
								?>
							">Payee</a></b></u></td>
							<td align="center"><u><b>Comment</b></u></td>
						</TR>
						<?php 
				    	$con = mysql_connect($host,$username,$password);
							if (!$con) {
								die('Could not connect: ' . mysql_error());
							}

							mysql_select_db($db_name, $con);
							
							$sqlStr = "SELECT DATE_FORMAT(d.debit_date, '%M %e, %Y') as debit_date,
																						CONCAT_WS(' ',p.firstName,p.lastName) AS purchaserName, 
																						c.name, 
																						d.amount, 
																						s.name as payee,
																						d.comment			 
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
								echo $row['debit_date'] . "</td><td>" . $row['purchaserName'] . "</td><td>" . $row['name'] . "</td><td>$" . $row['amount'] . "</td><td>" . $row['payee'] . "</td><td>" . $row['comment'] . "</td>";
								echo "</tr>";
							}

							mysql_close($con);
						?>
					</TABLE>
				</TD>
			</TR>
		</TABLE>
	</CENTER>
</body>
</html>
