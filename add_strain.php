

<?php

if (count(get_included_files()) == 1) exit("Access resricted");

include 'scripts/db.php';

$strain = mysqli_real_escape_string($link, $_REQUEST['strain']);
$comment = mysqli_real_escape_string($link, $_REQUEST['comment']);
$current_date = date("Y-m-d");
$creator = $_SESSION['user_id'];


// Insert new strain if not existing
$check = "SELECT name FROM strain WHERE name LIKE '$strain'";
$check_query = mysqli_query($link, $check);
if (mysqli_num_rows($check_query) < 1) {
    $sql_strain = "INSERT INTO strain (name,comment,creator,date_db) VALUES (?,?,?,?)";
    $stmt_strain = $link->prepare($sql_strain);
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
            $stmt_strain->bind_param("ssis", $strain, $comment, $creator, $current_date);
            $stmt_strain->execute();
            $stmt_strain->close();
        } else {
            echo "This should never happen";
        }
    }
}