
<?php

if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    include 'scripts/db.php';
//Variables
    $strain = mysqli_real_escape_string($link, $_POST['strain']);
    $comment = mysqli_real_escape_string($link, $_POST['comment']);
    $current_date = date("Y-m-d");
    $creator = $_SESSION['user_id'];


// Insert new strain if not existing
    $check = "SELECT name FROM strain WHERE name LIKE '$strain'";
    $check_query = mysqli_query($link, $check);
    if (mysqli_num_rows($check_query) < 1) {
        $sql_strain = "INSERT INTO strain (name,comment,creator,date_db) VALUES (?,?,?,?)";
        if ($stmt_strain = $link->prepare($sql_strain)) {
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                if ($stmt_strain->bind_param("ssis", $strain, $comment, $creator, $current_date)) {
                    if ($stmt_strain->execute()) {
                        $_SESSION['success'] = "<div class = 'success'>New strain submitted successfully</div>";
                        mysqli_close($link) or die("Could not close database connection");
                        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?success");
                        exit(); 
                    } else {
                        $_SESSION['error'] = "<div class = 'error'>Execute failed: (" . $stmt_strain->errno . ")" . " " . "Error: " . $stmt_strain->error . "</div>";
                        mysqli_close($link) or die("Could not close database connection");
                        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?error");
                        exit(); 
                    } $stmt_strain->close();
                } else {
                    $_SESSION['error'] = "<div class = 'error'>Binding parameters failed: (" . $stmt_strain->errno . ")" . " " . "Error: " . $stmt_strain->error . "</div>";
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
        $SESSION['existing'] = "<div class = 'existing'>The entered strain already exists! Please enter a new one</div>";
        mysqli_close($link) or die("Could not close database connection");
        header("Location: http://" . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . "/" . "new_insert.php?existing");
        exit(); 
    }
}
