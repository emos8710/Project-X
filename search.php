<!DOCTYPE html>
<!--

-->
<html>
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Search for entry</title>
	<link href="upstrain.css" rel="stylesheet">
    </head>
    <body>
        
	<?php include 'top.php'; ?>
        
	<main>
		<div class="innertube">
			<h2>Search for entry </h2>
                      
        
        <form action="search.php" method="post" id="searchform">
            
             <?php
             session_start(); 
             ?>

            <p>
            <label>Upstrain ID: 
                <input type="text" name="id_criteria"/></label>
            </p>
            
            <p>
            <label>Strain: 
                <input type="text" name="strain_criteria"/></label>
            </p>
            
            <p>
            <label>Backbone: 
                <input type="text" name="backbone_criteria"/></label>
            </p>
            
            <p>    
            <label>Insert: 
                <input type="text" name="insert_criteria"/></label>
            </p>
            
            <p>
               <select name="insert_type">
                <option value="promotor">Promotor</option>
                <option value="coding_seq">Coding sequence</option>
                <option value="RBS">RBS</option>
                <option value="other">Other</option>
            </select></p>
            
            <p>    
            <label>Biobrick registry ID: 
                <input type="text" name="bb_id_criteria"/></label>
            </p>
            
            <p>    
            <label>Comment: 
                <input type="text" name="comment_criteria" ows ="4" cols="50"/></label>
            </p>
            
            <p>    
            <label>Year created: 
                <input type="text" name="creation_year_criteria" minlength= "4" maxlengh= "4" pattern = "(?:19|20)[0-9]{2})" 
                placeholder="YYYY" /></label>
            </p>
            
            <p>    
            <label>Date inserted: 
                <input type="text" name="inserted_date_criteria"/></label>
            </p>
            
            <p>    
            <label>Creator: 
                <input type="text" name="creator_criteria"/></label>
            </p>
            
            
            <input name="submit-form" value="Search" type="submit" class="btn btn-lg btn-success">
            
            </form>
                </div>
    
            
    <?php
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
        $creator_criteria = mysqli_real_escape_string($link, $_REQUEST['creator_criteria']);
    
        
        $ConditionArray = [];
        
        if(!empty($id_criteria)) {
            if (!preg_match('/[^A-Za-z0-9]/', $id_criteria)) { 
                $ConditionArray[] = "t6.upstrain_id = '$id_criteria'";
            } else {
                echo nl2br ("\n \n Error: Non-valid character usage for 'ID'.");    
            }
        }  
        
        if(!empty($strain_criteria)) {
            $ConditionArray[] = "t4.name = '$strain_criteria'";
        }   
        
        if(!empty($backbone_criteria)) {
            if (!preg_match('/[^A-Za-z0-9]/', $backbone_criteria)) { 
                $ConditionArray[] = "t3.name = '$backbone_criteria'";
            } else {
                echo nl2br ("\n \n Error: Non-valid character usage for 'Backbone'.");
            }
        }    
        
        if(!empty($insert_criteria)) {
            $ConditionArray[] = "t5.name = '$insert_criteria'";
        }   
        
        if(!empty($bb_id_criteria_criteria)) {
            if (!preg_match('/[^A-Za-z0-9]/', $bb_id_criteria)) {
                $ConditionArray[] = "t1.entry_reg = '$bb_id_criteria'";
            } else {
                echo nl2br ("\n \n Error: Non-valid character usage for 'Biobrick registry ID'.");
            }
        }  
        
        if(!empty($comment_criteria)) {
            $ConditionArray[] = "t1.comment = '$comment_criteria'";
        }   
        
        if(!empty($creation_year_criteria)) {
            if (is_numeric($creation_year_criteria)) {
                $ConditionArray[] = "t1.year_created = $creation_year_criteria";
            } else {
                echo nl2br ("\n \n Error: Non-valid character usage for 'Year created'.");                
            }
        } 
        
        if(!empty($inserted_date_criteria)) {
            if (is_numeric($inserted_date_criteria)) {
                $ConditionArray[] = "t1.date_db = $inserted_date_criteria";
            } else {
                echo nl2br ("\n \n Error: Non-valid character usage for 'Date inserted'.");
            }
        } 
        
        if(!empty($creator_criteria)) {
                $ConditionArray[] = "(t2.first_name = '$creator_criteria' OR t2.last_name = '$creator_criteria')";
        }        
        
        $entrysql = "SELECT DISTINCT t1.comment AS cmt, t1.year_created AS year, "
	."t1.date_db AS date, t1.entry_reg AS biobrick, t4.name AS strain, "
	."t5.insert_id AS ins, t3.name AS backbone, t2.user_id AS user_id, "
	."t2.first_name AS fname, t2.last_name AS lname, t10.upstrain_id AS up_id "
        ."FROM ((ins_type AS t8), (entry AS t1)) "        
        ."INNER JOIN users AS t2 ON t1.creator = t2.user_id " 
        ."INNER JOIN backbone AS t3 ON t1.backbone = t3.id "
        ."INNER JOIN strain AS t4 ON t1.strain = t4.id "
        ."INNER JOIN entry_inserts AS t5 ON t1.id = t5.entry_id "
        ."INNER JOIN ins AS t6 ON t8.id = t6.type "        
        ."INNER JOIN entry_inserts AS t9 ON t6.id = t9.insert_id "              
        ."INNER JOIN entry_upstrain AS t10 ON t1.id = t10.entry_id "        
        ."WHERE ";     
                
        
        if (count($ConditionArray) > 0) {
            $sql = $entrysql . implode(' AND ', $ConditionArray);
            
        } else {
            echo nl2br ("\n Error: Please enter search query");
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
	 
		
if($iserror && !empty($ConditionArray)) {
    echo "<h3>Error: ".$error."</h3>";
    echo "<br>".
    "<a href=\"javascript:history.go(-1)\">Go back</a>";
    
} else {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>upstrain ID</th><th>Strain</th><th>Backbone</th>"
    . "<th>Insert</th><th>Year</th><th>iGEM registry entry</th>"
    . "<th>Creator</th><th>Added date</th><th>Comment</th></tr>";
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["up_id"]. "</td><td>" . $row["strain"]. 
                "</td><td>" . $row["backbone"]. "</td><td>" . $row["ins"].
                "</td><td>" . $row["year"]. "</td><td>" . $row["biobrick"]. 
                "</td><td>" . "<a href=\"user.php?user_id=".$row["user_id"]."\">". 
                $row["fname"]. " " . $row["lname"]. "</td><td>" . $row["date"].
                "</td><td>" . $row["cmt"]. "</td></tr>";
    }
    echo "</table>";
    echo $entrydata['cmt'];				
}
?>
            
            
   </main>
        <?php include 'bottom.php'; ?>
    </body>
</html>

