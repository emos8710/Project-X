<?php

if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    include 'scripts/db.php';

//Variables
    $backbone = mysqli_real_escape_string($link, $_REQUEST['backbone']);
    $comment = mysqli_real_escape_string($link, $_REQUEST['comment']);
    $reg_id = mysqli_real_escape_string($link, $_REQUEST['Bb_registry']);
    $current_date = date("Y-m-d");
    $creator = $_SESSION['user_id'];
    $private = 0;

    if (isset($_POST['private'])) {
        $private = intval($_POST['private']);
    }

/// Insert new backbone if not existing
    $check = "SELECT name FROM backbone WHERE name LIKE '$backbone'";
    $check_query = mysqli_query($link, $check);
    if (mysqli_num_rows($check_query) < 1) {
        $sql_backbone = "INSERT INTO backbone (name, Bb_reg, date_db,creator, comment, private) VALUES (?,?,?,?,?,?)";
        if ($stmt_backbone = $link->prepare($sql_backbone)) {
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                if ($stmt_backbone->bind_param("sssisi", $backbone, $reg_id, $current_date, $creator, $comment, $private)) {
                    if ($stmt_backbone->execute()) {
                        $_SESSION['success'] = "<div class = 'success'>New backbone submitted successfully</div>";
                        header("Location: new_insert.php?success");
                    } else {
                        $_SESSION['error'] = "<div class = 'error'>Execute failed: (" . $stmt_backbone->errno . ")" . " " . "Error: " . $stmt_backbone->error . "</div>";
                        header("Location: new_insert.php?error");
                    } $stmt_backbone->close();
                } else {
                    $_SESSION['error'] = "<div class = 'error'>Binding parameters failed: (" . $stmt_backbone->errno . ")" . " " . "Error: " . $stmt_backbone->error . "</div>";
                    header("Location: new_insert.php?error");
                }
            }
        } else {
            $_SESSION['error'] = "<div class = 'error'>Prepare failed: (" . $link->errno . ")" . " " . "Error: " . $link->error . "</div>";
            header("Location: new_insert.php?error");
        }
    }
}