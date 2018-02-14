<?php 
/* Reset Password form */
require 'db.php';
session_start();

// Checks if the form is submitted with method="post"
if ($_SERVER['REQUEST_METHOD']=='POST'){
	$email 		= $mysqli->escape_string($_POST['email']);
	
	// checks if the email is registered in the system
    $result 	= $mysqli->query("SELECT * FROM users WHERE email='$email'");
	
	// If the query returns zero rows, the user does not exist
    if ($result->num_rows==0){ 
        $_SESSION['message'] = "There is no user with that email! Try another email or register for a new account.";
        header("location: error.php");
    }
	// If the query returns more than zero rows, the user exists
    else {
		// The query result is stored in $user
        $user = $result->fetch_assoc(); 
        
        $email 		= $user['email'];
        $hash 		= $user['hash'];
        $first_name = $user['first_name'];

        $_SESSION['message'] = "<p>A reset link has been sent to your email <span>$email</span>!</p>";

        // Send registration confirmation link (reset.php)
        $to      		= $email;
        $subject 		= 'Password Reset Link (UpStrain)';
        $message_body 	= 	'Hi '.$first_name.',
		
							Please click on this link to reset your password:

							http://localhost/login-system/reset.php?email='.$email.'&hash='.$hash;  

		mail($to, $subject, $message_body);
		
		// The user is redirected to the success page
        header("location: success.php");
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Reset Password</title>
	<link href="css/upstrain.css" rel="stylesheet">
	<link href="css/logstyle.css" rel="stylesheet">
	<?php include 'top.php'; ?>
</head>

<body>  
	<div class="form">
    <h1 class="loginss">Reset Your Password</h1>

		<form action="forgot.php" method="post">
			<div class="field-wrap">
				<label>
					Email Address<span class="req">*</span>
				</label>
				<input class="loginss" type="email"required autocomplete="off" name="email"/>
			</div>
	
			<p class="login"><a class="loginss" href="logsyst.php">Back to the login page</a></p>
	
			<button class="button button-block"/>Reset</button>
		</form>
	</div>
          
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="js/index.js"></script>
</body>

</html>
