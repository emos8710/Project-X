<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
	session_start();
}
?>

<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Control Panel</title>
	<link href="css/upstrain.css" rel="stylesheet">
</head>

<?php
//Set default content for the content display div
if (isset($_GET['content'])) {
	$current_content = $_GET['content'];
} else {
	$current_content = '';
}
?>

<script>
	function confirmDelete(e)
	{
		if(!confirm('Do you really want to delete this user?'))e.preventDefault();
	}
	
	function confirmAdmin(e)
	{
		if(!confirm('Do you really want to make this user an admin?'))e.preventDefault();
	}
	
	function swapContent(target, source)
	{
		if(source == target) {
			return;
		}else if(source == '') {
			document.getElementById(target).innerHTML = document.getElementById('showContent').innerHTML;
		} else {
			document.getElementById(target).innerHTML = document.getElementById(source).innerHTML;
		}
	}
	
</script>

<?php

	// Database stuff
	
	include 'scripts/db.php';
	
	// Fetch all users (admins first)
	$usersql = "SELECT user_id, username, first_name, last_name, email, phone, admin FROM users ORDER BY admin DESC, user_id ASC";
	$userquery = mysqli_query($link, $usersql) or die("MySQL error: ".mysqli_error($link));
	
	// Fetch all entries
	$entrysql = "SELECT entry.id AS eid, entry.comment, entry.year_created, entry.date_db, "
	."entry.entry_reg, entry_upstrain.upstrain_id AS uid, backbone.name AS bname, "
	."strain.name AS sname, entry_inserts.*, ins.name AS iname, users.user_id, users.username FROM entry "
	."LEFT JOIN entry_upstrain ON entry_upstrain.entry_id = entry.id "
	."LEFT JOIN backbone ON entry.backbone = backbone.id "
	."LEFT JOIN strain ON entry.strain = strain.id "
	."LEFT JOIN entry_inserts ON entry_inserts.entry_id = entry.id "
	."LEFT JOIN ins ON entry_inserts.insert_id = ins.id AND entry_inserts.entry_id = entry.id "
	."LEFT JOIN users ON entry.creator = users.user_id "
	."ORDER BY entry.id";
	$entryquery = mysqli_query($link, $entrysql) or die("MySQL error: ".mysqli_error($link));
	
	mysqli_close($link) or die("Could not close connection to database");

?>

<body onload="swapContent('showContent', '<?php echo $current_content; ?>');">
	
	<?php include 'top.php';
	
	if($admin) {
		?>
		
		<main>
			<div class="innertube">
				<!-- Initially hidden divs with control panel content -->
				<div class="control_panel_hidden" id="manageUsers">
					<h3>Manage users</h3>
					
					<p>
					<?php
					
					// Perform form delete request
					if($_SERVER['REQUEST_METHOD'] == 'POST') {
						include 'db.php';
						
						$delete = FALSE;
						$make_admin = FALSE;
						if (isset($_POST['delete'])) {
							$user_id = $_POST['delete'];
							$id = mysqli_real_escape_string($link, $user_id);
							$delete = TRUE;
						} else if (isset($_POST['admin'])) {
							$user_id = $_POST['admin'];
							$id = mysqli_real_escape_string($link, $user_id);
							$make_admin = TRUE;
						} else {
							echo "This should never happen";
						}
						
						$check_admin_sql = "SELECT admin from users WHERE user_id = ".$id;
						$check_admin_query = mysqli_query($link, $check_admin_sql) or die("MySQL error: ".mysqli_error($link));
						if (mysqli_fetch_array($check_admin_query)[0] == '1'): $is_admin = TRUE; else: $is_admin = FALSE; endif;
						
						if ($delete) {
							
							if ($user_id == $_SESSION['user_id']) {
								?>
								<strong style="color:red">You cannot remove yourself!</strong>
								<?php
							} else if ($is_admin) {
								?>
								<strong style="color:red">You cannot remove an admin!</strong>
								<?php
							} else {
								$deletesql = "DELETE FROM users WHERE user_id = ".$id;
								$deletequery = mysqli_query($link, $deletesql) or die("MySQL error: ".mysqli_error($link));							
								?>
								<strong style="color:green">User successfully deleted!</strong>
								<?php
							}
							?>
							<br>
							Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
							<?php
							header("Refresh: 10; url=".$_SERVER['REQUEST_URI']);
						}
						
						if ($make_admin) {
							
							if ($user_id == $_SESSION['user_id']) {
								?>
								<strong style="color:red">You are already an admin!</strong>
								<?php
							} else if ($is_admin) {
								?>
								<strong style="color:red">User is already an admin!</strong>
								<?php
							} else {
								$adminsql = "UPDATE users SET admin='1' WHERE user_id = ".$id;
								$adminquery = mysqli_query($link, $adminsql);
								?>
								<strong style="color:green">User <?php echo $user_id; ?> is now an admin!</strong>
								<?php
							}
							?>
							<br>
							Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
							<?php
							header("Refresh: 10; url=".$_SERVER['REQUEST_URI']);
						}
					}
					?>
					</p>
					
					<p>
					<table class="control-panel-users">
					
						<?php if (mysqli_num_rows($userquery) < 1) {
							?>
							<tr>
								<td>No users to show</td>
							</tr>
							<?php
						} else {
							?>
							
							<col><col><col><col><col><col><col><col><col>
							<tr>
								<th>User ID</th>
								<th>Name</th>
								<th>E-mail address</th>
								<th>Phone number</th>
								<th>User level</th>
								<th colspan="3">Actions</th>
							</tr>
							
							<?php
							while($user = mysqli_fetch_assoc($userquery)) {
								?>
								<tr>
									<td><?php echo $user['user_id']; ?></td>
									<td><?php echo $user['first_name']." ".$user['last_name']; ?></td>
									<td><?php echo $user['email']; ?></td>
									<td><?php echo $user['phone']; ?></td>
									<td><?php if ($user['admin'] == 1): echo "Admin"; else: echo "User"; endif; ?></td>
									<td>
										<form class="control-panel" action="user.php?user_id=<?php echo $user['user_id']; ?>&edit" method="POST">
											<button class="control-panel-edit" title="Edit user" />
										</form>
									</td>
									<td>
										<form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
											<input type="hidden" name="admin" value="<?php echo $user['user_id']; ?>">
											<button type="submit" class="control-panel-admin" title="Make admin" onclick="confirmAdmin(event)"/>
										</form>
									</td>
									<td>
										<form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
											<input type="hidden" name="delete" value="<?php echo $user['user_id']; ?>">
											<button type="submit" class="control-panel-delete" title="Delete user" onclick="confirmDelete(event)"/>
										</form>
									</td>
								<?php
							}
						}
						?>
					</table>
					</p>
					
				</div>
				
				<div class="control_panel_hidden" id="manageEntries">
					<h3>Manage entries</h3>
				</div>
			
				<h2>Control Panel</h2>
				
				<br>
				
				<!-- Nav menu with links to display desired content -->
				<div class="control_panel_menu">
					<ul class="control_panel_nav">
						<li><a href="?content=manageUsers">Manage users</a></li>
						<li><a href="?content=manageEntries">Manage entries</a></li>
					</ul>
				</div>
				
				<br>
				<br>
				
				<!-- Desired content is displayed here -->
				<div class="control_panel_show" id="showContent">
				</div>
				
			</div>
		</main>
		
		<?php
	} else {
		?>
		<h3 style="color:red">Error: Access denied.</h3>
		<br>
		<a href="index.php">Go home</a>
		<?php
	}
	
	include 'bottom.php'; ?>
	
</body>
</html>