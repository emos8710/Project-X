<!DOCTYPE html>


<?php
	session_start();
	
	// Fetch the upstrain id from URL
	$upstrain_id = $_GET["upstrain_id"];

	include 'scripts/db.php';

	$id = mysqli_real_escape_string($link, $upstrain_id);
	$sql = "SELECT upstrain_id FROM entry_upstrain WHERE upstrain_id LIKE '$id'";
	$result = mysqli_query($link, $sql);

	$iserror = FALSE;
	if(!$result) {
		$iserror = TRUE;
		$error = mysqli_error();
	} elseif(mysqli_num_rows($result) < 1) {
		$iserror = TRUE;
		$error = "No such entry";
	}

	mysqli_close($link) or die("Could not close database connection");

	if($iserror) {
		$title = "Error: ".$error;
	} else {
		$title = "UpStrain Entry ".$upstrain_id;
	}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title; ?></title>
	<link href="css/upstrain.css" rel="stylesheet">
</head>

<body>


	<?php include 'top.php'; ?>

	<!-- Body content of page -->
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
					
					include 'scripts/db.php';
					
					$entrysql = "SELECT entry.comment AS cmt, entry.year_created AS year, "
					."entry.date_db AS date, entry.entry_reg AS biobrick, strain.name AS strain, "
					."users.full_name AS uname, users.user_id AS user_id FROM entry, entry_upstrain, "
					."users, strain WHERE entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = "
					."entry.id AND entry.creator = users.user_id AND entry.strain = strain.id";
					$entryquery = mysqli_query($link, $entrysql);
					
					$backbonesql = "SELECT backbone.name AS name, backbone.Bb_reg AS biobrick, "
					."backbone.year_created AS year, backbone.date_db AS date, users.full_name AS uname, users.user_id AS user_id FROM backbone, entry, entry_upstrain, users WHERE "
					."entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = entry.id AND "
					."entry.backbone = backbone.id AND backbone.creator = users.user_id";
					$backbonequery = mysqli_query($link, $backbonesql);
					
					$insertsql = "SELECT DISTINCT ins.name AS name, ins.ins_reg AS biobrick, ins_type.name AS type, ins.year_created AS year, "
					."ins.date_db AS date, users.full_name AS uname, users.user_id AS user_id FROM ins, ins_type, entry, entry_upstrain, "
					."users, entry_inserts WHERE entry_upstrain.upstrain_id = '$id' AND entry_upstrain.entry_id = entry.id AND entry_inserts.entry_id = "
					."entry.id AND ins.type = ins_type.id AND ins.creator = users.user_id";
					$insertquery = mysqli_query($link, $insertsql);
					
					$filesql = "SELECT name_new AS filename FROM upstrain_file WHERE upstrain_file.upstrain_id = '$id'";
					$filequery = mysqli_query($link, $filesql);
					
					$rowserror = FALSE;
					$filerows = mysqli_num_rows($filequery);
					if($filerows < 1) {
						$hasfile = FALSE;
					} elseif($filerows == 1) {
						$hasfile = TRUE;
					}else {
						$rowserror = TRUE;
					}					
					
					$entryrows = mysqli_num_rows($entryquery);
					$backbonerows = mysqli_num_rows($backbonequery);
					$insertrows = mysqli_num_rows($insertquery);
					
					if(($entryrows != 1) || ($backbonerows != 1) || ($insertrows < 1)) {
						$rowserror = TRUE;
					}
					
					if($rowserror) {
						echo "<h3 style=\"color:red\">Error: Database returned unexpected number of rows</h3>";
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
									<td><?php echo "<a href=\"user.php?user_id=".$entrydata["user_id"]."\">".$entrydata["uname"]."</a>"; ?></td>
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
								<?php for($i = 1; $i <= $insertrows; $i++){echo "<col><col>";} ?>
								<tr>
									<th colspan="2">Backbone</th>
									<?php for($i = 1; $i <= $insertrows; $i++){
										?>
										<th colspan="2">Insert<?php echo " $i"; ?></th>
										<?php
									}
									?>
								</tr>
								<tr>
									<td><strong>Name:</strong></td>
									<td><?php echo $backbonedata["name"] ?></td>
									<?php for($i = 0; $i < $insertrows; $i++) {
										?>
										<td><strong>Name:</strong></td>
										<td><?php echo $inserts[$i]["name"]; ?></td>
										<?php
									}
									?>
								</tr>
								<tr>
									<td><strong>Year created:</strong></td>
									<td><?php echo $backbonedata["year"] ?></td>
									<?php for($i = 0; $i < $insertrows; $i++) {
										?>
										<td><strong>Year created:</strong></td>
										<td><?php echo $inserts[$i]["year"]; ?></td>
										<?php
									}
									?>
								</tr>
								<tr>
									<td><strong>iGEM registry entry:</strong></td>
									<td><?php if($backbonedata["biobrick"] === null || $backbonedata["biobrick"] == ''){ echo "N/A"; } 
									else { echo "<a class=\"external\" href=\"http://parts.igem.org/Part:".$backbonedata["biobrick"]."\" target=\"_blank\">".$backbonedata["biobrick"]."</a>"; } ?></td>
									<?php for($i = 0; $i < $insertrows; $i++) {
										?>
										<td><strong>iGEM registry entry:</strong></td>
										<td><?php if($inserts[$i]["biobrick"] === null || $inserts[$i]["biobrick"] == ''){ echo "N/A"; } 
										else { echo "<a class=\"external\" href=\"http://parts.igem.org/Part:".$inserts[$i]["biobrick"]."\" target=\"_blank\">".$inserts[$i]["biobrick"]."</a>"; } ?></td>
										<?php
									}
									?>
								</tr>
								<tr>
									<td><strong>Added by:</strong></td>
									<td><?php echo "<a href=\"user.php?user_id=".$backbonedata["user_id"]."\">".$backbonedata["uname"]."</a>"; ?></td>
									<?php for($i = 0; $i < $insertrows; $i++) {
										?>
										<td><strong>Added by:</strong></td>
										<td><?php echo "<a href=\"user.php?user_id=".$inserts[$i]["user_id"]."\">".$inserts[$i]["uname"]."</a>"; ?></td>
										<?php
									}
									?>
								</tr>
								<tr>
									<td><strong>Date added:</strong></td>
									<td><?php echo $backbonedata["date"]; ?> </td>
									<?php for($i = 0; $i < $insertrows; $i++) {
										?>
										<td><strong>Date added:</strong></td>
										<td><?php echo $inserts[$i]["date"]; ?> </td>
										<?php
									}
									?>
								</tr>
								<tr>
									<td></td>
									<td></td>
									<?php for($i = 0; $i < $insertrows; $i++) {
										?>
										<td><strong>Type:</strong></td>
										<td><?php echo $inserts[$i]["type"]; ?></td>
										<?php
									}
									?>
								</tr>
							</table>
						</div>
						<?php
					}
				}
				?>
		</div>
	</main>
	
	<?php include 'bottom.php'; ?>

</body>

</html>