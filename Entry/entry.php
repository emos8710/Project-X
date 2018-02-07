<!DOCTYPE html>


	<?php
	session_start();
	// fetch the upstrain id from URL
	$upstrain_id = $_GET["upstrain_id"];
	
	include 'db.php';
	
	$id = mysqli_real_escape_string($link, $upstrain_id);
	$sql = "SELECT upstrain_id FROM entry_upstrain WHERE upstrain_id LIKE '$id'";
	$result = mysqli_query($link, $sql);

	$iserror = FALSE;
	if(!$result = mysqli_query($link, $sql)) {
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
				
				include 'db.php';
				mysqli_close($link) or die("Could not close database connection");
				
			}
			
			?>

		</div>
	</main>
	
	<?php include 'bottom.php'; ?>		

</body>

</html>