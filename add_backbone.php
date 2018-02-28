<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

include 'scripts/db.php';

    $iserror = FALSE;
    
//Variables
    $backbone = mysqli_real_escape_string($link, $_REQUEST['backbone']);
    $comment = mysqli_real_escape_string($link, $_REQUEST['comment']);
    $reg_id = mysqli_real_escape_string($link, $_REQUEST['registry']);
    $current_date = date("Y-m-d");
    $creator = 1;
//$creator = $_POST['user_id'];

// Insert new backbone if not existing
    $check2 = "SELECT name FROM backbone WHERE name LIKE '$backbone'";
    $check_query2 = mysqli_query($link, $check2);
    if (mysqli_num_rows($check_query2) < 1) {
        $sql_backbone = "INSERT INTO backbone (name, date_db,creator, comment) VALUES (?,?,?,?)";
        $stmt_backbone = $link->prepare($sql_backbone);
        $stmt_backbone->bind_param("ssis", $backbone, $current_date, $creator, $comment);
        $stmt_backbone->execute();
        $stmt_backbone->close();
    }
}