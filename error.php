<?php
/* Displays all error messages */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$title = "Error";
?>
<!DOCTYPE html>

<?php include 'top.php'; ?>

<body>
    <main>
        <div class="form">
            <h1 class="login">Error</h1>
            <p class="login">
                <?php
                if (isset($_SESSION['message']) AND ! empty($_SESSION['message'])) {
                    ?>
                    <strong><?php echo $_SESSION['message']; ?></strong>
                    <br>
                    <a href="logsyst.php">Back to login/registration</a>
                    <?php
                } else {
                    header("location: logsyst.php");
                }
                ?>
            </p>     
        </div>
    </main>
    <?php include 'bottom.php'; ?>
</body>
</html>
