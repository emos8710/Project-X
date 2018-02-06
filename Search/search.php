<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// connect to database    
include 'db.php';

    if(isset(mysql_real_escape_string($link, $_REQUEST['submit']))) {
    
        $id_criteria = mysqli_real_escape_string($link, $_REQUEST['id_criteria']);
        $strain_criteria = mysqli_real_escape_string($link, $_REQUEST['strain_criteria']);
        $backbone_criteria = mysqli_real_escape_string($link, $_REQUEST['backbone_criteria']);
        $insert_criteria = mysqli_real_escape_string($link, $_REQUEST['insert_criteria']);
        $bb_id_criteria = mysqli_real_escape_string($link, $_REQUEST['bb_id_criteria']);
        $comment_criteria = mysqli_real_escape_string($link, $_REQUEST['comment_criteria']);
        $creation_year_criteria = mysqli_real_escape_string($link, $_REQUEST['creation_year_criteria']);
        $inserted_date_criteria = mysqli_real_escape_string($link, $_REQUEST['inserted_date_criteria']);
    
        // query the database table
        $sql="SELECT * FROM entry WHERE (id like '%".$id_criteria."%') OR (backbone like '%".$backbone_criteria."%') OR 
        (strain like '%".$strain_criteria."%') OR (ins like '%".$insert_criteria."%') OR 
        (entry_reg like '%".$bb_id_criteria."%') OR (comment like '%".$comment_criteria."%') OR
        (year_created like '%".$creation_year_criteria."%') OR
        (date_db like '%".$inserted_date_criteria."%')";
            
        $result=mysql_query($sql);

        // create a while loop and loop through result set
        while ($row=mysql_fetch_array($result)) {
            $name=$row['name'];
            $ID=$row['ID'];
        // display the result of the array
            echo "<ul>\n";
            echo "<li>" . "<a href=\"search.php?id=$ID\">" . $name . "</a></li>\n";
            "</ul>";
            }
        }
    else {
    echo "<p>Please enter a search query</p>";
    }
if (mysqli_query($link, $sql)) {
        echo "<p>Search function failed</p>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($link);
    }
    mysqli_close($link) or die('Could not close connection to database');

