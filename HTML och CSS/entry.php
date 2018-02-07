<!DOCTYPE html>


	<?php
	session_start();
	// fetch the upstrain id from URL (!!make sure this variable is passed from results table!!)
	$upstrain_id = $_GET["id"];
	define('title',"upStrain Entry ".$upstrain_id);
	exit();
	?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo title; ?></title>
	<link href="upstrain.css" rel="stylesheet">
</head>

<body>

<?php

include 'db.php';
// database interaction goes here
mysqli_close($link) or die("Could not close database connection");

?>

</body>

</html>