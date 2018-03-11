<?php

if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    include 'scripts/db.php';

    function test_input($string) {
        return htmlspecialchars(strip_tags(stripslashes(trim($string))));
    }

//Variables
    $backbone = mysqli_real_escape_string($link, test_input($_POST['backbone']));
    $comment = mysqli_real_escape_string($link, test_input($_POST['comment']));
    $reg_id = mysqli_real_escape_string($link, test_input($_POST['Bb_registry']));
    $current_date = date("Y-m-d");
    $creator = $_SESSION['user_id'];
    $private = 0;

    if (isset($_POST['private'])) {
        $private = intval(test_input($_POST['private']));
    }

// Insert new backbone if not existing
    $check = "SELECT name FROM backbone WHERE name LIKE '$backbone'";
    $check_query = mysqli_query($link, $check);
    if (mysqli_num_rows($check_query) < 1) {
        $sql_backbone = "INSERT INTO backbone (name, Bb_reg, date_db,creator, comment, private) VALUES (?,?,?,?,?,?)";
        if ($stmt_backbone = $link->prepare($sql_backbone)) {
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                if ($stmt_backbone->bind_param("sssisi", $backbone, $reg_id, $current_date, $creator, $comment, $private)) {
                    if ($stmt_backbone->execute()) {
                        $_SESSION['success'] = "<div class = 'success'>New backbone submitted successfully</div>";
                        mysqli_close($link) or die("Could not close database connection");
                        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?success");
                        exit();
                    } else {
                        $_SESSION['error'] = "<div class = 'error'>Execute failed: (" . $stmt_backbone->errno . ")" . " " . "Error: " . $stmt_backbone->error . "</div>";
                        mysqli_close($link) or die("Could not close database connection");
                        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?error");
                        exit();
                    } $stmt_backbone->close();
                } else {
                    $_SESSION['error'] = "<div class = 'error'>Binding parameters failed: (" . $stmt_backbone->errno . ")" . " " . "Error: " . $stmt_backbone->error . "</div>";
                    mysqli_close($link) or die("Could not close database connection");
                    header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?error");
                    exit();
                }
            }
        } else {
            $_SESSION['error'] = "<div class = 'error'>Prepare failed: (" . $link->errno . ")" . " " . "Error: " . $link->error . "</div>";
            mysqli_close($link) or die("Could not close database connection");
            header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?error");
            exit();
        }
    } else {
        $_SESSION['existing'] = "<div class = 'existing'>The entered backbone already exists! Please enter a new one </div>";
        mysqli_close($link) or die("Could not close database connection");
        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?existing");
        exit();
    }
}