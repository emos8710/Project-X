<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// connect to database    
include 'db.php';

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        $id_criteria = mysqli_real_escape_string($link, $_REQUEST['id_criteria']);
        $strain_criteria = mysqli_real_escape_string($link, $_REQUEST['strain_criteria']);
        $backbone_criteria = mysqli_real_escape_string($link, $_REQUEST['backbone_criteria']);
        $insert_criteria = mysqli_real_escape_string($link, $_REQUEST['insert_criteria']);
        $bb_id_criteria = mysqli_real_escape_string($link, $_REQUEST['bb_id_criteria']);
        $comment_criteria = mysqli_real_escape_string($link, $_REQUEST['comment_criteria']);
        $creation_year_criteria = mysqli_real_escape_string($link, $_REQUEST['creation_year_criteria']);
        $inserted_date_criteria = mysqli_real_escape_string($link, $_REQUEST['inserted_date_criteria']);
    
        // query the database table
        $sql ="SELECT * FROM entry WHERE (id like '%".$id_criteria."%') OR (backbone like '%".$backbone_criteria."%') OR 
        (strain like '%".$strain_criteria."%') OR (ins like '%".$insert_criteria."%') OR 
        (entry_reg like '%".$bb_id_criteria."%') OR (comment like '%".$comment_criteria."%') OR
        (year_created like '%".$creation_year_criteria."%') OR
        (date_db like '%".$inserted_date_criteria."%')";
            
        $result=mysqli_query($link, $sql);
        
        if(mysqli_num_rows($result) > 0) {
            // create a while loop and loop through result set
            while ($row=mysqli_fetch_array($result, MYSQL_ASSOC)) {
                $id = $row['id'];
                echo $id;
            // display the result of the array
                echo "<ul>\n";
                echo "<li>" . "<a href=\"search.php?id=$id\">" . $id . "</a></li>\n";
                "</ul>";
                }
        }
        else {
            echo "<p>No matching results, try another search.</p>";
        }
        }
    else if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('HTTP/1.0 405 Method Not Allowed');
    exit;
}

