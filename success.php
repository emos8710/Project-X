<?php
/* Displays all successful messages */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Success</title>
	<link href="css/upstrain.css" rel="stylesheet">
</head>

<body>
	<?php include 'top.php'; ?>
<main>
<div class="form">
    <h1 class="login"><?= 'Success'; ?></h1>
    <p class="login">
    <?php 
    if( isset($_SESSION['message']) AND !empty($_SESSION['message']) ){
        echo $_SESSION['message'];
	}
    else{
        header( "location: logsyst.php" );
	}
    ?>
    </p>
</div>
</main>
	<?php include 'bottom.php'; ?>
</body>
</html>
