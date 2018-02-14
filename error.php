<?php
/* Displays all error messages */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Error</title>
	<link href="css/upstrain.css" rel="stylesheet">
	<link href="css/logstyle.css" rel="stylesheet">
	<?php include 'top.php'; ?>
</head>

<body>
<div class="form">
    <h1 class="loginss">Error</h1>
    <p class="loginss">
    <?php 
    if( isset($_SESSION['message']) AND !empty($_SESSION['message']) ): 
        echo $_SESSION['message'];    
    else:
        header( "location: logsyst.php" );
    endif;
    ?>
    </p>     
    <a class="loginss" href="logsyst.php"><button class="button button-block"/>Home</button></a>
</div>
</body>
</html>
