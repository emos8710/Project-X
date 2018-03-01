<?php

/* if (count(get_included_files()) == 1) exit("Access restricted.");*/
 
/* Verifies the user */
require 'scripts/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Makes sure that the email and hash variables aren't empty
if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
    $email 	= $mysqli->escape_string($_GET['email']); 
    $hash 	= $mysqli->escape_string($_GET['hash']); 
    
    // Find the user with the email and hash, which haven't verified their account yet 
    $result = $mysqli->query("SELECT * FROM users WHERE email='$email' AND hash='$hash' AND active='0'");

    if ($result->num_rows == 0){ 
        $_SESSION['message'] = "The account has already been activated.";
        header("location: error.php");
    }
    else {
		$user = $result->fetch_assoc(); // $user is now an array containing the rows belonging to the matched username in the query
        $_SESSION['user_id'] = $user['user_id'];
		$_SESSION['first_name'] = $user['first_name'];
		$_SESSION['message'] = "You have activated ".$_SESSION['first_name']."s account!";
        
        // Set the user status to active (active = 1)
        $mysqli->query("UPDATE users SET active='1' WHERE email='$email'") or die($mysqli->error);
        $_SESSION['active'] = 1;
        header("location: success.php");
		
		// Send an email to the user to let them know they are verified.
		$currentyear	= $date('Y');
		$toemail		= $user['email'];
		$subject 		= 'Welcome!';
		$message_body 	= 	'Hi,'
							.'You have now been verified as a member of iGEM Uppsala year '.$currentyear.'!'
							.'You can now log in to your UpStrain account.'
							.'Welcome!';

		mail( $toemail, $subject, $message_body );
    }
}
else {
    $_SESSION['message'] = "The verification did not succeed! Try again. ";
    header("location: error.php");
}     
?>