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
    $current_date = date("Y-m-d");
    $type = mysqli_real_escape_string($link, test_input($_POST['new_insert_type']));
    $name = mysqli_real_escape_string($link, test_input($_POST['new_insert']));
    $regid = mysqli_real_escape_string($link, test_input($_POST['Ins_registry']));
    $comment = mysqli_real_escape_string($link, test_input($_POST['comment']));
    $creator = $_SESSION['user_id'];
    $private = 0;

    if (isset($_POST['private'])) {
        $private = intval(test_input($_POST['private']));
    }

// Insert new insert if not existing
    $check = "SELECT name FROM ins WHERE name LIKE '$name'";
    $check_query = mysqli_query($link, $check);
    if (mysqli_num_rows($check_query) < 1) {
        $sql_ins = "INSERT INTO ins (name,type,ins_reg,creator,date_db,comment,private) VALUES (?,?,?,?,?,?,?)";
        if ($stmt_ins = $link->prepare($sql_ins)) {
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                if ($stmt_ins->bind_param("sisissi", $name, $type, $regid, $creator, $current_date, $comment, $private)) {
                    if ($stmt_ins->execute()) {
                        $_SESSION['success'] = "<div class = 'success'>New insert submitted successfully</div>";
                        mysqli_close($link) or die("Could not close database connection");
                        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_insert");
                        exit();
                    } else {
                        $_SESSION['error'] = "<div class = 'error'>Execute failed: (" . $stmt_ins->errno . ")" . " " . "Error: " . $stmt_ins->error . "</div>";
                        mysqli_close($link) or die("Could not close database connection");
                        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_insert");
                        exit();
                    } $stmt_ins->close();
                } else {
                    $_SESSION['error'] = "<div class = 'error'>Binding parameters failed: (" . $stmt_ins->errno . ")" . " " . "Error: " . $stmt_ins->error . "</div>";
                    mysqli_close($link) or die("Could not close database connection");
                    header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_insert");
                    exit();
                }
            }
        } else {
            $_SESSION['error'] = "<div class = 'error'>Prepare failed: (" . $link->errno . ")" . " " . "Error: " . $link->error . "</div>";
            mysqli_close($link) or die("Could not close database connection");
            header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_insert");
            exit();
        }
    } else {
        $_SESSION['existing'] = "<div class = 'existing'>The entered insert already exists! Please enter a new one. </div>";
        mysqli_close($link) or die("Could not close database connection");
        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?content=new_insert");
        exit();
    }
}
?>