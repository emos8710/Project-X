<?php 
session_start();
?>
<?php
/* The login process */

// Protection against SQL-injections 
$username = $mysqli->escape_string($_POST['username']); 					// Extra characters are removed
$result = $mysqli->query("SELECT * FROM users WHERE username='$username'");	// $result becomes the row resulting from the query

// If $result contains no rows then the user does not exist
if ($result->num_rows==0){ 				
    $_SESSION['message'] = "The user does not exist! Try again or register.";
    header("location: error.php"); // The error message is sent to error.php
}
// Otherwise, the username exists
else { 
    $user = $result->fetch_assoc(); // $user is not an array containing the rows belonging to the matched username in the query

	// Checks if the entered password matches the password saved for the user
	// If the passwords match the results are saved to the session variables 
    if (password_verify($_POST['password'], $user['password'])) { 
		$_SESSION['user_id']		= $user['user_id'];
        $_SESSION['first_name'] 	= $user['first_name'];
        $_SESSION['last_name'] 		= $user['last_name'];
		$_SESSION['email'] 			= $user['email'];
		$_SESSION['phone'] 			= $user['phone'];
		$_SESSION['username'] 		= $user['username'];
        $_SESSION['active'] 		= $user['active'];
		$_SESSION['admin'] 			= $user['admin'];
        
		// The session is set to logged in
        $_SESSION['logged_in'] = true;

		// The user is sent to the profile page
        header("location: index.php");
    }
	// If the passwords do not match an error is sent to error.php
    else {
        $_SESSION['message'] = "The password you entered is incorrect! Try again or reset your password.";
        header("location: error.php");
    }
}

