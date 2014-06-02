<?php
	require("estate/database.php");
	$query = $_POST['query'];
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>NERO Component ID Identify!</title>
  <link type="text/css" href="style.css" rel="stylesheet">
</head>

<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
    <div align="center">
		<table width="75%" align="center" border="0">
			<tr>
				<td colspan="2" align="center">
					<h1>Identify!</h1>
				</td>
			</tr>
			<tr>
				<form action="identify.php" method="post">
				<td class="CopyText" align="center" width="35%" valign="top">
					Enter ID number: 
					<input type="text" name="query" size="6" maxlength="6" class="form" tabindex="1">
					<input type="submit" value="Identify!" class="form" size="6">
				</td>
				</form>
				<?php
				 ?>
				
				<td class="CopyText" width="65%" valign="top">
				<?php 
					// If the form has been submitted, a variable will be sent to the same page and
					// the following code will be run
					if (!empty($query)) {
					$sql = "SELECT * FROM components WHERE national_id = '" . $query . "'";
					$result = mysql_query($sql) or die(mysql_error());
					$row = mysql_fetch_object($result);
					// if number entered is not valid, hide detail information below
					if (mysql_num_rows($result)==0) { 
					echo $query . " is not a component";
					echo "<div style='display:none'>";
					}
					$type = substr($row->type,0,1);
					// check for the strength of item
					switch ($row->rank) {
						case "1";
							$y = "Common";
							break;
						case "2";
							$y = "Rare";
							break;
						case "4";
							$y = "Singular";
							break;
					}
					?>
						ID Number: <?=$row->national_id?><br>
						Detailed Description: <?=$row->desc_full?> <br>
						Visible as a: <?=$row->desc_short?> <br>
						This is a <?=$y?> <?=$row->type?> (<?=$type?><?=$row->rank?>) component.
					<?php
					} else {
					?>
					
					Please enter the identify number.
					<?php
					}
					?>
					</div>
			</tr>
		</table>	
	</div>
    <!-- main content -->
    </td>
  </tr>
</table>

</body>
</html>