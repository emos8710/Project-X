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

</head>

<body>
	<?php include 'top.php'; ?>
<main>
    <div class="form">
          <h1 class="login">You have been logged out!</h1>
    </div>
</main>
	<?php include 'bottom.php'; ?>
</body>
</html>
