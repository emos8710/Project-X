<?php
/* Displays user information and some useful messages */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in using the session variable
// Makes it easier to read
$first_name	= $_SESSION['first_name'];
$last_name 	= $_SESSION['last_name'];
$email 		= $_SESSION['email'];
$active 	= $_SESSION['active'];

?>

<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
	<title>Welcome <?php echo $first_name.' '.$last_name; ?></title>
	<link href="css/upstrain.css" rel="stylesheet">
</head>

<body>
<?php include 'top.php'; ?>
<main>
  <div class="form">

          <h1 class="login">Thank you for registering!</h1>
          
          <p class="login">
          <?php 
     
          // Display message about account verification link only once
          if ( isset($_SESSION['message']) ){
              echo $_SESSION['message'];
              //unset( $_SESSION['message'] ); // No message upon refresh
          }
          
          ?> 
		  </p>

    </div>
    
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="js/index.js"></script>
</main>
<?php include 'bottom.php'; ?>
</body>
</html>
