<?php
/* Password reset process, updates database with new user password */
require 'db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Make sure the form is being submitted with method="post"
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
	// Checks that the password is long enough
	if (!strlen($_POST['newpassword'])<8) {
		// Make sure the two passwords match
		if ( $_POST['newpassword'] == $_POST['confirmpassword'] ) { 
	
			$new_password = password_hash($_POST['newpassword'], PASSWORD_BCRYPT);
        
			// We get $_POST['email'] and $_POST['hash'] from the hidden input field of reset.php form
			$email = $mysqli->escape_string($_POST['email']);
			$hash = $mysqli->escape_string($_POST['hash']);
        
			$sql = "UPDATE users SET password='$new_password', hash='$hash' WHERE email='$email'";

			if ( $mysqli->query($sql) ) {

			$_SESSION['message'] = "Your password has been reset successfully!";
			header("location: success.php");    

			}

		}
		else {
			$_SESSION['message'] = "Two passwords you entered don't match, try again!";
			header("location: error.php");    
		}

	}
	else {
		$_SESSION['message']="The new password is too short! Please try again. The password must be at least 8 characters long.";
		header("location: error.php");
	}
?>