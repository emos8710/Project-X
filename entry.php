<!DOCTYPE html>


<?php
	session_start();
	// fetch the upstrain id from URL
	$upstrain_id = $_GET["upstrain_id"];

	include 'scripts/db.php';

	$id = mysqli_real_escape_string($link, $upstrain_id);
	$sql = "SELECT upstrain_id FROM entry_upstrain WHERE upstrain_id LIKE '$id'";
	$result = mysqli_query($link, $sql);

	$iserror = FALSE;
	if(!$result) {
		$iserror = TRUE;
		$error = mysqli_error();
	} elseif(mysqli_num_rows($result) < 1) {
		$iserror = TRUE;
		$error = "No such entry";
	}

	mysqli_close($link) or die("Could not close database connection");

	if($iserror) {
		$title = "Error: ".$error;
	} else {
		$title = "UpStrain Entry ".$upstrain_id;
	}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title; ?></title>
	<link href="css/upstrain.css" rel="stylesheet">
</head>

<body>

	<?php include 'top.php'; ?>

	<main>
		<div class="innertube">

			<?php
			
			if($iserror) {
				echo "<h3>Error: ".$error."</h3>";
				echo "<br>".
				"<a href=\"javascript:history.go(-1)\">Go back</a>";
			} else {
				
				echo "<h2>UpStrain Entry ".$upstrain_id."</h2>";
				
				include 'scripts/db.php';
				
				$entrysql = "SELECT entry.comment AS cmt, entry.year_created AS year, "
				."entry.date_db AS date, entry.entry_reg AS biobrick, strain.name AS strain, entry.sequence AS seq, "
				."users.first_name AS fname, users.last_name AS lname FROM entry, entry_upstrain, "
				."users, strain WHERE entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = "
				."entry.id AND entry.creator = users.user_id AND entry.strain = strain.id";
				$entryquery = mysqli_query($link, $entrysql);
				
				$backbonesql = "SELECT backbone.name AS name, backbone.Bb_reg AS biobrick, "
				."backbone.year_created AS year, backbone.date_db AS date, users.first_name AS fname, "
				."users.last_name AS lname FROM backbone, entry, entry_upstrain, users WHERE "
				."entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = entry.id AND "
				."entry.backbone = backbone.id AND backbone.creator = users.user_id";
				$backbonequery = mysqli_query($link, $backbonesql);
				
				$insertsql = "SELECT ins.name AS ins, ins.ins_reg AS biobrick, ins.type AS type, ins.year_created AS year, "
				."ins.date_db AS date, users.first_name AS fname, users.last_name AS lname FROM ins, entry, entry_upstrain, "
				."users WHERE entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = entry.id AND entry.ins = "
				."ins.id AND ins.creator = users.user_id";
				$insertquery = mysqli_query($link, $insertsql);
				
				$filesql = "SELECT name_new AS filename FROM upstrain_file WHERE upstrain_file.upstrain_id = '$id'";
				$filequery = mysqli_query($link, $filesql);
				
				$rowserror = FALSE;
				$filerows = mysqli_num_rows($filequery);
				if($filerows < 1) {
					$hasfile = FALSE;
				} elseif($filerows == 1) {
					$hasfile = TRUE;
				}else {
					$rowserror = TRUE;
				}					
				
				$entryrows = mysqli_num_rows($entryquery);
				$backbonerows = mysqli_num_rows($backbonequery);
				$insertrows = mysqli_num_rows($insertquery);
				
				if(($entryrows > 1) || ($backbonerows > 1) || ($insertrows > 1)) {
					$rowserror = TRUE;
				}
				
				if($rowserror) {
					echo "<br>".gettype($filerows);
					echo "<br>".gettype($entryrows);
					echo "<br>".gettype($backbonerows);
					echo "<br>".gettype($insertrows);
					echo "<h3 style=\"color:red\">Error: Database returned unexpected number of rows</h3>";
				} else {
					
					
					echo "<p>"
					."<div class=\"entry_table\">"
					."<table>"
					."<th>Entry details</th>"
					."<th>Backbone</th>"
					."<th>Insert</th>"
					."</table>"
					."</div>";
									
					mysqli_close($link) or die("Could not close database connection");
					
				}
				
			}
			
			?>

		</div>
	</main>
	
	<?php include 'bottom.php'; ?>		

</body>

</html>