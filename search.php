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
		<title>Search for entries</title>
		<link href="css/upstrain.css" rel="stylesheet">
    </head>
<body>
        
	<?php include 'top.php'; ?>
        
	<main>
		<div class="innertube">
			<form class="search-form" action="search.php" method="post" id="searchform">
				<h2>Search for entries</h2>
				<div>
					<div class="field-wrap"
						<label>Upstrain ID</label>
						<input type="text" name="id_criteria" placeholder="UUYYYYXXX" pattern = "UU\d{7,10}" title="Upstrain ID must match pattern UUYYYYXXX."/>
					</div>
					
					<div class="field-wrap">
						<label>Strain</label>
						<input type="text" name="strain_criteria"/>
					</div>
					
					<div class="field-wrap">
						<label>Insert</label>
						<input type="text" name="insert_criteria"/>
					</div>
					
					<div class="field-wrap">
						<label>Year created</label>
                                                <input type="text" name="creation_year_criteria" minlength= "4" maxlengh= "4" pattern = "(?:19|20)[0-9]{2}" 
						placeholder="YYYY" title ="Must contain four digits for year."/>
					</div>
					
					<div class="field-wrap">
						<label>Creator</label>
						<input type="text" name="creator_criteria" placeholder=""/>
					</div>
					
					<input name="submit-form" value="Search" type="submit">
					
				</div>
				
				<div>
					<div class="field-wrap">
						<label>Biobrick registry ID</label>
						<input type="text" name="bb_id_criteria" placeholder="BBa_K[X]" pattern="BBa_K\d{4,12}" title ="Biobrick ID must match pattern BBa_KXXXXX."/>
					</div>
					
					<div class="field-wrap">
						<label>Backbone</label>
						<input type="text" name="backbone_criteria"/>
					</div>
					
					<div class="field-wrap">
						<label>Insert Type</label>
						<select name="insert_type">
							<option value=""></option>    
							<option value="promotor">Promotor</option>
							<option value="coding">Coding</option>
						</select>    
					</div>
					
					<div class="field-wrap">
						<label>Date inserted</label>
						<input type="date" name="inserted_date_criteria" pattern = "((?:19|20)[0-9]{2})-([0-9]{2})-([0-9]{2})" 
							   placeholder="YYYY-MM-DD" title="Must match date pattern YYYY-MM-DD"/>
					</div>
					
					<div class="field-wrap">
						<label>Comment</label>
						<input type="text" name="comment_criteria" rows ="4" cols="50"/>
					</div>
				</div>
			</form>
		
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
			."t1.date_db AS date, t1.entry_reg AS biobrick, "
                        ."t1.private AS private, t4.name AS strain, "
			."GROUP_CONCAT(DISTINCT t6.name SEPARATOR ', ') AS insname, "
			."t3.name AS backbone, "
			."t2.user_id AS user_id, "
			."GROUP_CONCAT(DISTINCT t7.name SEPARATOR ', ') AS instype, "
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
			
			$num_result_rows = 0;
			
			// If there are results, show them
			if (count($ConditionArray) > 0) {
				$sql = $entrysql . implode(' AND ', $ConditionArray) . " GROUP BY up_id";
				$result = mysqli_query($link, $sql);
				$num_result_rows = mysqli_num_rows($result);
				
			} else if ($ischarvalid && count($ConditionArray) == 0) {
				echo nl2br ("\n Error: Please enter search query");
			}
			if ($num_result_rows > 0) {
				echo "<table>";
				echo "<tr><th>UpStrain ID</th><th>Strain</th><th>Backbone</th>"
				. "<th>Insert</th><th>Insert Type</th><th>Year</th><th>iGEM Registry</th>"
				. "<th>Creator</th><th>Added date</th><th>Comment</th></tr>";
				
				// output data of each row
				while($row = $result->fetch_assoc()) {
					$biobrick = "";
					if($row["biobrick"] === null || $row["biobrick"] == ''){ 
						$biobrick = "N/A";              
					} else { 
						$biobrick = "<a class=\"external\" href=\"http://parts.igem.org/Part:".$row["biobrick"]."\" target=\"_blank\">".$row["biobrick"]."</a>"; 
					}
					if (!$loggedin && $row["private"] == 1)	{
                                            
                                        } else {
					echo "<tr><td><a href=\"entry.php?upstrain_id=". $row["up_id"]."\">".$row["up_id"]."</a>".
							"</td><td>" . $row["strain"].
							"</td><td>" . $row["backbone"]. 
							"</td><td>" . $row["insname"].
							"</td><td>" . $row["instype"].
							"</td><td>" . $row["year"]. 
							"</td><td>" . $biobrick. 
							"</td><td>" . "<a href=\"user.php?user_id=".$row["user_id"]."\">". 
							$row["fname"]. " " . $row["lname"]. "</td><td>" . $row["date"].
							"</td><td class=\"comment\">" . $row["cmt"]. "</td></tr>";
				}
                            }
				echo "</table>";	
		
			}
			// If there are no rows, create error
			else {
				$iserror = TRUE;
				$error = "No matching results, try another search.";
			}
			// Show errors
			if ($iserror && !empty($ConditionArray)) {
				echo "<h3> Error: ".$error."</h3>";
			}
			
			mysqli_close($link) or die("Could not close database connection");
		}
		?>
        
        </div>    
	</main>
    <?php include 'bottom.php'; ?>
</body>
</html>

