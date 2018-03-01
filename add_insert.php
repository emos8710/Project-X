<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

include 'scripts/db.php';

$current_date = date("Y-m-d");
$creator = $_POST['user_id'];
$type = mysqli_real_escape_string($link,$_REQUEST['insert_type[]']);
$name = mysqli_real_escape_string($link,$_REQUEST['ins[]']); 
$regid = mysqli_real_escape_string($link,$_REQUEST['Ins_registry']);
$comment = mysqli_real_escape_string($link, $_REQUEST['comment']);


// Insert new insert if not existing
    $check = "SELECT name FROM ins WHERE name LIKE '$name'";
    $check_query = mysqli_query($link, $check);
    if (mysqli_num_rows($check_query) < 1) {
        $sql_ins = "INSERT INTO ins (name,type,ins_reg,creator,date_db,comment) VALUES (?,?,?,?,?,?)";
        $stmt_ins = $link->prepare($sql_strain);
        $stmt_ins->bind_param('$name','$type', '$regid','$creator','$current_date', '$comment');
        $stmt_ins->execute();
        $stmt_ins->close();
    } 
}
?>