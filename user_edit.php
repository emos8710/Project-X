<?php
if (count(get_included_files()) == 1) exit("Access restricted."); // prevent direct access (included only)

// Displays page if user is logged in and is activated and has the right privileges
if($loggedin && $active && $userpage_owner_or_admin) {
	//Set display for the content div
	if (isset($_GET['content'])) {
		$current_content = $_GET['content'];
	} else {
		$current_content = "";
	}
	
	// Update procedures
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		include 'scripts/db.php';
		
		$iserror = FALSE;
		
		// Change first and last name at the same time
		if (isset($_POST['first_name']) && isset($_POST['last_name']) && $_POST['first_name'] != "" && $_POST['last_name'] != "") {
			$fname = mysqli_real_escape_string($link, $_POST['first_name']);
			$lname = mysqli_real_escape_string($link, $_POST['last_name']);
			// Check if characters have been removed
			if ($fname != $_POST['first_name']) {
				$iserror = TRUE;
				$update_msg = "First name contains invalid characters.";
				goto errorTime;
			}else if ($lname != $_POST['last_name']) {
				$iserror = TRUE;
				$update_msg = "Last name contains invalid characters.";
				goto errorTime;
			}
			$update_sql = "UPDATE users SET first_name = ?, last_name = ? WHERE user_id = ".$user_id;
			// Do the change
			if ($stmt = mysqli_prepare($link, $update_sql)) {
					mysqli_stmt_bind_param($stmt, "ss", $fname, $lname);
					if (mysqli_stmt_execute($stmt)) {
						$update_msg = "Successfully updated first and last name.";
					} else {
						$iserror = TRUE;
						$update_msg = "Couldn't change first and last name, failed to execute statement. ".mysqli_stmt_error($stmt);
					}
					mysqli_stmt_close($stmt);
				} else {
					$iserror = TRUE;
					$update_msg = "Couldn't change first and last name, failed to prepare statement. ".mysqli_stmt_error($stmt);
				}
		// Change separate values
		} else {
			// Change first name
			if (isset($_POST['first_name']) && $_POST['first_name'] != "" && $_POST['last_name'] === "") {
				$to_update = "first_name";
				$user_input = $_POST['first_name'];
				$update_val = mysqli_real_escape_string($link, $_POST['first_name']);
				$update_msg = "first name";
			// Change last name
			} else if (isset($_POST['last_name']) && $_POST['last_name'] != "" && $_POST['first_name'] === "") {
				$to_update = "last_name";
				$user_input = $_POST['last_name'];
				$update_val = mysqli_real_escape_string($link, $_POST['last_name']);
				$update_msg = "last name";
			// Change user name
			} else if (isset($_POST['user_name']) && $_POST['user_name'] != "") {
				// Check if an admin is trying to change another admin's username
				if ($adminpage && !$isowner) {
					$iserror = TRUE;
					$update_msg = "Can't change other admin's username.";
				} else {
					$to_update = "username";
					$user_input = $_POST['user_name'];
					$update_val = mysqli_real_escape_string($link, $_POST['user_name']);
					$update_msg = "username";
				}
			// Change email
			} else if (isset($_POST['email']) && $_POST['email'] != "") {
				$to_update = "email";
				$user_input = $_POST['email'];
				$update_val = mysqli_real_escape_string($link, $_POST['email']);
				$update_msg = "email";
			// Change phone number
			} else if (isset($_POST['phone']) && $_POST['phone'] != "") {
				$to_update = "phone";
				$user_input = $_POST['phone'];
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
			// Change password
			} else if (isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['conf_password']) && $_POST['old_password'] != "" && $_POST['new_password'] != "" && $_POST['conf_password'] != "") {
				
				// Check that the input old password matches the stored password
				$result = mysqli_query($link, "SELECT password FROM users WHERE user_id = '$user_id'");
				$password = mysqli_fetch_assoc($result);
				$password = $password['password'];
				if (!password_verify($_POST['old_password'], $password)) {
					$iserror = TRUE;
					$update_msg = "Wrong password.";
					goto errorTime;
				}
				// Make sure user is changing their own password, and that the new password qualifies
				if ($_SESSION['user_id'] != $user_id) {
					$iserror = TRUE;
					$update_msg = "Can't change someone else's password!";
					goto errorTime;
				} else if (strlen($_POST['new_password'])<8) {
					$iserror = TRUE;
					$update_msg = "The new password is too short.";
					goto errorTime;
				} else if ($_POST['new_password'] != $_POST['conf_password']) {
					$iserror = TRUE;
					$update_msg = "The new passwords don't match.";
					goto errorTime;
				}
				
				// Hash and change to new password
				$hash = mysqli_real_escape_string($link, password_hash($_POST['new_password'], PASSWORD_BCRYPT));
				$password_sql = "UPDATE users SET password = '$hash' WHERE user_id = '$user_id'";
				if ($result = mysqli_query($link, $password_sql)) {
					$update_msg = "Successfully changed the password.";
				} else {
					$iserror = TRUE;
					$update_msg = "Failed to change the password. ".mysqli_error($link);
				}
			}
			// Execute the change of first name, last name, username, email or phone number
			if (isset($update_val) && $update_val != "" && !$iserror) {
				// Check if user input invalid characters
				if ($update_val != $user_input) {
					$iserror = TRUE;
					$update_msg = "Input ".$to_update." contains invalid characters.";
					goto errorTime;
				}
				// Prepare and execute statement
				$update_sql = "UPDATE users SET ".$to_update." = ? WHERE user_id = ".$user_id;
				if ($stmt = mysqli_prepare($link, $update_sql)) {
					mysqli_stmt_bind_param($stmt, "s", $update_val);
					if (mysqli_stmt_execute($stmt)) {
						$update_msg = "Successfully updated ".$update_msg.".";
					} else {
						$iserror = TRUE;
						$update_msg = "Couldn't update ".$update_msg.", failed to execute statement. ".mysqli_stmt_error($stmt);
					}
					mysqli_stmt_close($stmt);
				} else {
					$iserror = TRUE;
					$update_msg = "Couldn't update ".$update_msg.", failed to prepare statement. ".mysqli_stmt_error($stmt);
				}
			}
		}
		errorTime:	// Go here when there is an error
		// Style the success or error message. 
		if ($iserror) {
			$update_msg = "<strong style=\"color:red\">Error: ".$update_msg."</strong>";
		} else if (isset($update_msg)) {
			$update_msg = "<strong style=\"color:green\">".$update_msg."</strong>";
		}
		
		mysqli_close($link);
	}
	?>
	<!-- Confirm action popup -->
	<script>
    function confirmAction(e, msg) {
        if (!confirm(msg))
            e.preventDefault();
    }
	</script>
 	<?php
	// Show the user page
	// Fetch user information from database
	include 'scripts/db.php';
	
	$usersql = "SELECT first_name AS fname, last_name AS lname, "
	."email, phone, username AS uname, admin FROM users WHERE user_id = '$id'";
	$user_result = mysqli_query($link, $usersql);
	mysqli_close($link) or die("Could not close database connection");
	
	$info = mysqli_fetch_assoc($user_result);
	?>
	 
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
				<input type="text" name="user_name" pattern=".{3,50}" required title="The username must be 3-50 characters long">
				<?php if($_SESSION['user_id'] == $user_id) { ?>
					<input type="submit" value="Submit" onclick="confirmAction(event, 'Do you really want to change your username?')">
				<?php } else { ?>
					<input type="submit" value="Submit" onclick="confirmAction(event, 'This is not your account! Do you still want to change the username?')">
				<?php } ?>
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
				<input type="email" name="email" required> 
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
		
		<?php if ($_SESSION['user_id'] == $user_id) {
		// Change password
		?>
			<li><?php if ($current_content != "password") { ?>
				<a href="?user_id=<?php echo $user_id; ?>&edit&content=password">Change password</a>
			<?php }
				if($current_content == "password") { ?>
					<form action="user.php?user_id=<?php echo $user_id; ?>&edit" method="POST">
						Old password
						<input type="password" name="old_password" required>
						New password
						<input type="password" name="new_password" required pattern=".{8,}" title="The password must be at least 8 characters">
						Confirm new password
						<input type="password" name="conf_password" required pattern=".{8,}" title="The password must be at least 8 characters">
						<input type="submit" value="Submit">
						<a href="?user_id=<?php echo $user_id; ?>&edit">Cancel</a>
					</form></li>
			<?php }
		} ?>
	<ul>
	
	<!-- Show success/error message -->
	<?php if($_SERVER['REQUEST_METHOD']=='POST' && isset($update_msg)): echo "<br>".$update_msg; endif; ?>
	<!-- Back button -->
	<a href="javascript:history.go(-1)">Go back</a>

<?php
// Hides page if the user is not logged in or activated
} else {
	if (!$loggedin) {
		?>
		<h3 style="color:red">Access denied (you are not logged in).</h3>
		<?php
	} else if (!$active) {
		?>
		<h3 style="color:red">Access denied (your account is not activated yet).</h3>
		<?php			
	} else {
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