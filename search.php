<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Search</title>
	<link href="upstrain.css" rel="stylesheet">
    </head>
    <body>
        
	<?php include 'top.php'; ?>
        
        	<!-- Main content goes here -->
	<main>
		<div class="innertube">
			<h2>Search </h2>
        
        <form action="search.php" method="post" id="searchform">

            <p>
            <label>upstrain ID: 
                <input type="text" name="id_criteria" /></label>
            </p>
            
            <p>
            <label>Strain: 
                <input type="text" name="strain_criteria" /></label>
            </p>
            
            <p>
            <label>Backbone: 
                <input type="text" name="backbone_criteria" /></label>
            </p>
            
            <p>    
            <label>Insert: 
                <input type="text" name="insert_criteria" /></label>
            </p>
            
            <p>    
            <label>Biobrick registry ID: 
                <input type="text" name="bb_id_criteria" /></label>
            </p>
            
            <p>    
            <label>Comment: 
                <input type="text" name="comment_criteria" /></label>
            </p>
            
            <p>    
            <label>Year created: 
                <input type="text" name="creation_year_criteria" /></label>
            </p>
            
            <p>    
            <label>Date inserted: 
                <input type="text" name="inserted_date_criteria" /></label>
            </p>
            
            <!--<input type="submit" name="submit" value="Search">-->
            <input name="submit-form" value="search" type="submit" class="btn btn-lg btn-success">
        
            
    </body>
</html>


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
    
        
        $ConditionArray = [];
        
        if(!empty($id_criteria)) {
            if (!preg_match('/[^A-Za-z0-9]/', $id_criteria)) { 
                $ConditionArray[] = "id like '$id_criteria'";
            } else {
                echo nl2br ("\n \n Error: Non-valid character usage for 'ID'.");    
            }
        }  
        
        if(!empty($strain_criteria)) {
            $ConditionArray[] = "strain like '$strain_criteria'";
        }   
        
        if(!empty($backbone_criteria)) {
            if (!preg_match('/[^A-Za-z0-9]/', $backbone_criteria)) { 
                $ConditionArray[] = "backbone like '$backbone_criteria'";
            } else {
                echo nl2br ("\n \n Error: Non-valid character usage for 'Backbone'.");
            }
        }    
        
        if(!empty($insert_criteria)) {
            $ConditionArray[] = "ins like $insert_criteria";
        }   
        
        if(!empty($bb_id_criteria_criteria)) {
            if (!preg_match('/[^A-Za-z0-9]/', $bb_id_criteria)) {
                $ConditionArray[] = "entry_reg like '$bb_id_criteria'";
            } else {
                echo nl2br ("\n \n Error: Non-valid character usage for 'Biobrick registry ID'.");
            }
        }  
        
        if(!empty($comment_criteria)) {
            $ConditionArray[] = "comment like '$comment_criteria'";
        }   
        
        if(!empty($creation_year_criteria)) {
            if (is_numeric($creation_year_criteria)) {
                $ConditionArray[] = "year_created like '$creation_year_criteria'";
            } else {
                echo nl2br ("\n \n Error: Non-valid character usage for 'Year created'.");                
            }
        } 
        
        if(!empty($inserted_date_criteria)) {
            if (is_numeric($inserted_date_criteria)) {
                $ConditionArray[] = "date_db like '$inserted_date_criteria'";
            } else {
                echo nl2br ("\n \n Error: Non-valid character usage for 'Date inserted'.");
            }
        } 
        
        if (count($ConditionArray) > 0) {
            $sql = "SELECT * FROM entry WHERE ".implode(' AND ', $ConditionArray);
            
        } else {
            echo nl2br ("\n \n Please enter search query");
        }

        $result=mysqli_query($link, $sql);  

        
        $iserror = FALSE;
        if (mysqli_num_rows($result) < 1) {
            $iserror = TRUE;
            $error = "No matching results, try another search.";
        }
      }
        
    else if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('HTTP/1.0 405 Method Not Allowed');
    exit;
    }
	 
		
if($iserror) {
    echo "<h3>Error: ".$error."</h3>";
    echo "<br>".
    "<a href=\"javascript:history.go(-1)\">Go back</a>";
} else {
    while($row = mysqli_fetch_array($result)) {
        echo $row['id'];
        }
    include 'db.php';
    mysqli_close($link) or die("Could not close database connection");
				
}
include 'bottom.php';


?>

