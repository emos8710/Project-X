<!DOCTYPE html>


<?php

	if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	// Fetch the upstrain id from URL
	if (isset($_GET["upstrain_id"])) {
		$upstrain_id = $_GET["upstrain_id"];
	} else {
		?>
		<h3 style="color:red">Error: No ID specified</h3>
		<br>
		<a href="index.php">Go home</a>
		<?php
		exit();
	}

	include 'scripts/db.php';

	$id = mysqli_real_escape_string($link, $upstrain_id);
	$sql = "SELECT upstrain_id FROM entry_upstrain WHERE upstrain_id LIKE '$id'";
	
	$iserror = FALSE;
	$result = mysqli_query($link, $sql);
	if(!$result) {
		$iserror = TRUE;
		$error = mysqli_error();
	} elseif(mysqli_num_rows($result) < 1) {
		$iserror = TRUE;
		$error = "No such entry";
	}

	mysqli_close($link) or die("Could not close database connection");
	
	
	if (isset($_GET["edit"])) {
		$edit = TRUE;
	} else {
		$edit = FALSE;
	}

	if($iserror) {
		$title = "Error: ".$error;
	} else if($edit) {
			$title = "Edit entry ".$upstrain_id;
	} else {
		$title = "UpStrain - Entry ".$upstrain_id;
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

<!-- Body content of page -->

<?php 
if($edit) {
	include 'entry_edit.php';
} else {
	include 'entry_show.php';
}
?>


<?php include 'bottom.php'; ?>

</body>

</html>