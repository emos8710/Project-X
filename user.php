<?php

if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
	session_start();
}

//Function for parsing URL variable (making sure user ID is proper)
function check_user_id($input) {
	return preg_match('/^\d+$/', $input) == 1;
}

$is_user_error = !isset($_GET['user_id']);

if ($is_user_error) {
	$user_error = "No user id specified.";
} else {
	// Fetch the user id from URL and check if it is valid
	$user_id = $_GET['user_id'];
	
	$is_user_error = !check_user_id($user_id);
	if($is_user_error) {
		$user_error = "Invalid user ID.";
	}
}

if (!$is_user_error) {

	// Connect to database
	include 'scripts/db.php';
	
	// Check if user exists
	$id = mysqli_real_escape_string($link, $user_id);
	$sql = "SELECT user_id, username AS uname FROM users WHERE user_id LIKE '$id'";
	$result = mysqli_query($link, $sql);

	$is_mysql_error = !$result;
	if($is_mysql_error) {
		$mysql_error = mysqli_error($link);
	} else {
		$is_mysql_error = mysqli_num_rows($result) < 1;
		if ($is_mysql_error) {
			$mysql_error = "No such user";
		} else {
			$is_mysql_error = mysqli_num_rows($result) > 1;
			if ($is_mysql_error) $mysql_error = "This should never happen";
		}
	}
	
	if (!$is_mysql_error) {
		$username = mysqli_fetch_assoc($result)['uname'];
	}
	
	mysqli_free_result($result);
	
	// Close database connection
	mysqli_close($link) or die("Could not close database connection");
	
	$edit = isset($_GET['edit']);
	
	$isowner = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id);
	
}

if($is_user_error) {
	$title = "Error: ".$user_error;
} else if($is_mysql_error) {
	$title = "Error: ".$mysql_error;
} else {
	$title = "User ".$username;
}
	
?>

<!DOCTYPE html>

<?php include 'top.php'; ?>

<body>
<main>
	<div class="innertube">	
		<?php
		
		// Print error text...
		if ($is_user_error || $is_mysql_error) {
			if ($is_user_error): echo "<h3>Error: ".$user_error."</h3>";
			elseif ($is_mysql_error): echo "<h3>Error: ".$mysql_error."</h3>";
			endif;
			echo "<br>".
			"<a href=\"javascript:history.go(-1)\">Go back</a>";
		// ... Or show user information or edit page
		} else {
			
			// Connect to database
			include 'scripts/db.php';
			
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
			
			// Fetch user info
			$info = mysqli_fetch_assoc($user_result);
			
			$adminpage = $info['admin'] == 1; // check if current page is an admin's
			$adminpage_owner = ($adminpage && $isowner);
			$userpage_owner_or_admin = ($isowner || $admin);
		
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