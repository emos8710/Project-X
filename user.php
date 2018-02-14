<?php
	if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	$iserror = FALSE;
	
	if (!isset($_GET['user_id'])) {
		$iserror = TRUE;
		$error = "No user id specified";
		$title = "Error: ".$error;
	} else {
		
		// Fetch the user id from URL
		$user_id = $_GET['user_id'];
	
		// Connect to database
		include 'scripts\db.php';
		
		// Check if user exists
		$id = mysqli_real_escape_string($link, $user_id);
		$sql = "SELECT user_id, username AS uname FROM users WHERE user_id LIKE '$id'";
		$result = mysqli_query($link, $sql);

		$iserror = FALSE;
		if(!$result) {
			$iserror = TRUE;
			$error = mysqli_error();
		} elseif(mysqli_num_rows($result) < 1) {
			$iserror = TRUE;
			$error = "No such user";
		} elseif(mysqli_num_rows($result) > 1) {
			$iserrr = TRUE;
			$error = "This should never happen";
		}

		if($iserror) {
			$title = "Error: ".$error;
		} else {
			$title = "User ".mysqli_fetch_assoc($result)['uname'];
			mysqli_free_result($result);
		}
		
		// Close database connection
		mysqli_close($link) or die("Could not close database connection");
		
		if (isset($_GET["edit"]) && $_GET["edit"] == "1") {
			$edit = TRUE;
		} else {
			$edit = FALSE;
		}
		
		if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
			$isuser = TRUE;
		}
		else {
			$isuser = FALSE;
		}
	}
	
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title; ?></title>
	<link href="css/upstrain.css" rel="stylesheet">
</head>

<body>
	<!-- Include site navigation -->
	<?php include 'top.php'; ?>
	
	<main>
		<div class="innertube">	
		
	
	<?php
	
	// Print error text...
	if($iserror) {
		echo "<h3>Error: ".$error."</h3>";
		echo "<br>".
		"<a href=\"javascript:history.go(-1)\">Go back</a>";
	} else {
		if($edit) {
			include 'user_edit.php';
		} else {
			include 'user_show.php';
		}
	}
	
	?>
	
		</div>
	</main>

	<!-- Include site footer -->
	<?php include 'bottom.php'; ?>		

</body>

</html>