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
//Set display for the content div
if (isset($_GET['content'])) {
	$current_content = $_GET['content'];
} else {
	$current_content = '';
}
?>

<script>
	function confirmAction(e, msg) {
		if(!confirm(msg))e.preventDefault();
	}
</script>

<?php

	// Database stuff
	
	include 'scripts/db.php';
	
	// Fetch all users (admins first)
	$usersql = "SELECT user_id, username, first_name, last_name, email, phone, admin FROM users ORDER BY admin DESC, user_id ASC";
	$userquery = mysqli_query($link, $usersql) or die("MySQL error: ".mysqli_error($link));
	
	// Fetch all entries
	$entrysql = "SELECT entry.id AS eid, entry.comment AS cmt, entry.year_created AS year, entry.date_db AS date, "
	."entry.entry_reg AS biobrick, entry_upstrain.upstrain_id AS uid, backbone.name AS bname, "
	."strain.name AS sname, ins.name AS iname, users.user_id AS usid, users.username AS usname, users.first_name AS fname, users.last_name AS lname FROM entry "
	."LEFT JOIN entry_upstrain ON entry_upstrain.entry_id = entry.id "
	."LEFT JOIN backbone ON entry.backbone = backbone.id "
	."LEFT JOIN strain ON entry.strain = strain.id "
	."LEFT JOIN entry_inserts ON entry_inserts.entry_id = entry.id "
	."LEFT JOIN ins ON entry_inserts.insert_id = ins.id AND entry_inserts.entry_id = entry.id "
	."LEFT JOIN users ON entry.creator = users.user_id "
	."ORDER BY eid";
	$entryquery = mysqli_query($link, $entrysql) or die("MySQL error: ".mysqli_error($link));
	
	// Fetch event log
	$logsql = "SELECT * from event_log ORDER by time DESC";
	$logquery = mysqli_query($link, $logsql) or die("MySQL error: ".mysqli_error($link));
	
	mysqli_close($link) or die("Could not close connection to database");

?>

<body>
	
	<?php include 'top.php';
	
	if($loggedin && $active && $admin) {
		?>
		
		<main>
			<div class="innertube">
				<h2>Control Panel</h2>
				<br>
				
				<!-- Nav menu with links to display desired content -->
				<div class="control_panel_menu">
				<h3>Navigation</h3>
					<ul class="control_panel_nav">
						<li><a href="?content=manage_users">Manage users</a></li>
						<li><a href="?content=manage_entries">Manage entries</a></li>
						<li><a href="?content=event_log">Event log</a></li>
					</ul>
				</div>
				
				<br>
				<br>
				
				<!-- Desired content is displayed here -->
				<div class="control_panel_show">
				
					<?php if ($current_content == "manage_users") {
						?>
						
						<h3>Manage users</h3>
					
						<?php
						
						// Perform form requests
						if($_SERVER['REQUEST_METHOD'] == 'POST') {
							?>
							<p>
							<?php
							include 'scripts/db.php';
							
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
							
							$check_admin_sql = "SELECT admin, active from users WHERE user_id = ".$id;
							$check_admin_query = mysqli_query($link, $check_admin_sql) or die("MySQL error: ".mysqli_error($link));
							if (mysqli_fetch_array($check_admin_query)[0] == '1'): $is_admin = TRUE; else: $is_admin = FALSE; endif;
							if (mysqli_fetch_array($check_admin_query)[1] == '1'): $is_active = TRUE; else: $is_active = FALSE; endif;
							
							if ($delete) {
								
								if ($user_id == $_SESSION['user_id']) {
									$delete_msg = "<strong style=\"color:red\">You cannot remove yourself!</strong>";
								} else if ($is_admin) {
									$delete_msg = "<strong style=\"color:red\">You cannot remove an admin!</strong>";
								} else {
									$deletesql = "DELETE FROM users WHERE user_id = ".$id;
									if (!$deletequery = mysqli_query($link, $deletesql)): $delete_msg = "<strong style=\"color:red\">Database error: Cannot remove user (user probably has entries).</strong>";
										else: $delete_msg = "<strong style=\"color:green\">User successfully deleted!</strong>"; endif;
								}
								echo $delete_msg;
							}
							
							if ($make_admin) {
								
								if ($user_id == $_SESSION['user_id']) {
									?>
									<strong style="color:red">You are already an admin!</strong>
									<?php
								} else if ($is_admin) {
									$admin_msg = "<strong style=\"color:red\">User is already an admin!</strong>";
								} else if (!$is_active) {
									$admin_msg = "<strong style=\"color:red\">User is not activated!</strong>";
								} else {
									$adminsql = "UPDATE users SET admin='1' WHERE user_id = ".$id;
									$adminquery = mysqli_query($link, $adminsql);
									$admin_msg = "<strong style=\"color:green\">User ".$user_id."is now an admin!</strong>";
								}
								echo $admin_msg;
								
							}
							
							mysqli_close($link) or die("Could not close connection to database");
							
							?>
							<br>
							Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
							<?php
							header("Refresh: 10; url=".$_SERVER['REQUEST_URI']);
							?>
							</p>
							<?php
						}
						?>
						
						
						<p>
								<?php if (mysqli_num_rows($userquery) < 1) {
									?>
									<strong>No users to show</strong>
									<?php
								} else {
									?>
									<table class="control-panel-users">
									<col><col><col><col><col><col><col><col><col><col>
									<tr>
										<th>User ID</th>
										<th>Username</th>
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
											<td><?php echo $user['username']; ?></td>
											<td><?php echo $user['first_name']." ".$user['last_name']; ?></td>
											<td><?php echo $user['email']; ?></td>
											<td><?php echo $user['phone']; ?></td>
											<td><?php if ($user['admin'] == 1): echo "Admin"; else: echo "User"; endif; ?></td>
											<td>
												<form class="control-panel" action="user.php" method="GET">
													<input type="hidden" name="user_id" value="<?php echo "".$user['user_id'].""; ?>">
													<input type="hidden" name="edit">
													<button class="control-panel-edit" title="Edit user" type="submit"/>
												</form>
											</td>
											<td>
												<form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
													<input type="hidden" name="admin" value="<?php echo $user['user_id']; ?>">
													<button type="submit" class="control-panel-admin" title="Make admin" onclick="confirmAction(event, 'Really want to make this user admin?')"/>
												</form>
											</td>
											<td>
												<form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
													<input type="hidden" name="delete" value="<?php echo $user['user_id']; ?>">
													<button type="submit" class="control-panel-delete" title="Delete user" onclick="confirmAction(event, 'Really want to delete this user?')"/>
												</form>
											</td>
										</tr>
										<?php
									}
								}
								?>
							</table>
						</p>
						
						<?php	
					} else if ($current_content == "manage_entries") {						
						?>
						<h3>Manage entries</h3>
						
						<?php
						if($_SERVER['REQUEST_METHOD'] == 'POST') {
							?>
							<p>
							<?php
							
							include 'scripts/db.php';
							
							$delete_entry = FALSE;
							if (isset($_POST['delete'])) {
								$entry_id = $_POST['delete'];
								$id = mysqli_real_escape_string($link, $entry_id);
								$delete_entry = TRUE;
							} else {
								echo "This should never happen.";
							}
							
							if ($delete_entry) {
								$deletesql = "DELETE FROM entry WHERE id = ".$entry_id;
								if(!$deletequery = mysqli_query($link, $deletesql)): $delete_msg = "<strong style=\"color:red\">Database error: ".mysqli_error($link)."</strong>";
								else: $delete_msg = "<strong style=\"color:green\">Entry successfully deleted!</strong>";
								endif;
								echo $delete_msg;
							}
							
							?>
							<br>
							Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
							<?php
							header("Refresh: 10; url=".$_SERVER['REQUEST_URI']);
							?>
							</p>
							<?php
						}
						?>
						
						<p>
								<?php
								if (mysqli_num_rows($entryquery) < 1) {
									?>
									<strong>No entries to show</strong>
									<?php
								} else {
									$row = mysqli_fetch_assoc($entryquery);
									$entry_inserts = array(); // create empty array for linking each entry with its inserts
									$rows = [];
									while ($row) { // will return FALSE when no more rows
										array_push($rows, $row);
										$current_id = $row['eid']; // fetch current entry ID
										$inserts = []; // empty array for storing inserts
										while ($current_id == $row['eid']) { // loop over all rows with the same entry ID
											array_push($inserts, $row['iname']); // add this row's insert to the array
											$row = mysqli_fetch_assoc($entryquery); // next row
											if(!$row) break; //break if no more rows
										}
										$entry_inserts[$current_id] = $inserts; // associate entry ID with its inserts in the array.
									}
									mysqli_data_seek($entryquery, 0); // reset result to beginning
									
									?>
									<table class="control-panel-entries">
									<col><col><col><col><col><col><col><col><col><col>
									<tr>
										<th>Upstrain ID</th>
										<th>Date added</th>
										<th>Strain</th>
										<th>Backbone</th>
										<th>Inserts</th>
										<th>Year created</th>
										<th>iGEM Registry</th>
										<th>Comment</th>
										<th>Created by</th>
										<th colspan="2">Actions</th>
									</tr>
									<?php
									foreach ($rows as $row) {
										?>
										<tr>
											<td><a href="entry.php?upstrain_id=<?php echo $row['uid']; ?>"><?php echo $row['uid']; ?></a></td>
											<td><?php echo $row['date']; ?></td>
											<td><?php echo $row['sname']; ?></td>
											<td><?php echo $row['bname']; ?></td>
											<?php 
											$current_entry = $row['eid'];
											$current_inserts = $entry_inserts[$current_entry];
											$ins_string = "";
											for ($i = 0; $i < count($current_inserts); $i++) {
												if ($i == count($current_inserts) - 1): $ins_string = $ins_string.$current_inserts[$i];
												else: $ins_string = $ins_string.$current_inserts[$i].", ";
												endif;
											}
											?>
											<td><?php echo $ins_string; ?></td>
											<td><?php echo $row['year']; ?></td>
											<td><?php echo $row['biobrick']; ?></td>
											<td><?php echo $row['cmt']; ?> </td>
											<td><a href="user.php?user_id=<?php echo $row['usid']; ?>"><?php echo $row['fname']." ".$row['lname']; ?></a></td>
											<td>
												<form class="control-panel" action="entry.php" method="GET">
													<input type="hidden" name="upstrain_id" value="<?php echo "".$row['uid'].""; ?>">
													<input type="hidden" name="edit">
													<button class="control-panel-edit" title="Edit entry" />
												</form>
											</td>
											<td>
												<form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
													<input type="hidden" name="delete" value="<?php echo $row['eid']; ?>">
													<button type="submit" class="control-panel-delete" title="Delete entry" onclick="confirmAction(event, 'Really want to delete this entry?')"/>
												</form>
											</td>
										</tr>
										<?php
									}
								}
								?>
							</table>
						</p>
						<?php
					} else if ($current_content == "event_log") {						
						?>
						<p>
						<h3>Event log</h3>
						
						<?php
						if (mysqli_num_rows($logquery) < 1) {
							?>
							<strong>No events logged.</strong>
							<?php
						} else {
							?>
							<table class="control-panel-log">
								<col><col><col><col><col>
								<tr>
									<th>Timestamp</th>
									<th>Event type</th>
									<th>Object ID</th>
									<th>Object type</th>
									<th>Comment</th>
								</tr>
								
								<?php
								while ($log = mysqli_fetch_assoc($logquery)) {
									?>
									<tr>
										<td><?php echo $log['time']; ?></td>
										<td><?php echo $log['type']; ?></td>
										<td><?php echo $log['object_id']; ?></td>
										<td><?php echo $log['object']; ?></td>
										<td><?php echo $log['edit_comment']; ?></td>
									</tr>
									<?php
								}
								?>
							</table>
							<?php
						}
						?>
						</p>
						<?php
					} else {
						echo "";
					}
					?>
				
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