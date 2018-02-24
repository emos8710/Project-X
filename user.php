<?php
	if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	//Function for parsing URL variable (making sure user ID is proper)
	function check_user_id($input) {
	if (preg_match('/^\d+$/', $input) == 1): return TRUE; else: return FALSE; endif;
	}
	
	$is_user_error = FALSE;
	$is_mysql_error = FALSE;
	
	if (!isset($_GET['user_id'])) {
		$is_user_error = TRUE;
		$user_error = "No user id specified.";
	} else {
		
		// Fetch the user id from URL
		$user_id = $_GET['user_id'];
		
		if(!check_user_id($user_id)) {
			$is_user_error = TRUE;
			$user_error = "Invalid user ID.";
		}
	}
	
	if (!$is_user_error) {
	
		// Connect to database
		include 'scripts\db.php';
		
		// Check if user exists
		$id = mysqli_real_escape_string($link, $user_id);
		$sql = "SELECT user_id, username AS uname FROM users WHERE user_id LIKE '$id'";
		$result = mysqli_query($link, $sql);

		if(!$result) {
			$is_mysql_error = TRUE;
			$mysql_error = mysqli_error();
		} elseif(mysqli_num_rows($result) < 1) {
			$is_mysql_error = TRUE;
			$mysql_error = "No such user";
		} elseif(mysqli_num_rows($result) > 1) {
			$is_mysql_error = TRUE;
			$mysql_error = "This should never happen";
		}
		
		$username = mysqli_fetch_assoc($result)['uname'];
		mysqli_free_result($result);
		
		// Close database connection
		mysqli_close($link) or die("Could not close database connection");
		
		if (isset($_GET["edit"])) {
			$edit = TRUE;
		} else {
			$edit = FALSE;
		}
		
		if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
			$isowner = TRUE;
		}
		else {
			$isowner = FALSE;
		}
	}
	
	if($is_user_error) {
		$title = "User ID error";
	} else if($is_mysql_error) {
		$title = "Database error";
	} else {
		$title = "User ".$username;
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
			if ($is_user_error || $is_mysql_error) {
				if($is_user_error) echo "<h3>Error: ".$user_error."</h3><br>";
				if($is_mysql_error) echo "<br><h3>Error: ".$mysql_error."</h3>";
				echo "<br>".
				"<a href=\"javascript:history.go(-1)\">Go back</a>";
			// ... Or show user information or edit page
			} else {
				
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
				
				//Fetch user info
				$info = mysqli_fetch_assoc($user_result);
			
				if($edit) {
					include 'user_edit.php';
				} else {
					include 'user_show.php';
				}
			}
			
			?>
		</div>
	</main>

	<!-- Include site footer -->
	<?php include 'bottom.php'; ?>		

</body>

</html>