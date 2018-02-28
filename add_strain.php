<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

include 'scripts/db.php';

$strain = mysqli_real_escape_string($link,$_REQUEST['strain']);
$current_date = date("Y-m-d");
$creator = 1; 
//$creator = $_POST['user_id']; 
$comment = mysqli_real_escape_string($link, $_REQUEST['comment']);


// Insert new strain if not existing
    $check = "SELECT name FROM strain WHERE name LIKE '$strain'";
    $check_query = mysqli_query($link, $check);
    if (mysqli_num_rows($check_query) < 1) {
        $sql_strain = "INSERT INTO strain (name,comment,creator,date_db) VALUES (?,?,?,?)";
        $stmt_strain = $link->prepare($sql_strain);
        $stmt_strain->bind_param("ssis", $strain, $comment, $creator, $current_date);
        $stmt_strain->execute();
        $stmt_strain->close();
    } 
}