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
	<link href="upstrain.css" rel="stylesheet">
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
				."entry.date_db AS date, entry.entry_reg AS biobrick, entry.sequence AS seq, "
				."users.first_name AS fname, users.last_name AS lname FROM entry, entry_upstrain, "
				."users WHERE entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = "
				."entry.id AND entry.creator = users.user_id";
				
				$backbonesql = "SELECT backbone.name AS name, backbone.Bb_reg AS biobrick, "
				."backbone.year_created AS year, backbone.date_db AS date, users.first_name AS fname, "
				."users.last_name AS lname FROM backbone, entry, entry_upstrain, users WHERE "
				."entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = entry.id AND "
				."entry.backbone = backbone.id AND backbone.creator = users.user_id";
				
								
				
				
				mysqli_close($link) or die("Could not close database connection");
				
			}
			
			?>

		</div>
	</main>
	
	<?php include 'bottom.php'; ?>		

</body>

</html>