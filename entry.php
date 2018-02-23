<!DOCTYPE html>

<?php

	if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) { // restrict direct access
		session_start();
	}
	
	// URL variable parsing function
	function check_upstrain_id($input) {
	if (!is_string($input)) return FALSE;
	if (preg_match('/^UU[1-2][0-9]{6}$/', $input) == 1): return TRUE; else: return FALSE; endif;
}
	
	$is_upstrain_error = FALSE;
	$is_mysql_error = FALSE;
	
	// Fetch the upstrain id from URL
	if (isset($_GET["upstrain_id"])) {
		$upstrain_id = $_GET["upstrain_id"];
		if(!check_upstrain_id($upstrain_id)) {
			$is_upstrain_error = TRUE;
			$upstrain_error = "Invalid entry ID.";
		}
	} else {
		$is_upstrain_error = TRUE;
		$upstrain_error = "No entry ID specified";
	}

	if (!$is_upstrain_error) {
		include 'scripts/db.php';

		$id = mysqli_real_escape_string($link, $upstrain_id);
		$sql = "SELECT upstrain_id FROM entry_upstrain WHERE upstrain_id LIKE '$id'";
		
		$result = mysqli_query($link, $sql);
		if(!$result) {
			$is_mysql_error = TRUE;
			$mysql_error = mysqli_error();
		} elseif(mysqli_num_rows($result) < 1) {
			$is_mysql_error = TRUE;
			$mysql_error = "No such entry";
		}

		mysqli_close($link) or die("Could not close database connection");
	}
		
	// check if edit or show
	if (isset($_GET["edit"])) {
		$edit = TRUE;
	} else {
		$edit = FALSE;
	}

	// set page title
	if($is_upstrain_error) {
		$title = "ID error";
	} else if ($is_mysql_error) {
		$title = "Database Error";
	} else if($edit) {
		$title = "Edit entry ".$upstrain_id;
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

<!-- Body content of page -->

<main>
	<div class="innertube">
		<?php
		if($is_upstrain_error) {
			?>			
			<h3>
				Error:
				<br>
				<?php echo $upstrain_error ?>
			</h3>
			<br>
			<a href="javascript:history.go(-1)">Go back</a>
			<?php
		} else if($is_mysql_error) {
			?>			
			<h3>
				Error:
				<br>
				<?php echo $mysql_error ?>
			</h3>
			<br>
			<a href="javascript:history.go(-1)">Go back</a>
			<?php
		}else {
			if($edit) {
			include 'entry_edit.php';
			} else {
			include 'entry_show.php';
			}
		}
		?>
	</div>
</main>

<?php include 'bottom.php'; ?>

</body>
</html>