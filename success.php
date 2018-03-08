<?php
/* Displays all successful messages */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$title = "Success";

include 'top.php';
?>
<main>
    <div class="form">
        <h1 class="login"><?= 'Success'; ?></h1>
        <p class="login">
            <?php
            if (isset($_SESSION['message']) AND ! empty($_SESSION['message'])) {
                echo $_SESSION['message'];
            } else {
                header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "logsyst.php");
            }
            ?>
        </p>
    </div>
</main>
<?php include 'bottom.php'; ?>
