<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (count(get_included_files()) == 1)
    exit("Access restricted");

/* Registration process */

/* reCAPTCHA */
$response = $_POST["g-recaptcha-response"];
$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = array(
    'secret' => '6LfhRkoUAAAAAP_0r8kbO31Q-7VA6ftxRtKiVn6I',
    'response' => $_POST["g-recaptcha-response"]
);

$query = http_build_query($data);

$options = array(
    'http' => array(
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        "Content-Length: " . strlen($query) . "\r\n" .
        "User-Agent:MyAgent/1.0\r\n",
        'method' => 'POST',
        'content' => $query
    )
);

$context = stream_context_create($options);
$verify = file_get_contents($url, false, $context);
$captcha_success = json_decode($verify);

// If the reCAPTCHA failed, throw an error
if ($captcha_success->success == false) {
    $_SESSION['message'] = 'reCAPTCHA failed. Are you a bot?';

    header("location: error.php"); // An error message is sent to error.php
} else {
    // Checks if the password is an ok length
    if (!(strlen($_POST['password']) < 8)) {
        // Checks if the two passwords match 
        if ($_POST['confpassword'] == $_POST['password']) {
            
            // Sets the session variables which will be shown on profile before verification
            $_SESSION['email'] = $_POST['email'];
            $_SESSION['first_name'] = $_POST['firstname'];
            $_SESSION['last_name'] = $_POST['lastname'];

            // Protection against SQL injection - all unneccessary variables are removed
            $first_name = $mysqli->escape_string($_POST['firstname']);
            $last_name = $mysqli->escape_string($_POST['lastname']);
            $email = $mysqli->escape_string($_POST['email']);
            $phone = $mysqli->escape_string($_POST['phone']);
            $username = $mysqli->escape_string($_POST['username']);
            $password = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT)); // Max 72 characters
            $hash = $mysqli->escape_string(md5(rand(0, 1000)));

            // Checks if the email is already registered
            $result = $mysqli->query("SELECT * FROM users WHERE email='$email'") or die($mysqli->error());

            // If the query returns more than one row, the email already exists
            if ($result->num_rows != 0) {
                $_SESSION['message'] = 'The email is already registered to another user. 
									Have you forgotten the password? - Click on Reset Password.';
                header("location: error.php"); // An error message is sent to error.php
            }
            // The email is not registered
            else {
                // Checks if characters have been removed from the username
                if ($username != $_POST['username']) {
                    $_SESSION['message'] = 'The username contains invalid characters! 
											Choose another username!';
                    header("location: error.php");
                }
                // Otherwise, the username is OK and the registration process proceeds. 
                else {
                    // Checks if the username is free or taken by another user
                    $result = $mysqli->query("SELECT * FROM users WHERE username='$username'") or die($mysqli->error());

                    // If the query returns more than one row, the username is taken.
                    if ($result->num_rows != 0) {
                        $_SESSION['message'] = 'The username is taken by another user. Please choose another username.';
                        header("location: error.php"); // An error message is sent
                    }
                    // If the query returns zero rows, the username is free
                    else {
                        // The registering user is inserted into the database
                        $sql = "INSERT INTO users (first_name, last_name, email, phone, username, password, hash, time) VALUES ('$first_name','$last_name','$email','$phone','$username','$password','$hash', UNIX_TIMESTAMP(NOW()))";

                        // If the insertion succeeds the user is set to logged in, but not active (0)
                        if ($mysqli->query($sql)) {
                            $id_query = $mysqli->query("SELECT user_id FROM users WHERE username='$username'") or die($mysqli->error());
                            $_SESSION['user_id'] = $user_id = $id_query->fetch_assoc()['user_id'];
                            $_SESSION['logged_in'] = true; // So we know the user has logged in
                            $_SESSION['active'] = 0; //0 until user activates their account with verify.php
                            $_SESSION['message'] = "A confirmation link has been sent to the administrator."
                                    . " Your account will be activated when the administrator has confirmed that you are a 
													member of the iGEM team this year."
                                    . " If the account is not activated within 72 hours, your UpStrain account will be removed.";

                            // Send registration confirmation link (verify.php)
                            $to = $email;
                            $subject = 'Account Verification ( UpStrain )';
                            $message_body = 'Hi, thanks for registering!'
                                    . ' Please click this link to activate ' . $first_name . 's UpStrain account:'
                                    . ' http://localhost/verify.php?email=' . $email . '&hash=' . $hash;

                            mail($to, $subject, $message_body);
                            header("location: profile.php");
                        }
                        // If the insertion did not succeed, the registration has failed and an error message is shown
                        else {
                            $_SESSION['message'] = 'The registration failed!';
                            header("location: error.php");
                        }
                    }
                }
            }
        } else {
            $_SESSION['message'] = "The passwords do not match! Try again.";
            header("location: error.php");
        }
    } else {
        $_SESSION['message'] = "The entered password is too short. Please try again. The password must be at least 8 characters long!";
        header("location: error.php");
    }
}