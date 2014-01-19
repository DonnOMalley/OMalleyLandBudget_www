<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">

  <title>OMalleyLand Budget Spending Details - Mobile</title>
  
<?php 

	include '../includes/common.php';
	include '../includes/database.php';
	include '../includes/classes.php';

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
?>
</head>
<body>
	<CENTER>
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
							
								echo "</option>
								"; 					
								$loop = $loop + 1;
							}
						?>
				    </select>

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
							
								echo "</option>
								"; 									
								$loop = $loop + 1;
							}
						?>
				    </select>

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
								echo "<td align=center><u><b><a href='nfo.php?sort=payee&sortType=";
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
							echo "
";
			
							while($row = $result->fetch_array(MYSQLI_BOTH)) {
								echo "<tr align=top><td nowrap=nowrap>";
								echo $row['debit_date'] . "</td><td>" . $row['UserName'] . "</td><td>" . $row['name'] . "</td><td>$" . $row['amount'] . "</td><td>" . $row['Payee'] . "</td><td>" . $row['comment'] . "</td>";
								echo "</tr>
";
							}

							$mysqli->close();
						?>
					</TABLE>
				</TD>
			</TR>
		</TABLE>
	</CENTER>
</body>
</html>
