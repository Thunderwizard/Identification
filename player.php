<?php
	require("database.php");
	//$minUserLevel = 3;
	$requiredUserLevel = array(3,4,5);
  	include("auth/secure.php");
	
	require("functions.php");
	
	$result2 = mysql_query("SELECT * FROM players WHERE idPlayer = '$ID'") or die(mysql_error());
	$row2 = mysql_fetch_object($result2);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>NERO Midwest Player Database</title>
  <link type="text/css" href="nero.css" rel="stylesheet">
</head>

<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">
<table width="800" cellspacing="0" cellpadding="0" border="0">
  <tr>
    <td height="74" colspan="2"><?php include("header.php");?></td>
  </tr>
  <tr>
    <td width="150" valign="top">
      <?php include("nav.php");?>
    </td>
    <td width="650" valign="top">
    <!-- main content -->
    <br>
    <div align="center" class="CopyHeader">NERO Midwest Character Database</div>
    <br>
    <div align="center">
		
<table cellpadding="1" cellspacing="1" border="0" width="600">
  <tr><td colspan="4">&nbsp;</td></tr>
	<tr>
		<td class="copyheader" width="145" nowrap>Player Name:</td>
		<td width="5">&nbsp;</td>
		<td class="copytext"><?=$row2->firstName?> <?=$row2->lastName?></td>
    <td>&nbsp;</td>
	</tr>
  <tr>
		<td class="copyheader" width="145" nowrap>Membership Expires:</td>
		<td width="5">&nbsp;</td>
		<td class="copytext"><?=date('n/j/Y', strtotime($row2->memberdate));?></td>
    <td>&nbsp;</td>
	</tr>
  <tr>
		<td class="copyheader" width="145" nowrap>Current E-Mail:</td>
		<td width="5">&nbsp;</td>
		<td class="copytext"><?=$row2->email?></td>
    <td>&nbsp;</td>
	</tr>
  <tr>
		<td class="copyheader" width="145" nowrap>Goblin Points:</td>
		<td width="5">&nbsp;</td>
    <?php 
    $sql = "SELECT SUM(amount) as total FROM goblin_log WHERE idPlayer = '$ID'";
    $result = mysql_query($sql) or die (mysql_error());
    $goblins = mysql_fetch_object($result);
    ?>
		<td class="copytext"><?=$goblins->total?></td>
    <td>&nbsp;</td>
	</tr>
	
	<tr><td colspan="4"><hr></td></tr>
	<tr><td colspan="4" class="copyheader">Available Characters</td></tr>
	<?php
	$result3 = mysql_query("SELECT * FROM characters WHERE idPlayer = '$ID' ORDER BY idCharacter") or die(mysql_error());
	while ($char = mysql_fetch_object($result3)) {
	if($char->active == "dead") continue;
	// get class
	$result4 =mysql_query("SELECT name FROM class WHERE idClass = '$char->idClass'") or die(mysql_error());
	$class = mysql_fetch_object($result4);
	// get race
	$result5 =mysql_query("SELECT name FROM races WHERE idRace = '$char->idRace'") or die(mysql_error());
	$race = mysql_fetch_object($result5);
	
	$result2 =mysql_query("SELECT SUM(amountBlanket) as blanket, SUM(amountSilver) as silver, SUM(amountGoblins) as goblins FROM xp_log WHERE idCharacter = '$char->idCharacter'") or die(mysql_error());
	$level = mysql_fetch_object($result2);
	$totalXP = $level->blanket + $level->silver + $level->goblins;
	// get build totals
	$level = calcBuild($totalXP);
	$totalBP = listBuild($totalXP);
	?>
	<tr>
		<td class="copytext"><A href="character.php?idChar=<?=$char->idCharacter?>&idPlayer=<?=$ID?>"><?=$char->name?></a></td>
		<td>&nbsp;</td>
		<td class="copytext">Level <?=$level?> <?=$race->name?> <?=$class->name?> </td>
    <td class="copytext" width="40%">(<?=$totalBP?> build)</td>
	</tr>
	<?php
	}
?>
</table>
</div>
    <!-- main content -->
    </td>
  </tr>
</table>



</body>
</html>
