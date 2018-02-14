<?php
	if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	// Fetch the user id from URL
	$user_id = $_GET["user_id"];
	
	// IMPLEMENT WHEN LOGIN WORKS
	$isloggedin = TRUE;
	//if(!isset($_SESSION['user_id'])){
	//	$isloggedin = TRUE;
	//}
	//else {
	//	
	//}
	$isadmin = FALSE;
	//if(isset($_SESSION['isadmin'])) {
	//	$isadmin = TRUE;
	//}
	$isuser = TRUE;
	//if($_session['user_id'] == $user_id) {
	//	$isuser = TRUE;
	//}
	
	// Connect to database
	include 'scripts\db.php';
	
	// Check if user exists
	$id = mysqli_real_escape_string($link, $user_id);
	$sql = "SELECT user_id FROM users WHERE user_id LIKE '$id'";
	$result = mysqli_query($link, $sql);

	$iserror = FALSE;
	if(!$result) {
		$iserror = TRUE;
		$error = mysqli_error();
	} elseif(mysqli_num_rows($result) < 1) {
		$iserror = TRUE;
		$error = "No such user";
	} elseif(mysqli_num_rows($result) > 1) {
		$iserrr = TRUE;
		$error = "This should never happen";
	}
	
	// Close database connection
	mysqli_close($link) or die("Could not close database connection");

	if($iserror) {
		$title = "Error: ".$error;
	} else {
		$title = "User ".$user_id;
	}
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $title; ?></title>
	<link href="css/upstrain.css" rel="stylesheet">
</head>

<body>
	<!-- Include site navigation -->
	<?php include 'top.php'; ?>

	<main>
		<div class="innertube">

			<?php
			// Print error text...
			if($iserror) {
				echo "<h3>Error: ".$error."</h3>";
				echo "<br>".
				"<a href=\"javascript:history.go(-1)\">Go back</a>";
			} 
			// ... or show user page
			else {
				// Connect to database
				include 'scripts\db.php';
				
				// Fetch user information from database
				$usersql = "SELECT first_name AS fname, last_name AS lname, "
				."email, phone, username AS uname, admin FROM users WHERE user_id = '$id'";
				$user_result = mysqli_query($link, $usersql);
				
				// Fetch information about entries from database
				$entrysql = "SELECT entry.id AS eid, entry.comment, entry.year_created, entry.date_db, "
				."entry.entry_reg, entry_upstrain.upstrain_id AS uid, backbone.name AS bname, "
				."strain.name AS sname, entry_inserts.*, ins.name AS iname FROM entry "
				."LEFT JOIN entry_upstrain ON entry_upstrain.entry_id = entry.id "
				."LEFT JOIN backbone ON entry.backbone = backbone.id "
				."LEFT JOIN strain ON entry.strain = strain.id "
                ."LEFT JOIN entry_inserts ON entry_inserts.entry_id = entry.id "
                ."LEFT JOIN ins ON entry_inserts.insert_id = ins.id AND entry_inserts.entry_id = entry.id "
				."WHERE entry.creator = '$id' "
				."ORDER BY entry.id";
				$entry_result = mysqli_query($link, $entrysql);
				
				// Close database connection
				mysqli_close($link) or die("Could not close database connection");
				
				// Put user and first row of entry info in arrays
				$info = mysqli_fetch_assoc($user_result);
				$entry = mysqli_fetch_assoc($entry_result);
			
				if ($isloggedin) {?>
				<!-- Show user information -->
				<!-- Shows user as admin or user -->
				<?php if ($info["admin"] == 1) echo "<h2>Admin ";
				else echo "<h2>User ";
				echo $info["uname"]."</h2>"; ?>
				<br>
				<h3>Contact information</h3>
				<p>Name: <?php echo $info["fname"]." ".$info["lname"] ?>
				<br>Email: <?php echo $info["email"] ?>
				<br>Phone: <?php echo $info["phone"] ?></p>
				
				<?php } else {
					echo "<p>You need to log in to see contact information.</p>";
				}?>
				<br>
				
				<!-- Show entry information -->
				<h3>User entries</h3>
				<!-- Create table -->
				<table class="user_entries">
					<tr>
						<th>Entry ID</th>
						<th>Strain</th>
						<th>Backbone</th>
						<th>Inserts</th>
						<th>Year created</th>
						<th>Registry</th>
						<th>Comment</th>
					</tr>
				
					<?php // Fill table one entry at a time
					while ($entry) {
						$current_entry = $entry["eid"];
						
						// Part 1 of entry row
						$tpart_1 = "<tr>"
						."<td><a href=\"entry.php?upstrain_id=".$entry["uid"]."\">".$entry["uid"]."</a></td>"
						."<td>".$entry["sname"]."</td>"
						."<td>".$entry["bname"]."</td>";
						
						// Decide if user can edit entries
						if ($isadmin OR $isuser) {
							$edit = "<td style=\"border: none;\">"
							."<a class=\"edit\" href=\"entry.php?upstrain_id=".$entry["uid"]."&edit=1\">Edit</a></td>";
						} else $edit = "";
						
						// Part 3 of entry row, with or without edit option
						$tpart_3 = "<td>".$entry["year_created"]."</td>"
						."<td>".$entry["entry_reg"]."</td>"
						."<td>".$entry["comment"]."</td>"
						.$edit
						."</tr>";
						
						// Part 2 of entry row, find all inserts
						$inserts = $entry["iname"];
						$entry = mysqli_fetch_assoc($entry_result);
						while (TRUE) {
							// Check if different entry or end of results
							if(!$entry OR $entry["eid"] != $current_entry) {
								break;
							}
							// Add next insert to list
							$inserts = $inserts."<br>".$entry["iname"];
							$entry = mysqli_fetch_assoc($entry_result);
						}
					
						$tpart_2 = "<td>".$inserts."</td>";
						
						// Piece together the parts to form a row of the table
						echo $tpart_1.$tpart_2.$tpart_3;
					}
				// End table
				echo "</table>";
			} ?>
		</div>
	</main>
	<!-- Include site footer -->
	<?php include 'bottom.php'; ?>		

</body>

</html>