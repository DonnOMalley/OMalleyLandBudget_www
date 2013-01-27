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
		
		<form method="post" action="paymentBudgetCalculator.php">
			<table border=1>
				<tr>
					<td align="center"><b><u>Payment Amount(in $)</b></u></td>
					<td align="center"><b><u>Savings Percentage(%)</b></u></td>
				</tr>
				<tr>
					<td>
						<input type='text' name='paymentAmount' />
					</td>
					<td>
						<input type='text' name='savingsPercentage' />
					</td>
				</tr>
				<tr>
					<td colspan=2 align="center"><input value="Update" type="submit"></td>
				</tr>
			<table>
		</form>


		<TABLE>
			<TR>
				<TD valign="top">
					<TABLE BORDER=1>
						<TR>
							<?php
								$paymentAmount = $_POST['paymentAmount'];
								$savingsPercentage = $_POST['savingsPercentage'];
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

								//No Need for link here as this will be a constant value
								echo '<td align="center"><u><b>
												Payment Amount
											</b></u></td>';

								//No Need for link here as this will be a constant value
								echo '<td align="center"><u><b>
												Savings Percentage
											</b></u></td>';
								
								$href = "budget.php?sort=Current_Monthly_Spending&sortType=";
								if($sort=="Budgeted_Amount_By_Percentages") {
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
											Pay Period Budget
											</a></b></u></td>';
							?>
						</TR>
						<?php 
							$con = mysql_connect($host,$username,$password);
							if (!$con) {
								die('Could not connect: ' . mysql_error());
							}

							mysql_select_db($db_name, $con);

							//These need to be replaced by values passed in later
							$salary_amount = "2350";
							$savings_percentage	= "5";
							if($paymentAmount <> "") {
								$salary_amount = $paymentAmount;
							}
							if($savingsPercentage <> "") {
								$savings_percentage = $savingsPercentage;
							}
							

							//Get Budgets by percentages, Don't deduct from Bills (hence - 1 in count)
							// Requires a hard coded salary amount and savings percentage
							$strSQL = "SELECT  c.name, 
																" . $salary_amount . " AS Salary_Amount,
																" . $savings_percentage . " as Savings_Percentage,
																CAST(" . $salary_amount . " * (CASE c.name
																																	 WHEN 'Bills' THEN (sum(d.amount) / debit_totals.total_amount)
																																	 ELSE (((sum(d.amount) / debit_totals.total_amount) * 100) - (" . $savings_percentage . " / category_list.list_count)) / 100
																																END) AS DECIMAL(10,2)) as Budgeted_Amount_By_Percentages
												FROM OMalleyLandBudget.Debits d 
												JOIN OMalleyLandBudget.Categories c on d.category_id = c.id
												JOIN (SELECT count(distinct c2.name) - 1 as list_count 
															FROM OMalleyLandBudget.Categories c2 
															JOIN OMalleyLandBudget.Debits d2 on d2.category_id = c2.id) as category_list
												JOIN (SELECT SUM(amount) as total_amount 
															FROM OMalleyLandBudget. Debits) as debit_totals
												GROUP BY c.name
												ORDER BY c.name";
							
							$result = mysql_query($strSQL);

							while($row = mysql_fetch_array($result)) {
								echo "<tr align=top><td nowrap=nowrap>";
								echo $row['name'] . "</td><td align=center>$" . $row['Salary_Amount'] . "</td><td align=center>" . $row['Savings_Percentage'] . "%</td><td>$" . $row['Budgeted_Amount_By_Percentages'];
								echo "</td></tr>";
							}
							echo "<tr><td>Savings</td><td colspan=3 align=center>$" . sprintf('%0.2f',($salary_amount * ($savings_percentage / 100))) . "</td></tr>";

							mysql_close($con);
						?>
					</TABLE>
				</TD>
			</TR>
		</TABLE>
	</CENTER>
</body>
</html>
