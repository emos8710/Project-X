<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/* The login process */

// Protection against SQL-injections 
$ip_address = mysqli_real_escape_string($link, $_SERVER['REMOTE_ADDR']);
$username = mysqli_real_escape_string($link, $_POST['username']);      // Extra characters are removed
// Check if user has failed login too many times recently
$check_attempts = mysqli_query($link, "SELECT attempts, time from attempt_log WHERE ip = '$ip_address' AND username = '$username'");

if (mysqli_num_rows($check_attempts) < 1) {
    mysqli_query($link, "INSERT INTO attempt_log(ip, username, time) VALUES ('$ip_address', '$username', 0)");
    $check_attempts = mysqli_query($link, "SELECT attempts, time from attempt_log WHERE ip = '$ip_address' AND username = '$username'");
}

$result = mysqli_fetch_assoc($check_attempts);
$n_attempts = $result['attempts'];
$timeout_start = $result['time'];
unset($result);
if ($n_attempts >= 3 && time() - $timeout_start < 300) {
    $_SESSION['message'] = "You are blocked due to too many login attempts. Please try again in 5 minutes."
            . "<br>"
            . "Note: 5 minute timer will reset if you attempt to login again.";
    header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "error.php"); // The error message is sent to error.php
} else {
    if (time() - $timeout_start >= 300) {
        mysqli_query($link, "UPDATE attempt_log SET attempts = 0 WHERE ip = '$ip_address' AND username = '$username'");
    }

    $result = $mysqli->query("SELECT * FROM users WHERE username='$username'"); // $result becomes the row resulting from the query
    // If $result contains no rows then the user does not exist
    if ($result->num_rows == 0) {
        $_SESSION['message'] = "The user does not exist! Try again or register.";
        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "error.php"); // The error message is sent to error.php
    }
    // Otherwise, the username exists
    else {
        $user = mysqli_fetch_assoc($result);
        // Check if password is correct
        if (password_verify($_POST['password'], $user['password'])) {
            mysqli_query($link, "UPDATE attempt_log SET attempts = 0 WHERE ip = '$ip_address' AND username = '$username'"); // Reset login attempts
            // Update the session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['phone'] = $user['phone'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['active'] = $user['active'];
            $_SESSION['admin'] = $user['admin'];

            // Keep reminding the user this account is not active, until they activate
            if ($_SESSION['active'] == false) {
                $_SESSION['message'] = "Your account is inactive. You will able to log in when the administrator has verified your account.";
                $_SESSION['logged_in'] = false;
                header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "error.php");
            } else if ($_SESSION['active'] == true) {
                // The session is set to logged in
                $_SESSION['logged_in'] = true;
                header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "index.php");
            }
            // The user is sent to the profile page
        }
        // If the passwords do not match, the failed login is recorded and error is sent to error.php
        else {
            mysqli_query($link, "UPDATE attempt_log SET attempts = attempts + 1, time = UNIX_TIMESTAMP(NOW()) WHERE ip = '$ip_address' AND username = '$username'");

            $remaining = 3 - (mysqli_fetch_array(mysqli_query($link, "SELECT attempts from attempt_log WHERE ip = '$ip_address' AND username = '$username'"))[0]);

            $_SESSION['message'] = "The password you entered is incorrect! Try again or reset your password."
                    . "<br>"
                    . "Remaining attempts: $remaining";
            header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "error.php");
        }
    }
}