<?php
/* Log out process, unsets and destroys session variables */
session_start();
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
          <h1 class="loginss">Thanks for stopping by</h1>
              
          <p class="loginss"><?= 'You have been logged out!'; ?></p>
          
          <a href="logsyst.php"><button class="button button-block"/>Home</button></a>

    </div>
</body>
</html>
