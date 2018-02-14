<?php
	if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	// Fetch the user id from URL
	$user_id = $_GET["user_id"];
	
	// IMPLEMENT WHEN LOGIN WORKS
	if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true){
		$isloggedin = TRUE;
	}
	else {
		$isloggedin = FALSE;
	}
	
	if(isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
		$isadmin = TRUE;
	} else {
		$isadmin = FALSE;
	}
	
	if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
		$isuser = TRUE;
	} else {
		$isuser = FALSE;
	}
	
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
	
	<?php if($edit) {
		include 'user_edit.php';
	} else {
		include 'user_show.php';
	}
	?>
	
	<!-- Include site footer -->
	<?php include 'bottom.php'; ?>		

</body>

</html>