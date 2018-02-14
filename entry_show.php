<main>
	<div class="innertube">

			<?php
			
			//Print error...
			if($iserror) {
				?>
				
				<h3>Error: <?php echo $error ?></h3>
				<br>
				<a href="javascript:history.go(-1)">Go back</a>
				
				<?php
				
			//...or do everything else
			} else {
				
				echo "<h2>UpStrain Entry ".$upstrain_id."</h2>";
				
				if($admin) {
					?>
					<p>
					<a class="edit" href="<?php echo $_SERVER['REQUEST_URI'] ?>&edit=1">Edit entry</a>
					</p>
					<?php
				}
				
				include 'scripts/db.php';
				
				$entrysql = "SELECT entry.comment AS cmt, entry.year_created AS year, "
				."entry.date_db AS date, entry.entry_reg AS biobrick, strain.name AS strain, "
				."users.first_name AS fname, users.last_name AS lname, users.user_id AS user_id FROM entry, entry_upstrain, "
				."users, strain WHERE entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = "
				."entry.id AND entry.creator = users.user_id AND entry.strain = strain.id";
				$entryquery = mysqli_query($link, $entrysql);
				
				$backbonesql = "SELECT backbone.name AS name, backbone.Bb_reg AS biobrick, "
				."backbone.date_db AS date, users.first_name AS fname, users.last_name AS lname, users.user_id AS user_id FROM backbone, entry, entry_upstrain, users WHERE "
				."entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = entry.id AND "
				."entry.backbone = backbone.id AND backbone.creator = users.user_id";
				$backbonequery = mysqli_query($link, $backbonesql);
				
				$insertsql = "SELECT ins.name AS name, ins.ins_reg AS biobrick, ins_type.name AS type, ins.date_db AS date, "
				."users.first_name AS fname, users.last_name AS lname, users.user_id AS user_id FROM ins, ins_type, entry, entry_upstrain, "
				."users, entry_inserts WHERE entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = entry.id AND entry_inserts.entry_id = "
				."entry.id AND entry_inserts.insert_id = ins.id AND ins.type = ins_type.id AND ins.creator = users.user_id";
				$insertquery = mysqli_query($link, $insertsql);
				
				$filesql = "SELECT name_new AS filename FROM upstrain_file WHERE upstrain_file.upstrain_id = '$id'";
				$filequery = mysqli_query($link, $filesql);
				
				$mysqlerror = FALSE;
				$rowserror = FALSE;
				if(!$entryquery || !$backbonequery || !$insertquery || !$filequery) {
					$mysqlerror = TRUE;
				} else {
				
					$filerows = mysqli_num_rows($filequery);
					if($filerows < 1) {
						$hasfile = FALSE;
					} elseif($filerows == 1) {
						$hasfile = TRUE;
					}else {
						$rowserror = TRUE;
						$errormsg = '<h3 style=\"color:red\">Error: Database returned unexpected number of rows</h3>';
					}					
					
					$entryrows = mysqli_num_rows($entryquery);
					$backbonerows = mysqli_num_rows($backbonequery);
					$insertrows = mysqli_num_rows($insertquery);
					
					if(($entryrows != 1) || ($backbonerows != 1) || ($insertrows < 1)) {
						$rowserror = TRUE;
						$errormsg = '<h3 style=\"color:red\">Error: Database returned unexpected number of rows</h3>';
					}
					
				}
				
				if($rowserror || $mysqlerror) {
					echo $errormsg;
				} else {
					$entrydata = mysqli_fetch_assoc($entryquery);
					$backbonedata = mysqli_fetch_assoc($backbonequery);
					
					$inserts = [];
					while ($row = mysqli_fetch_assoc($insertquery)){
						array_push($inserts, $row);
					}
					
					if($hasfile){
						$filedata = mysqli_fetch_assoc($filequery);
					}
					
					mysqli_close($link) or die("Could not close database connection");
					
					?>
					<div class="entry_table">
						<table class="entry">
							<col><col>
							<tr>
								<th colspan="2"> Entry details</th>
							</tr>
							<tr>
								<td><strong>Strain:</strong></td>
								<td><?php echo $entrydata["strain"] ?></td>
							</tr>
							<tr>
								<td><strong>Year created:</strong></td>
								<td><?php echo $entrydata["year"] ?></td>
							</tr>
							<tr>
								<td><strong>iGEM registry entry:</strong></td>
								<td><?php if($entrydata["biobrick"] === null || $entrydata["biobrick"] == ''){ echo "N/A"; } 
								else { echo "<a class=\"external\" href=\"http://parts.igem.org/Part:".$entrydata["biobrick"]."\" target=\"_blank\">".$entrydata["biobrick"]."</a>"; } ?></td>
							</tr>
							<tr>
								<td><strong>Added by:</strong></td>
								<td><?php echo "<a href=\"user.php?user_id=".$entrydata["user_id"]."\">".$entrydata["fname"]." ".$entrydata["lname"]."</a>"; ?></td>
							</tr>
							<tr>
							<td><strong>Date added:</strong></td>
							<td><?php echo $entrydata["date"]; ?> </td>
							</tr>
							<tr>
								<td><strong>Comment:</strong></td>
								<td rowspan="2"><?php echo $entrydata["cmt"]; ?></td>
							</tr>
							<tr>
							</tr>
							<tr>
							<td><?php if($hasfile) { echo "<strong>Download sequence (FASTA):</strong>"; } ?></td>
							<td><?php if($hasfile) { echo "<a href=\"files/".$filedata["filename"]."\" download>".$filedata["filename"]."</a>"; } ?></td>
							</tr>
						</table>
					</div>
					
					<div class="backbone_inserts">
						<table class="entry">
							<col><col>
							<tr>
								<th colspan="2">Backbone</th>
							</tr>
							<tr>
								<td><strong>Name:</strong></td>
								<td><?php echo $backbonedata["name"] ?></td>
							</tr>
							<tr>
								<td><strong>iGEM registry entry:</strong></td>
								<td><?php if($backbonedata["biobrick"] === null || $backbonedata["biobrick"] == ''){ echo "N/A"; } 
								else { echo "<a class=\"external\" href=\"http://parts.igem.org/Part:".$backbonedata["biobrick"]."\" target=\"_blank\">".$backbonedata["biobrick"]."</a>"; } ?></td>
							</tr>
							<tr>
								<td><strong>Added by:</strong></td>
								<td><?php echo "<a href=\"user.php?user_id=".$backbonedata["user_id"]."\">".$backbonedata["fname"]." ".$backbonedata["lname"]."</a>"; ?></td>
							</tr>
							<tr>
								<td><strong>Date added:</strong></td>
								<td><?php echo $backbonedata["date"]; ?> </td>
							</tr>
							<tr>
							<?php for ($i = 0; $i < $insertrows; $i++) {
								?>
								<tr>
									<th colspan="2"><?php echo "Insert "; if ($insertrows > 1) {echo ($i+1);} ?></th>
								</tr>
								<tr>
									<td><strong>Name:</strong></td>
									<td><?php echo $inserts[$i]["name"]; ?></td>
								</tr>
								<tr>
								<tr>
									<td><strong>iGEM registry entry:</strong></td>
									<td><?php if($inserts[$i]["biobrick"] === null || $inserts[$i]["biobrick"] == ''){ echo "N/A"; } 
									else { echo "<a class=\"external\" href=\"http://parts.igem.org/Part:".$inserts[$i]["biobrick"]."\" target=\"_blank\">".$inserts[$i]["biobrick"]."</a>"; } ?></td>
								</tr>
								<tr>
									<td><strong>Added by:</strong></td>
									<td><?php echo "<a href=\"user.php?user_id=".$inserts[$i]["user_id"]."\">".$inserts[$i]["fname"]." ".$inserts[$i]["lname"]."</a>"; ?></td>
								</tr>
								<tr>
									<td><strong>Date added:</strong></td>
									<td><?php echo $inserts[$i]["date"]; ?> </td>
								</tr>
								<tr>
									<td><strong>Type:</strong></td>
									<td><?php echo $inserts[$i]["type"]; ?></td>
								</tr>
								<?php
							}
							?>
						</table>
					</div>
					<?php
				}
			}
			?>
	</div>
</main>