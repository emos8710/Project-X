<?php
if (count(get_included_files()) == 1) exit("Access restricted."); // prevent direct access (included only)

$adminpage = $info['admin'] == 1; // check if current page is an admin's
$adminpage_owner = ($adminpage && $isowner);
$userpage_owner_or_admin = (!$adminpage && ($isowner || $admin));

// Displays page if user is logged in and activated and has the right privileges
if($loggedin && $active && ($adminpage_owner || $userpage_owner_or_admin)) {
	?>
	
	<?php
	//Set display for the content div
	if (isset($_GET['content'])) {
		$current_content = $_GET['content'];
	} else {
		$current_content = "";
	}
	?>
	
	<?php
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		include 'scripts/db.php';
		
		$update_msg = "";
		
		if (isset($_POST['first_name']) && $_POST['last_name']) {
			$fname = mysqli_real_escape_string($link, $_POST['first_name']);
			$lname = mysqli_real_escape_string($link, $_POST['last_name']);
			$update_sql = "UPDATE users SET first_name = '".$fname."', last_name = '".$lname."' WHERE user_id = ".$_GET['user_id'];
			$update = mysqli_query($link, $update_sql);
		} else {
			if (isset($_POST['first_name'])) {
				$to_update = "first_name";
				$update_val = mysqli_real_escape_string($link, $_POST['first_name']);
			} else if (isset($_POST['last_name'])) {
				$to_update = "last_name";
				$update_val = mysqli_real_escape_string($link, $_POST['last_name']);
			} else if (isset($_POST['user_name'])) {
				$to_update = "username";
				$update_val = mysqli_real_escape_string($link, $_POST['user_name']);
			} else if (isset($_POST['email'])) {
				$to_update = "email";
				$update_val = mysqli_real_escape_string($link, $_POST['email']);
			} else if (isset($_POST['phone'])) {
				$to_update = "phone";
				$update_val = mysqli_real_escape_string($link, $_POST['phone']);
			}
			$update_sql = "UPDATE users SET ".$to_update." = '".$update_val."' WHERE user_id = ".$_GET['user_id'];
			$update = mysqli_query($link, $update_sql);
		}
		if($update) {
			$update_msg = "<strong style=\"color:green\">Success!</strong>";
		} else {
			$update_msg = "<strong style=\"color:red\">Error</strong>";
		}
	}
	?>

	<?php
	include 'scripts/db.php';
	
	// Fetch user information from database
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
			<a href="?user_id=<?php echo $_GET['user_id'] ?>&edit&content=name">Edit</a> 
		<?php } ?></li>
		<?php if($current_content == "name") { ?>
			<li><form action="user.php?user_id=<?php echo $_GET['user_id'] ?>&edit" method="POST">
				New first name
				<input type="text" name="first_name">
				New last name
				<input type="text" name="last_name">
				<input type="submit" value="Submit">
				<a href="?user_id=<?php echo $_GET['user_id']?>&edit">Cancel</a>
			</form></li>
		<?php } ?>
		
		<!-- Edit user name -->
		<li>User name	<?php echo $info["uname"] ?>	<a href="?user_id=<?php echo $_GET['user_id']?>&edit&content=user_name">Edit</a></li>
		<?php if($current_content == "user_name") { ?>
			<li><form action="user.php?user_id=<?php echo $_GET['user_id'] ?>&edit" method="POST">
				New user name
				<input type="text" name="user_name"> 
				<input type="submit" value="Submit">
				<a href="?user_id=<?php echo $_GET['user_id']?>&edit">Cancel</a>
			</form></li>
		<?php } ?>
		
		<!-- Edit email -->
		<li>Email	<?php echo $info["email"] ?>	<a href="?user_id=<?php echo $_GET['user_id']?>&edit&content=email">Edit</a></li>
		<?php if($current_content == "email") { ?>
			<li><form action="user.php?user_id=<?php echo $_GET['user_id'] ?>&edit" method="POST">
				New email
				<input type="text" name="email"> 
				<input type="submit" value="Submit">
				<a href="?user_id=<?php echo $_GET['user_id']?>&edit">Cancel</a>
			</form></li>
		<?php } ?>
		
		<!-- Edit phone number -->
		<li>Phone	<?php echo $info["phone"] ?>	<a href="?user_id=<?php echo $_GET['user_id']?>&edit&content=phone">Edit</a></li>
		<?php if($current_content == "phone") { ?>
			<li><form action="user.php?user_id=<?php echo $_GET['user_id'] ?>&edit" method="POST">
				New phone number
				<input type="text" name="phone"> 
				<input type="submit" value="Submit">
				<a href="?user_id=<?php echo $_GET['user_id']?>&edit">Cancel</a>
			</form></li>
		<?php } ?>
	<ul>
	<br>
	<?php if($_SERVER['REQUEST_METHOD']=='POST'): echo $update_msg; endif; ?>

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
	<a href="entry.php?upstrain_id=<?php echo "$user_id" ?> ">Go back to the user page</a>
	<?php
}
?>