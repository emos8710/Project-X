<?php
/* Log out process, unsets and destroys session variables */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy(); 
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Logged out</title>
	<link href="css/upstrain.css" rel="stylesheet">
	<link href="css/logstyle.css" rel="stylesheet">
	<?php include 'top.php'; ?>
</head>

<body>
    <div class="form">
          <h1 class="loginss">You have been logged out!</h1>
    </div>
</body>
</html>
