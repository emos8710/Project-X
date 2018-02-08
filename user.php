<!DOCTYPE html>


<?php
	session_start();
	// Fetch the user id from URL
	$user_id = $_GET["user_id"];
	
	// Connect to database
	include 'scripts\db.php';
	
	// Check if user exists
	$id = mysqli_real_escape_string($link, $user_id);
	$sql = "SELECT user_id FROM users WHERE user_id LIKE '$id'";
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
	
	// Close database connection
	mysqli_close($link) or die("Could not close database connection");

	if($iserror) {
		$title = "Error: ".$error;
	} else {
		$title = "User ".$user_id;
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
			
			if($iserror) {
				echo "<h3>Error: ".$error."</h3>";
				echo "<br>".
				"<a href=\"javascript:history.go(-1)\">Go back</a>";
			} else {
				
				echo "<h2>User ".$user_id."</h2>";
				
				include 'scripts\db.php';
				
				$entrysql = "SELECT users.first_name AS fname, users.last_name AS lname, "
				."users.email AS email, users.phone AS phone, users.username AS uname FROM users WHERE user_id = '$id'";
				
				mysqli_close($link) or die("Could not close database connection");
				
			}
			
			?>

		</div>
	</main>
	<!-- Include site footer -->
	<?php include 'bottom.php'; ?>		

</body>

</html>