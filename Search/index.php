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
	<title>UpStrain</title>
	<link href="upstrain.css" rel="stylesheet">
    </head>
    <body>
        
	<?php include 'top.php'; ?>
        
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
            <input name="submit-form" value="Search" type="submit" class="btn btn-lg btn-success">
        
        <?php include 'bottom.php'; ?>
            
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
    
        // query the database table
        $sql ="SELECT * FROM entry WHERE (id like '%".$id_criteria."%') OR (backbone like '%".$backbone_criteria."%') OR 
        (strain like '%".$strain_criteria."%') OR (ins like '%".$insert_criteria."%') OR 
        (entry_reg like '%".$bb_id_criteria."%') OR (comment like '%".$comment_criteria."%') OR
        (year_created like '%".$creation_year_criteria."%') OR
        (date_db like '%".$inserted_date_criteria."%')";
            
        $result=mysqli_query($link, $sql);
        
        $iserror = FALSE;
        if (mysqli_num_rows($result) < 1) {
            $iserror = TRUE;
            $error = "No such entry";
            echo "<p>No matching results, try another search.</p>";
        }
        elseif(!$result = mysqli_query($link, $sql)) {
            $iserror = TRUE;
            $error = mysqli_error();
        }
        
    else if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('HTTP/1.0 405 Method Not Allowed');
    exit;
    }
}
	
mysqli_close($link) or die("Could not close database connection");
	
if($iserror) {
	$title = "Error: ".$error;
    } 
else {

     while ($row=mysqli_fetch_array($result, MYSQL_ASSOC)) {
     $id = $row['id'];
     $table = "";
        }  
    }

			
if($iserror) {
    echo "<h3>Error: ".$error."</h3>";
    echo "<br>".
    "<a href=\"javascript:history.go(-1)\">Go back</a>";
} else {
    echo $id;
    include 'db.php';
    mysqli_close($link) or die("Could not close database connection");
				
}
			
?>

