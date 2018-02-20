<!DOCTYPE html>

<?php
	if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
		session_start();
	}
?>

<!--

-->
<html>
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Search for entry</title>
	<link href="css/upstrain.css" rel="stylesheet">
    </head>
    <body>
        
	<?php include 'top.php'; ?>
        
	<main>
		<div class="innertube">
			<h2>Search for entry </h2>
                      
        
        <form action="search.php" method="post" id="searchform">

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
            <label>Insert Type:
               <select name="insert_type">
               <option value=""></option>    
               <option value="promotor">Promotor</option>
               <option value="coding">Coding</option>
            </select>
            </label>       
            </p>
            
            <p>    
            <label>Biobrick registry ID: 
                <input type="text" name="bb_id_criteria" placeholder="BBa_K----"/></label>
            </p>
            
            <p>    
            <label>Comment: 
                <input type="text" name="comment_criteria" ows ="4" cols="50"/></label>
            </p>
            
            <p>    
            <label>Year created: 
                <input type="text" name="creation_year_criteria" minlength= "4" maxlengh= "4" pattern = "(?:19|20)[0-9]{2}" 
                placeholder="YYYY" /></label>
            </p>
            
            <p>    
            <label>Date inserted: 
                <input type="text" name="inserted_date_criteria" pattern = "((?:19|20)[0-9]{2})-([0-9]{2})-([0-9]{2})" 
                       placeholder="YYYY-MM-DD"/></label>
            </p>
            
            <p>    
            <label>Creator: 
                <input type="text" name="creator_criteria"/></label>
            </p>
            
            
            <input name="submit-form" value="Search" type="submit" class="btn btn-lg btn-success">
            
            </form>
                </div>
    
            
    <?php
    include 'scripts/db.php';

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
        $insert_type_criteria = mysqli_real_escape_string($link, $_REQUEST['insert_type']);
    
        
        $ConditionArray = [];
        $ischarvalid = TRUE;
        
        if(!empty($id_criteria)) {
            if (!preg_match('/[^A-Za-z0-9]/', $id_criteria)) { 
                $ConditionArray[] = "t9.upstrain_id = '$id_criteria'";
            } else {
                $ischarvalid = FALSE;
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
                $ischarvalid = FALSE;
                echo nl2br ("\n \n Error: Non-valid character usage for 'Backbone'.");
            }
        }    
        
        if(!empty($insert_criteria)) {
            $ConditionArray[] = "(t9.entry_id IN (SELECT entry_inserts.entry_id FROM "
                    ."entry_inserts WHERE entry_inserts.insert_id IN (SELECT ins.id FROM "
                    . "ins WHERE (ins.name = '$insert_criteria'))))";
        }   
        
        if(!empty($bb_id_criteria)) {
            if (!preg_match('/[^A-Za-z0-9_]/', $bb_id_criteria)) {
                $ConditionArray[] = "t1.entry_reg = '$bb_id_criteria'";
            } else {
                $ischarvalid = FALSE;
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
                $ischarvalid = FALSE;
                echo nl2br ("\n \n Error: Non-valid character usage for 'Year created'.");                
            }
        } 
        
        if(!empty($inserted_date_criteria)) {
            if (!preg_match('/^(?:19|20)[0-9]{2})-([0-9]{2})-([0-9]{2})/', $inserted_date_criteria)) {
                $ConditionArray[] = "t1.date_db = '$inserted_date_criteria'";
            } else {
                $ischarvalid = FALSE;
                echo nl2br ("\n \n Error: Non-valid character usage for 'Date inserted'.");
            }
        } 
        
        if(!empty($creator_criteria)) {
                $ConditionArray[] = "(t2.first_name = '$creator_criteria' OR "
                        . "t2.last_name = '$creator_criteria' OR "
                        . "(CONCAT(t2.first_name,' ', t2.last_name) = '$creator_criteria'))";
        } 
        
        if(!empty($insert_type_criteria)) {
            $ConditionArray[] = "(t9.entry_id IN (SELECT entry_inserts.entry_id FROM "
                    . "entry_inserts WHERE entry_inserts.insert_id IN "
                    . "(SELECT ins.id FROM ins WHERE ins.type IN "
                    . "(SELECT ins_type.id FROM ins_type WHERE "
                    . "(ins_type.name = '$insert_type_criteria')))))";
            
        } 
        
        
        $entrysql = "SELECT DISTINCT t1.comment AS cmt, t1.year_created AS year, "
	."t1.date_db AS date, t1.entry_reg AS biobrick, t4.name AS strain, "
	."GROUP_CONCAT(DISTINCT t6.name) AS insname, "
        ."t3.name AS backbone, "
        ."t2.user_id AS user_id, "
        ."GROUP_CONCAT(DISTINCT t7.name) AS instype, "
	."t2.first_name AS fname, t2.last_name AS lname, "
        ."t9.upstrain_id AS up_id "
        ."FROM (entry AS t1) "
        ."LEFT JOIN entry_inserts AS t5 ON t5.entry_id = t1.id "
        ."LEFT JOIN ins AS t6 ON t6.id = t5.insert_id "              
        ."LEFT JOIN ins_type AS t7 ON t7.id = t6.type "        
        ."LEFT JOIN users AS t2 ON t1.creator = t2.user_id " 
        ."LEFT JOIN backbone AS t3 ON t1.backbone = t3.id "
        ."LEFT JOIN strain AS t4 ON t1.strain = t4.id "                      
        ."LEFT JOIN entry_upstrain AS t9 ON t1.id = t9.entry_id "                
        ."WHERE ";
        
        $sql = "";
        $result = "";
        $iserror = FALSE;
        
        if (count($ConditionArray) > 0) {
            $sql = $entrysql . implode(' AND ', $ConditionArray) . " GROUP BY up_id";
            $result=mysqli_query($link, $sql);
            $num_result_rows = mysqli_num_rows($result);
                      
        } else if ($ischarvalid && count($ConditionArray) == 0) {
            echo nl2br ("\n Error: Please enter search query");
        }
        
       mysqli_close($link) or die("Could not close database connection");
    }
        
    else if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('HTTP/1.0 405 Method Not Allowed');
    exit;
    }
	 
		
   if ($num_result_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>upstrain ID</th><th>Strain</th><th>Backbone</th>"
    . "<th>Insert</th><th>Insert Type</th><th>Year</th><th>iGEM registry entry</th>"
    . "<th>Creator</th><th>Added date</th><th>Comment</th></tr>";
    
    // output data of each row
    while($row = $result->fetch_assoc()) {
          $biobrick = "";
          if($row["biobrick"] === null || $row["biobrick"] == ''){ 
              $biobrick = "N/A";              
          } else { 
              $biobrick = "<a class=\"external\" href=\"http://parts.igem.org/Part:".$row["biobrick"]."\" target=\"_blank\">".$row["biobrick"]."</a>"; 
          }
            
        echo "<tr><td><a href=\"entry.php?upstrain_id=". $row["up_id"]."\">".$row["up_id"]."</a>".
                "</td><td>" . $row["strain"].
                "</td><td>" . $row["backbone"]. 
                "</td><td>" . $row["insname"].
                "</td><td>" . $row["instype"].
                "</td><td>" . $row["year"]. 
                "</td><td>" . $biobrick. 
                "</td><td>" . "<a href=\"user.php?user_id=".$row["user_id"]."\">". 
                $row["fname"]. " " . $row["lname"]. "</td><td>" . $row["date"].
                "</td><td>" . $row["cmt"]. "</td></tr>";
        }
    echo "</table>";	
    
   } else {
       $iserror = TRUE;
       $error = "No matching results, try another search.";
   }
    if ($iserror && !empty($ConditionArray)) {
        echo "<h3> Error: ".$error."</h3>";
        echo "<br>".
        "<a href=\"javascript:history.go(-1)\">Go back</a>";
}
?>
                        
            
   </main>
        <?php include 'bottom.php'; ?>
    </body>
</html>

