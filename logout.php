<?php
/* Log out process, unsets and destroys session variables */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
session_unset();
session_destroy();

$title = "Logged out";

include 'top.php';
?>

<main>
    <div class="form">
        <h1 class="login">You have been logged out!</h1>
    </div>
</main>
<?php include 'bottom.php'; ?>
