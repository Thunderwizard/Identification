<?php
	require("database.php");
	include("auth/secure.php");
	require("functions.php");
	
	$idChar = $_GET['idChar'];
	$idPlayer = $_GET['idPlayer'];
	$id = $_GET['idPlayer'];
	
	$result = mysql_query("SELECT name, firstName, lastName, idClass, idRace, magic_type FROM characters, players WHERE players.idPlayer = characters.idPlayer AND players.idPlayer = '$idPlayer' AND idCharacter ='$idChar'") or die(mysql_error());
	$row = mysql_fetch_object($result);
	
?>
<html>
<head>
	<title>NERO Midwest Database: <?=$row->name?></title>
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
   <div align="center">
<table cellpadding="0" cellspacing="0" border="0" width="600">
	<tr><td class="copyheader" align="center" colspan="4">NERO Midwest Character Sheet<br><br></td></tr>
	<tr>
		<td class="newsheader" width="145">Player's Name:</td>
		<td width="5">&nbsp;</td>
		<?php if ($userLevel <= 3) { ?>
		<td class="copytext" width="450" colspan="2"><a href="admin.php?id=<?=$idPlayer?>"><?=$row->firstName?> <?=$row->lastName?></a></td>
		<?php } else { ?>
		<td class="copytext" width="450" colspan="2"><a href="player.php?id=<?=$idPlayer?>"><?=$row->firstName?> <?=$row->lastName?></a></td>
		<?php } ?>
	</tr>
	<tr>
		<td class="newsheader">Character's Name:</td>
		<td width="5">&nbsp;</td>
		<td class="copytext" colspan="2"><?=$row->name?></td>
	</tr>
	<?php
	// get build totals
	$skillsql = "SELECT *, COUNT(*) as total FROM skills, skill_log WHERE idCharacter = '$idChar' AND skills.idSkill = skill_log.idSkill GROUP BY name ORDER BY skills.name";
	$result3 = mysql_query($skillsql) or die(mysql_error());
	$skillbuildcost = 0;
	while ($skill = mysql_fetch_object($result3)) {
		$skillcostsql = "SELECT * FROM skill_cost_class WHERE idSkill = '$skill->idSkill' AND idClass = '$row->idClass'";
		$skillcostresult = mysql_query($skillcostsql) or die (mysql_error());
		$skillcost = mysql_fetch_object($skillcostresult);
		$racemodsql = "SELECT * FROM skill_cost_race WHERE idSkill = '$skill->idSkill' AND idRace = '$row->idRace'";
		$racemodresult = mysql_query($racemodsql) or die (mysql_error());
		$racemod = mysql_fetch_object($racemodresult);
		$xx = 0;
	 	if($skill->total > "1") {
		// here is where you adjust the build costs
			$skill_cost = $skillcost->cost;
		// restart here
		// dwarf smithing check
		if ($skill->idSkill == 7 && $row->idRace == 4) {
			$skill_cost = $skill_cost - $racemod->cost;
		} elseif ($skill->idSkill == 10 && $row->idRace == 4) {
			$skill_cost = $skill_cost - $racemod->cost;
		} 
		//Mystic Wood Elves
		if ($skill->idSkill == 9 && $row->idRace == 10) {
			$skill_cost = $skill_cost - 1;
		}
		// Master prof build increase
		elseif ($skill->idSkill == 68 && $row->idClass == 1) {
			$x = $skill->total; 
			$y = 5; 
			$wptotal = floor($x / $y);
			if ($wptotal > 0) {
				//echo $wptotal."<br>";
				$wptotal = $wptotal * 2;
				//echo $wptotal."<br>";
			}
		}
		//templar Crit Attack check
		elseif ($skill->type == 10 && $row->idClass > 4) {
			if ($skill->total > 5 && $skill->total < 11) {
				//$skill_cost = $skill_cost + 1;
			} elseif ($skill->total >= 11) {
				//$skill_cost = $skill_cost + 2;
			} 
		}
		//secondary magic check and double costs
		if ($skill->type == 4 || $skill->type == 6) { //check if earth or celestial magic
			if ($skill->type != $row->magic_type) { //check if values are not equal 
				$skill_cost = $skill_cost * 2; //not equal is doubled
			}
		}
		// figure out subtotal and work it properly if the class is templar
		if ($skill->type == 10 && $row->idClass >= 5) {	
			if ($skill->total > 5 && $skill->total < 11) {
				$skill_cost = $skill_cost + 1;
				$subtotalcost = (($skill->total - 5) * $skill_cost) + 15;
			} elseif ($skill->total >= 11) {
				$skill_cost = $skill_cost + 2;
				$subtotalcost = (($skill->total - 10) * $skill_cost) + 35;
			} else {
				$subtotalcost = ($skill->total * $skill_cost);
			}
		} else { 
		// Individual skills are listed below
		// Adjust for Master Profs
		if ($skill->idSkill == 68 && $row->idClass == 1) {
			$subtotalcost = ($skill->total * $skill_cost) + $wptotal;
		} else {
		// final subtotal
			$subtotalcost = ($skill->total * $skill_cost);
			}
		}
		// add subtotal cost to build cost
		$skillbuildcost = $skillbuildcost + $subtotalcost; // otherwise standard cost
		// debug text
		//print $skill->name . " x" . $skill->total." x  ". $skill_cost ." = ".$subtotalcost." - ".$skillbuildcost."<br>"; 
		}
		else {
		$skill_cost = $skillcost->cost;
		// dwarf smithing check
		if ($skill->idSkill == 7 && $row->idRace == 4) {
			$skill_cost = $skill_cost - 1;
		}
		if ($skill->idSkill == 10 && $row->idRace == 4) {
			$skill_cost = $skill_cost - 1;
		}
		// scavenger check
		if ($skill->idSkill == 23 && $row->idRace == 20) {
			$skill_cost = $skill_cost * 2;
		}
		if ($skill->idSkill == 24 && $row->idRace == 20) {
			$skill_cost = $skill_cost * 2;
		}
		//Half-orc check
		elseif ($skill->idSkill == 23 && $row->idRace == 11) {
			$skill_cost = $skill_cost * 2;
		}
		// elf archery check
		if ($skill->idSkill == 47 && $row->idRace == $racemod->idRace) {
			$skill_cost = ceil($skill_cost / $racemod->cost);
		}
		//secondary magic check and double
		if ($skill->type == 4 || $skill->type == 6) { //check if earth or celestial magic
			if ($skill->type != $row->magic_type) { //check if values are not equal 
				$skill_cost = $skill_cost * 2; //not equal is doubled
			}
		}
		//single skill costs - run magic check here too
			$skillbuildcost = $skillbuildcost + $skill_cost;
		//print $skill->name." - ".$skill_cost." - ".$skillbuildcost."<br>";
	 	}
	}
	$totalXP = total_xp($idChar);
	$level = calcBuild($totalXP);
	$totalBP = listBuild($totalXP);
	$looseBP = (listBuild($totalXP) - $skillbuildcost);
	$looseXP = listLXP($totalXP);
		?>
	<tr>
		<td class="newsheader" valign="top">Character's Level:</td>
		<td width="5">&nbsp;</td>
		<td class="copytext" valign="top"><?=$level?></td>
		<td>
				<table cellpadding="0" cellspacing="1" border="0">
					<tr>
						<td class="copytext" align="right">Total BP:</td>
						<td class="copytext"><?=$totalBP?></td>
					</tr>
					<tr>
						<td class="copytext" align="right">Loose BP:</td>
						<td class="copytext"><?=$looseBP?></td>
					</tr>
					<tr>
						<td class="copytext" align="right">Loose XP:</td>
						<td class="copytext"><?=$looseXP?></td>
					</tr>
				</table>
			</div>	
		</td>
	</tr>
	<?php
	// get class
	$result4 =mysql_query("SELECT name FROM class WHERE idClass = '$row->idClass'") or die(mysql_error());
	$class = mysql_fetch_object($result4);
	// get race
	$result5 =mysql_query("SELECT * FROM races WHERE idRace = '$row->idRace'") or die(mysql_error());
	$race = mysql_fetch_object($result5);
	?>
	<tr>
		<td class="newsheader" width="145">Character's Race/Class:</td>
		<td width="5">&nbsp;</td>
		<td class="copytext" colspan="2"><?=$race->name?> <?=$class->name?></td>
	</tr>
	<?php 
		//get body points
		$body = body($level,$row->idClass);
		$body = $body + $race->hp_mod;
   ?>
	<tr>
		<td class="newsheader" width="145">Body Points:</td>
		<td width="5">&nbsp;</td>
		<td class="copytext" colspan="2"><?=$body?></td>
	</tr>
	<?php
	// get death count
	$deathsql = "SELECT idCharacter, count(*) as death FROM deaths WHERE idCharacter = '$idChar' GROUP BY idCharacter";
	$result6 =mysql_query($deathsql) or die(mysql_error());
	$death = mysql_fetch_object($result6);
	//get adjudications
	$adjudicatesql = "SELECT count(adjudicated) as adj FROM deaths WHERE idCharacter = '$idChar' AND adjudicated ='-1'";
	$result7 =mysql_query($adjudicatesql) or die(mysql_error());
	$adjudicate = mysql_fetch_object($result7);
	$a  = $death->death;
	$b = $adjudicate->adj;
	$deathcount = ($a - $b);
	?>
	<tr>
		<td class="newsheader" width="145">
		<?php
		 if ($userLevel == 1) { ?><a class="select" href="character.php?idChar=<?=$idChar?>&idPlayer=<?=$idPlayer?>">Deaths:</a><?php }
		 else {?>
		 Deaths:
		 <?php } ?>
		
		</td>
		<td width="5">&nbsp;</td>
		<td class="copytext" colspan="2"><?=$deathcount?></td>
	</tr>
	<tr><td colspan="4">&nbsp;</td></tr>
	<tr>
		<td class="newsheader" valign="top">Character Skills:</td>
		<td width="5">&nbsp;</td>
		<td class="copytext" colspan="2">
		  <?php
			//get skill list
			$previousSkill = "";
			$skillsql = "SELECT *, COUNT(*) as total FROM skills, skill_log WHERE idCharacter = '$idChar' AND skills.idSkill = skill_log.idSkill GROUP BY name ORDER BY skills.name";
			$result3 = mysql_query($skillsql) or die(mysql_error());
 			$skillcost = 0;
			while ($skill = mysql_fetch_object($result3)) {
				$skill_mod = $skill->skill_mod;
				$skillcostsql = "SELECT * FROM skill_cost_class WHERE idSkill = '$skill->idSkill' AND idClass = '$row->idClass'";
				$skillcostresult = mysql_query($skillcostsql) or die (mysql_error());
				$skillcost = mysql_fetch_object($skillcostresult);
	  		if(!@substr_count($skill->name, $previousSkill)) {
				// start listing skills
		      if(@substr_count($skill->name, 'Level')) {
          	$shortName = substr($skill->name, 0, -8);
		        print $shortName . " ";
		        for($i=1;$i<10;$i++) { $magicTotal[$i] = 0; }
		        $magics = mysql_query("SELECT *, COUNT(*) as total FROM skills, skill_log WHERE idCharacter = '$idChar' AND skills.idSkill = skill_log.idSkill GROUP BY name ORDER BY skills.name") or die ("Muhahahaha - " . mysql_error());
		        while($magicrow = mysql_fetch_object($magics)) {
		          if(@substr_count($magicrow->name, $shortName)) {
		            $blah = substr($magicrow->name, -1);
								$magicTotal[$blah] = $magicrow->total;
		          }
		        }
		        for($i=1;$i<10;$i++) {
		          print $magicTotal[$i]." ";
				  	if($i == 3 || $i == 6) { print "/ "; }
					if($i == 9) { print "<br>"; }
		        }
		      	} elseif($skill->idSkill == 19) {
						$a = $skill->total; 
						$b = 5;
						$bstotal = (floor($a / $b)*2);
						$r = ($a % $b); 
						print "Backstab +" . $bstotal."<br>";
		        if ($r!=0) {
						print "Back Attack x" . $r."<br>"; 
						}
          	} elseif($skill->idSkill == "65") {
				$x = $skill->total; 
				$y = 5; 
				$wptotal = floor($x / $y);
				$r = ($x % $y); 
				if ($wptotal > 0) {
					print "Weapon Proficiency +" . $wptotal."<br>";
				}
		        if ($r!=0) {
					print "Critical Attack x" . $r."<br>"; 
				}
            } elseif($skill->idSkill == "68") {
				$x = $skill->total; 
				$y = 5; 
				$wptotal = floor($x / $y);
				$r = ($x % $y); 
				print "Master Weapon Proficiency +" . $wptotal."<br>";
		        if ($r!=0) {
					print "Master Critical Attack x" . $r."<br>"; 
				}
			} elseif($skill->idSkill == "9") {
				$co_sql = "SELECT *, COUNT(*) as total FROM skill_log, craftsman WHERE  idCharacter = '$idChar' AND idSkill = '9' AND skill_log.skill_mod = craftsman.idCraftsman GROUP BY name ORDER BY craftsman.name";
				$co_result = mysql_query($co_sql) or die(mysql_error());
				while ($co = mysql_fetch_object($co_result)) {
					if ($co->total > 1) {
						print  "Craftsman (" . $co->name .  ")  x" . $co->total."<br>"; 
						} else {
						print  "Craftsman (" . $co->name .  ")<br>"; 
					}
				}
			} elseif($skill->idSkill == "34") {
		        print $skill->name . " x" . $skill->total."<br>"; 
			} elseif($skill->total > "1") {
		        print $skill->name . " x" . $skill->total."<br>"; 
		      	} else {
		        print $skill->name."<br>";
		      }
					
					}
	  $previousSkill = $skill->name;
	  if(@substr_count($skill->name, 'Magic')) { $previousSkill = $shortName; }
	}
		  ?>
		</td>
	</tr>
	
	<tr><td colspan="4">&nbsp;</td></tr>
	<tr>
		<td class="newsheader" valign="top">
		<?php
		 if ($userLevel == 1) { ?><a class="select" href="notes_edit.php?idChar=<?=$idChar?>&idPlayer=<?=$idPlayer?>">Special Notes:</a><?php }
		 else {?>
		 Special Notes:
		 <?php } ?>
			
		</td>
		<td width="5">&nbsp;</td>
		<td class="copytext" colspan="2">
		<?php
		// get notes
		$notessql = "SELECT * FROM notes WHERE idCharacter = '$idChar' ORDER BY idNote";
		$result8 = mysql_query($notessql) or die(mysql_error());
		while ($notes = mysql_fetch_object($result8)) { 
		?>
		<?=$notes->note?><br>
		<?php } ?>
		</td>
	</tr>
	
	<tr><td colspan="4">&nbsp;</td></tr>
	<tr>
		<td class="newsheader">Membership Expires:</td>
		<td width="5">&nbsp;</td>
		<td class="copytext" colspan="2">
		<?php
	// get notes
	$memberdatesql = "SELECT memberdate FROM players WHERE idPlayer = '$idPlayer'";
	$result9 = mysql_query($memberdatesql) or die(mysql_error());
	$expires = mysql_fetch_object($result9); 
	?>
	<?=date('n/j/Y', strtotime($expires->memberdate));?></td>
	</tr>
	<tr>
		<td class="newsheader">Last Updated:</td>
		<td width="5">&nbsp;</td>
		<td class="copytext" colspan="2">
		<?php
	// get date last updated
	$updatesql = "SELECT * FROM xp_log WHERE idCharacter = '$idChar'";
	$result9 = mysql_query($updatesql) or die(mysql_error());
	while ($update = mysql_fetch_object($result9)) {
    $lastupdated = date('n/j/Y', strtotime($update->date));
		}
    ?>
		<?=$lastupdated?>
		</td>
	</tr>	
	<tr>
		<td class="newsheader">Goblin Points:</td>
		<td width="5">&nbsp;</td>
		<td class="copytext" colspan="2">
		<?php 
    $sql = "SELECT SUM(amount) as total FROM goblin_log WHERE idPlayer = '$idPlayer'";
    $result = mysql_query($sql) or die (mysql_error());
    $goblins = mysql_fetch_object($result);
    
		if (empty($goblins->total)) {$gobs = 0;}
			else
		{$gobs = $goblins->total;}
		 
		echo $gobs?>
		</td>
	</tr>	
</table>
</div>
    <!-- main content -->
    </td>
  </tr>
</table>



</body>
</html>
