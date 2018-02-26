<?php
if (count(get_included_files()) == 1) exit("Access restricted."); // prevent direct access (included only)

// Displays page if user is logged in and is activated and has the right privileges
if($loggedin && $active && $userpage_owner_or_admin) {
	?>
	
	<?php
	//Set display for the content div
	if (isset($_GET['content'])) {
		$current_content = $_GET['content'];
	} else {
		$current_content = "";
	}

	$user_id = $user_id;;
	
	// If a form has been submitted
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		include 'scripts/db.php';
		
		$iserror = FALSE;
		
		// Change first and last name
		if (isset($_POST['first_name']) && isset($_POST['last_name']) && $_POST['first_name'] != "" && $_POST['last_name'] != "") {
			$fname = mysqli_real_escape_string($link, $_POST['first_name']);
			$lname = mysqli_real_escape_string($link, $_POST['last_name']);
			$update_msg = "name";
			$update_sql = "UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ".$user_id;
			if ($stmt = mysqli_prepare($link, $update_sql)) {
					mysqli_stmt_bind_param($stmt, "ss", $fname, $lname);
					if (mysqli_stmt_execute($stmt)) {
						$update_msg = "Successfully updated ".$update_msg.".";
					} else {
						$iserror = TRUE;
						$update_msg = "Failed to execute statement. ".mysqli_stmt_error($stmt);
					}
					mysqli_stmt_close($stmt);
				} else {
					$iserror = TRUE;
					$update_msg = "Failed to prepare statement. ".mysqli_stmt_error($stmt);
				}
		} else {
			// Change first name
			if (isset($_POST['first_name']) && $_POST['first_name'] != "" && $_POST['last_name'] == "") {
				$to_update = "first_name";
				$update_val = mysqli_real_escape_string($link, $_POST['first_name']);
				$update_msg = "first name";
			// Change last name
			} else if (isset($_POST['last_name']) && $_POST['last_name'] != "" && $_POST['first_name'] == "") {
				$to_update = "last_name";
				$update_val = mysqli_real_escape_string($link, $_POST['last_name']);
				$update_msg = "last name";
			// Change user name
			} else if (isset($_POST['user_name']) && $_POST['user_name'] != "") {
				if ($adminpage && !$isowner) {
					$iserror = TRUE;
					$update_msg = "Can't change other admin's username.";
				} else {
					$to_update = "username";
					$update_val = mysqli_real_escape_string($link, $_POST['user_name']);
					$update_msg = "username";
				}
			// Change email
			} else if (isset($_POST['email']) && $_POST['email'] != "") {
				$to_update = "email";
				$update_val = mysqli_real_escape_string($link, $_POST['email']);
				$update_msg = "email";
			// Change phone number
			} else if (isset($_POST['phone']) && $_POST['phone'] != "") {
				$to_update = "phone";
				$update_val = mysqli_real_escape_string($link, $_POST['phone']);
				$update_msg = "phone number";
			// Remove phone number
			} else if (isset($_POST['remove_phone'])) {
				$remove_sql = "UPDATE users SET phone = '' WHERE user_id = ".$user_id;
				if ($result = mysqli_query($link, $remove_sql)) {
					$update_msg = "Successfully removed phone number.";
				} else {
					$iserror = TRUE;
					$update_msg = "Failed to remove phone number. ".mysqli_error($link);
				}
			}
			// Do the change
			if (isset($update_val) && $update_val != "") {
				$update_sql = "UPDATE users SET ".$to_update." = ? WHERE user_id = ".$user_id;
				if ($stmt = mysqli_prepare($link, $update_sql)) {
					mysqli_stmt_bind_param($stmt, "s", $update_val);
					if (mysqli_stmt_execute($stmt)) {
						$update_msg = "Successfully updated ".$update_msg.".";
					} else {
						$iserror = TRUE;
						$update_msg = "Failed to execute statement. ".mysqli_stmt_error($stmt);
					}
					mysqli_stmt_close($stmt);
				} else {
					$iserror = TRUE;
					$update_msg = "Failed to prepare statement. ".mysqli_stmt_error($stmt);
				}
			}
		}
		if ($iserror) {
			$update_msg = "<strong style=\"color:red\">Error: ".$update_msg."</strong>";
		} else if (isset($update_msg)) {
			$update_msg = "<strong style=\"color:green\">".$update_msg."</strong>";
		}
		
		
		mysqli_close($link);
	}
	?>

 	<?php
	// Fetch user information from database
	include 'scripts/db.php';
	
	$usersql = "SELECT first_name AS fname, last_name AS lname, "
	."email, phone, username AS uname, admin FROM users WHERE user_id = '$id'";
	$user_result = mysqli_query($link, $usersql);
	mysqli_close($link);
	
	$info = mysqli_fetch_assoc($user_result);
	?>
	 
	<!-- do some edit form shit -->
	<ul>
		<!-- Edit name -->
		<li>Name	<?php echo $info["fname"]." ".$info["lname"];
		if ($current_content != "name") { ?>
			<a href="?user_id=<?php echo $user_id; ?>&edit&content=name">Edit</a> 
		<?php } ?></li>
		<?php if($current_content == "name") { ?>
			<li><form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
				New first name
				<input type="text" name="first_name">
				New last name
				<input type="text" name="last_name">
				<input type="submit" value="Submit">
				<a href="?user_id=<?php echo $user_id; ?>&edit">Cancel</a>
			</form></li>
		<?php } ?>
		
		<!-- Edit user name -->
		<li>Username	<?php echo $info["uname"];
		if ($adminpage && !$isowner) {
			echo " Can't change";
		}
		else if ($current_content != "user_name") { ?>
			<a href="?user_id=<?php echo $user_id; ?>&edit&content=user_name">Edit</a>
		<?php } ?></li>
		<?php if($current_content == "user_name") { ?>
			<li><form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
				New user name
				<input type="text" name="user_name"> 
				<input type="submit" value="Submit">
				<a href="?user_id=<?php echo $user_id; ?>&edit">Cancel</a>
			</form></li>
		<?php } ?>
		
		<!-- Edit email -->
		<li>Email <?php echo $info["email"];
		if ($current_content != "email") { ?>
			<a href="?user_id=<?php echo $user_id; ?>&edit&content=email">Edit</a>
		<?php } ?></li>
		<?php if($current_content == "email") { ?>
			<li><form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
				New email
				<input type="email" name="email"> 
				<input type="submit" value="Submit">
				<a href="?user_id=<?php echo $user_id;?>&edit">Cancel</a>
			</form></li>
		<?php } ?>
		
		<!-- Edit phone number -->
		<li>Phone number <?php echo $info["phone"];
		if ($current_content != "phone") { ?>
			<a href="?user_id=<?php echo $user_id;?>&edit&content=phone">Edit</a>
			<form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
				<input type="hidden" name="remove_phone">
				<input type="submit" value="Remove">
			</form>
		<?php } ?></li>
		<?php if($current_content == "phone") { ?>
			<li><form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
				New phone number
				<input type="text" name="phone"> 
				<input type="submit" value="Submit">
				<a href="?user_id=<?php echo $user_id;?>&edit">Cancel</a>
			</form></li>
		<?php } ?>
	<ul>
	
	<!-- Show success/error message -->
	<?php if($_SERVER['REQUEST_METHOD']=='POST' && isset($update_msg)): echo "<br>".$update_msg; endif; ?>

<?php
// Hides page if the user is not logged in or activated
} else {
	if (!$loggedin) {
		?>
		<h3 style="color:red">Access denied (you are not logged in).</h3>
		<?php
	}
	else if (!$active) {
		?>
		<h3 style="color:red">Access denied (your account is not activated yet).</h3>
		<?php			
	} 
	else {
		?>
		<h3 style="color:red">You are not allowed to edit this profile (you are not the owner or an admin).</h3>
		<?php
	}
	?>
	<br>
	<a href="javascript:history.go(-1)">Go back</a>
	<?php
}
?>