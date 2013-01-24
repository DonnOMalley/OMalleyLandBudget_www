<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">

  <title>OMalleyLand Budget - Budget</title>
  
<?php 
	$host="localhost"; // Host name 
	$username="web_user"; // Mysql username 
	$password="Cinderella"; // Mysql password 
	$db_name="OMalleyLandBudget"; // Database name 
?>

</head>
<body>
	<CENTER>
		<H1><a href="http://www.omalleyland.com">O'MalleyLand Budget</a></H1>
		<table>
		<tr>
		<td>
		<a href="http://www.omalleyland.com/budget/createDebit.php" target="_blank">Create New Debit(s)</a>
		</td>
		<td>
		<a href="http://www.omalleyland.com/budget/createParentCategory.php" target="_blank">Add New Category</a>
		</td>
		</table>
		
		<TABLE>
			<TR>
				<TD valign="top">
					<TABLE BORDER=1>
						<TR>
							<?php
								$sort = $_GET["sort"];
								$sortType = $_GET["sortType"];
								$href = "budget.php?sort=Category&sortType=";
								if($sort=='Category') {
									if($sortType=='asc') {
										$href = $href . "desc";
									}
									else {
										$href = $href . "asc";
									}
								}
								else if($sort=='') { 			//by default, the page loads with sorting by Category asc.
									$href = $href . "desc";
								}
								else {
									$href = $href . "asc";
								}
								echo '<td align="center"><u><b>
											<a href="' . $href . '">
												Category
											</a></b></u></td>';
								
								$href = "budget.php?sort=Monthly_Budget&sortType=";
								if($sort=="Monthly_Budget") {
									if($sortType=='asc') {
										$href = $href . "desc";
									}
									else {
										$href = $href . "asc";
									}
								}
								else { 			
									$href = $href . "asc";
								}
								echo '<td align="center"><u><b>
											<a href="' . $href . '">
												Budget
											</a></b></u></td>';
								
								$href = "budget.php?sort=Current_Monthly_Spending&sortType=";
								if($sort=="Current_Monthly_Spending") {
									if($sortType=='asc') {
										$href = $href . "desc";
									}
									else {
										$href = $href . "asc";
									}
								}
								else { 			
									$href = $href . "asc";
								}
								echo '<td align="center"><u><b>
											<a href="' . $href . '">
											Spent
											</a></b></u></td>';
								
								$href = "budget.php?sort=Monthly_Balance_Remaining&sortType=";
								if($sort=="Monthly_Balance_Remaining") {
									if($sortType=='asc') {
										$href = $href . "desc";
									}
									else {
										$href = $href . "asc";
									}
								}
								else { 			
									$href = $href . "asc";
								}
								echo '<td align="center"><u><b>
											<a href="' . $href . '">
											Balance
											</a></b></u></td>';
							?>
						</TR>
						<?php 
							$con = mysql_connect($host,$username,$password);
							if (!$con) {
								die('Could not connect: ' . mysql_error());
							}

							mysql_select_db($db_name, $con);
							
							$strSQL = "SELECT * FROM (SELECT  c.name as Category
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
															                ) CurrentSpending ON CurrentSpending.category_id = Averages.category_id) TBL";
							if($sort!="") {
								$strSQL = $strSQL . " ORDER BY " . $sort . " " . $sortType;
							}
							else {
								$strSQL = $strSQL . " ORDER BY Category ASC";
								$sort = "Category";
								$sortType = "asc";
							}
							
							$result = mysql_query($strSQL);

							while($row = mysql_fetch_array($result)) {
								echo "<tr align=top><td nowrap=nowrap>";
								echo $row['Category'] . "</td><td>$" . $row['Monthly_Budget'] . "</td><td>$" . $row['Current_Monthly_Spending'] . "</td>";
								if($row['Monthly_Balance_Remaining'] < 0) { 
									echo "<td bgcolor=#FF0000>";
								}
								elseif($row['Monthly_Balance_Remaining'] < (.25 * $row['Monthly_Budget'])) { 
									echo "<td bgcolor=#FFFF00>";
								}
								else {
									echo "<td>";
								}
								echo "$" . $row['Monthly_Balance_Remaining'] . "</td></tr>";
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
								echo "<tr align=top><td nowrap=nowrap>";
								echo "<b>Totals</b></td><td>$" . $row['Total_Monthly_Budget'] . "</td><td>$" . $row['Total_Monthly_Spending'] . "</td>";
								if($row['Total_Balance_Remaining'] < 0) { 
									echo "<td bgcolor=#FF0000>";
								}
								elseif($row['Total_Balance_Remaining'] < (.25 * $row['Total_Monthly_Budget'])) { 
									echo "<td bgcolor=#FFFF00>";
								}
								else {
									echo "<td>";
								}
								echo "$" . $row['Total_Balance_Remaining'] . "</td></tr>";
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
