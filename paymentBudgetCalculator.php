<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html><head>
  
  <meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">

  <title>OMalleyLand Budget - Budget</title>
  
<?php 

	include 'includes/common.php';
	include 'includes/database.php';
	include 'includes/classes.php';

	// Connect to server and select database.
	$mysqli = new mysqli("$HOST", "$DB_USER", "$DB_PASSWORD", "$DB_NAME");
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		$paymentAmount = $_POST['paymentAmount'];
		$savingsPercentage = $_POST['savingsPercentage'];
	}
	else {
		$paymentAmount = $_GET['paymentAmount'];
		$savingsPercentage = $_GET['savingsPercentage'];
		if(StrLen($paymentAmount)==0) {
			$paymentAmount = 2540;
		}
		if(StrLen($savingsPercentage)==0) {
			$savingsPercentage = 5;
		}
	}
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
						<input type='text' name='paymentAmount' <?php echo "value=" . $paymentAmount; ?> />
					</td>
					<td>
						<input type='text' name='savingsPercentage' <?php echo "value=" . $savingsPercentage; ?> />
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
							
								$sort = $_GET["sort"];
								$sortType = $_GET["sortType"];
								$href = "paymentBudgetCalculator.php?paymentAmount=" . $paymentAmount . "&savingsPercentage=" . $savingsPercentage . "&sort=Category&sortType=";
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
											
								echo '<td></td>';

								//No Need for link here as this will be a constant value
								//echo '<td align="center"><u><b>
								//				Payment Amount
								//			</b></u></td>';

								//No Need for link here as this will be a constant value
								//echo '<td align="center"><u><b>
								//				Savings Percentage
								//			</b></u></td>';
								
								$href = "paymentBudgetCalculator.php?paymentAmount=" . $paymentAmount . "&savingsPercentage=" . $savingsPercentage . "&sort=Current_Monthly_Spending&sortType=";
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
											Pay Period Budget
											</a></b></u></td>';
							?>
						</TR>
						<?php 						

							//Get Budgets by percentages, Don't deduct from Bills (hence - 1 in count)
							// Requires a hard coded salary amount and savings percentage
							$strSQL = "SELECT * FROM (
													SELECT  c.name, 
																	" . $paymentAmount . " AS Salary_Amount,
																	" . $savingsPercentage . " as Savings_Percentage,
																	CAST(" . $paymentAmount . " * (CASE c.name
																																		 WHEN 'Bills' THEN (sum(d.amount) / debit_totals.total_amount)
																																		 ELSE (((sum(d.amount) / debit_totals.total_amount) * 100) - (" . $savingsPercentage . " / category_list.list_count)) / 100
																																	END) AS DECIMAL(10,2)) as Budgeted_Amount_By_Percentages
													FROM " . $DB_NAME . "." . $TABLE_DEBITS . " d 
													JOIN " . $DB_NAME . "." . $TABLE_CATEGORIES . " c on d.category_id = c.id
													JOIN (SELECT count(distinct c2.name) - 1 as list_count 
																FROM " . $DB_NAME . "." . $TABLE_CATEGORIES . " c2 
																JOIN " . $DB_NAME . "." . $TABLE_DEBITS . " d2 on d2.category_id = c2.id) as category_list
													JOIN (SELECT SUM(amount) as total_amount 
																FROM " . $DB_NAME . ". " . $TABLE_DEBITS . ") as debit_totals
													GROUP BY c.name) TBL ";
							if($sort=="Category") {
								$strSQL = $strSQL . " ORDER BY name " . $sortType;
							}
							if(StrLen($sort)==0) {
								$strSQL = $strSQL . " ORDER BY name " . $sortType;
							}
							if($sort=="Current_Monthly_Spending") {
								$strSQL = $strSQL . " ORDER BY Budgeted_Amount_By_Percentages " . $sortType;
							}
							
							$result = $mysqli->query($strSQL);

							while($row = $result->fetch_array(MYSQLI_BOTH)) {
								echo "<tr align=top><td nowrap=nowrap>";
								//echo $row['name'] . "</td><td align=center>$" . $row['Salary_Amount'] . "</td><td align=center>" . $row['Savings_Percentage'] . "%</td><td>$" . $row['Budgeted_Amount_By_Percentages'];
								echo $row['name'] . "</td><td>&nbsp;</td><td align=center>$" . $row['Budgeted_Amount_By_Percentages'];
								echo "</td></tr>";
							}
							//echo "<tr><td>Savings</td><td colspan=3 align=center>$" . sprintf('%0.2f',($paymentAmount * ($savingsPercentage / 100))) . "</td></tr>";
							echo "<tr><td></td><td></td><td></td></tr>";
							echo "<tr><td><b>Savings</b></td><td>&nbsp;</td><td align=center><b>$" . sprintf('%0.2f',($paymentAmount * ($savingsPercentage / 100))) . "</b></td></tr>";							

							$mysqli->close();
						?>
					</TABLE>
				</TD>
			</TR>
		</TABLE>
	</CENTER>
</body>
</html>
