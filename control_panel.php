<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
	session_start();
}

//Set display for the content div
if (isset($_GET['content'])) {
	$current_content = $_GET['content'];
} else {
	$current_content = '';
}

// Set display for history div
$show_history = isset($_GET['history']);
if ($show_history) $history_content = $_GET['history'];
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

$title = "Control Panel";
?>

<!DOCTYPE html>

<?php include 'top.php'; ?>

<body>
	
	<?php 
	if($loggedin && $active && $admin) {
		?>
		
		<main>
			<div class="innertube">
				<h2>Control Panel</h2>
				<br>
				
				<!-- Nav menu with links to display desired content -->
				<div class="control_panel_menu">
				<h3>Navigation</h3>
					<ul>
						<a href="?content=manage_users">Manage users</a>
						<a href="?content=manage_entries">Manage entries</a>
						<a href="?content=event_log">Event log</a></li>
					</ul>
				</div>
				
				<br>
				<br>
				
				<div class="panel-history-show">
					<?php
					
					if ($show_history && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['history'])) {						
						if (isset($_POST['restore_data']) && isset($_POST['restore_user'])) {
							include 'scripts/db.php';
							$restore_id = mysqli_real_escape_string($link, $_POST['restore_data']);
							$user_id = mysqli_real_escape_string($link, $_POST['restore_user']);
							
							$check_exists = mysqli_query($link, "SELECT * from users WHERE user_id = ".$user_id);
							$deleted = (mysqli_num_rows($check_exists) < 1);
							
							if ($deleted) {
								$restore_sql = "INSERT INTO users(active, admin, email, first_name, hash, last_name, password, phone, username, user_id) "
								."SELECT active, admin, email, first_name, hash, last_name, password, phone, username, user_id FROM users_log "
								."WHERE old_data_id = ".$restore_id.";";
							} else {
								$old_data_sql = "SELECT email, first_name, last_name, phone, username "
								."FROM users_log WHERE old_data_id = ".$restore_id.";";
								
								$old_data = mysqli_fetch_assoc(mysqli_query($link, $old_data_sql));
								$restore_sql = "UPDATE users SET email = '".$old_data['email']."', first_name = '".$old_data['first_name']."', last_name = '".$old_data['last_name']
								."', phone = '".$old_data['phone']."', username = '".$old_data['username']."' WHERE user_id = ".$user_id;
							}
							
							$restore_query = mysqli_query($link, $restore_sql);
							
							?>
							<p>
							<?php
							if(!$restore_query) {
								?>
								<strong style="color:red">Error: <?php echo mysqli_error($link); ?></strong>
								<?php
							} else {
								?>
								<strong style="color:green">User info successfully restored!</strong>
								<?php
							}
							?>
							<br>
							Reloading in 10 seconds... <a href="<?php echo $_SERVER['REQUEST_URI']; ?>">Reload now</a>
							<?php
							header("Refresh: 10; url=".$_SERVER['REQUEST_URI']);
						}
						
						if ($history_content == "user") {
							include 'scripts/db.php';
						
							$id = mysqli_real_escape_string($link, $_POST['history']);
							
							$current_info_sql = "SELECT username, first_name, last_name, email, phone, admin FROM users WHERE user_id = ".$id;
							$current_info_query = mysqli_query($link, $current_info_sql);
							$old_info_sql = "SELECT old_data_id AS id, user_id AS uid, username, first_name, last_name, email, phone, admin, type, FROM_UNIXTIME(time) AS time FROM users_log WHERE user_id = ".$id." ORDER BY time DESC";
							$old_info_query = mysqli_query($link, $old_info_sql);
							
							$is_deleted = (mysqli_num_rows($current_info_query) < 1);
							$has_history = (mysqli_num_rows($old_info_query) >= 1);
							
							mysqli_close($link) or die("Could not close connection to database");
						?>
						
							<h3>User <?php echo $id; ?> info history</h3>
							<em>Logged history is automatically removed after 30 days.</em>
							
							<p>
								<table class="control-panel-history">
								<col><col><col><col><col><col><col><col>
								<tr>
									<th colspan="3"></th>
									<th colspan="4">Current data</th>
								</tr>
								<tr>
								<?php
								if ($is_deleted) {
									?>
									<td colspan="7"><strong>No active data (user has been removed).</strong></td>
									<?php
								} else {
									$data = mysqli_fetch_assoc($current_info_query);
									?>
									<th colspan="3"></th>
									<th>Username</th>
									<th>Name</th>
									<th>E-mail address</th>
									<th>Phone number</th>
									<th>Admin</th>
									</tr>
									<tr>
									<td colspan="3"></td>
									<td><?php echo $data['username']; ?></td>
									<td><?php echo $data['first_name']." ".$data['last_name']; ?></td>
									<td><?php echo $data['email']; ?></td>
									<td><?php echo $data['phone']; ?></td>
									<td><?php if ($data['admin'] == '1'): echo "Yes"; else: echo "No"; endif; ?></td>
									<?php
								}
								?>
								</tr>
								<tr>
									<th colspan="3"></th>
									<th colspan="4">Old data</th>
								</tr>
								<?php
								if ($has_history) {
									?>
									<tr>
										<th></th>
										<th>Time recorded</th>
										<th>Event type</th>
										<th>Username</th>
										<th>Name</th>
										<th>E-mail address</th>
										<th>Phone number</th>
									</tr>
									<?php
									while ($data = mysqli_fetch_assoc($old_info_query)) {
										?>
										<tr>
											<td>
												<form class="control-panel" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
													<input type="hidden" name="restore_data" value="<?php echo $data['id']; ?>">
													<input type="hidden" name="restore_user" value="<?php echo $data['uid']; ?>">
													<input type="hidden" name="history" value="<?php echo $id; ?>"> 
													<button type="submit" class="control-panel-restore" title="Restore" onclick="confirmAction(event, 'Restore user <?php echo $data['uid']; ?> to this record?')"/>
												</form>
											</td>
											<td><?php echo $data['time']; ?></td>
											<td><?php echo $data['type']; ?></td>
											<td><?php echo $data['username']; ?></td>
											<td><?php echo $data['first_name']." ".$data['last_name']; ?></td>
											<td><?php echo $data['email']; ?></td>
											<td><?php echo $data['phone']; ?></td>
										</tr>
										<?php										
									}
								} else {
									?>
									<tr>
										<td colspan="7"><strong>No old data recorded.</td>
									</tr>
									<?php
								}
								?>
								</table>
							</p>
							<?php
						} else if ($history_content == "entry") {
							include 'scripts/db.php';
							
							$id = mysqli_real_escape_string($link, $_POST['history']);
							
							$current_info_sql = "SELECT entry.id AS eid, entry.comment AS cmt, entry.year_created AS year, entry.date_db AS date, "
							."entry.entry_reg AS biobrick, entry_upstrain.upstrain_id AS uid, backbone.name AS bname, "
							."strain.name AS sname, users.user_id AS usid, users.username AS usname, users.first_name AS fname, users.last_name AS lname FROM entry "
							."LEFT JOIN entry_upstrain ON entry_upstrain.entry_id = entry.id "
							."LEFT JOIN backbone ON entry.backbone = backbone.id "
							."LEFT JOIN strain ON entry.strain = strain.id "
							."LEFT JOIN users ON entry.creator = users.user_id "
							."WHERE entry.id = ".$id;
							$current_info_query = mysqli_query($link, $current_info_sql);
							
							$old_info_sql = "SELECT FROM_UNIXTIME(entry_log.time) AS time, entry_log.type, entry_log.id AS eid, entry_log.comment AS cmt, entry_log.year_created AS year, entry_log.date_db AS date, "
							."entry_log.entry_reg AS biobrick, backbone.name AS bname, "
							."strain.name AS sname, users.first_name AS fname, users.last_name AS lname FROM entry_log "
							."LEFT JOIN backbone ON entry_log.backbone = backbone.id "
							."LEFT JOIN strain ON entry_log.strain = strain.id "
							."LEFT JOIN users ON entry_log.creator = users.user_id "
							."WHERE entry_log.id = ".$id;
							$old_info_query = mysqli_query($link, $old_info_sql);
							
							$is_deleted = (mysqli_num_rows($current_info_query) < 1);
							$has_history = (mysqli_num_rows($old_info_query) >= 1);
														
							mysqli_close($link) or die("Could not close connection to database");
							?>
							
							<h3>Entry <?php echo $id ?> info history</h3>
							<em>Logged history is automatically removed after 30 days.</em>
							
							<p>
								<table class="control-panel-history">
								<col><col><col><col><col><col><col><col>
								<tr>
									<th colspan="2"></th>
									<th colspan="6">Current data</th>
								</tr>
								<?php
								if ($is_deleted) {
									?>
									<tr>
									<td colspan="8"><strong>No active data (entry has been removed).</strong></td>
									</tr>
									<?php
								} else {
									$data = mysqli_fetch_assoc($current_info_query);
									?>
									<tr>
									<th colspan="2"></th>
									<th>Entry ID</th>
									<th>Comment</th>
									<th>Year created</th>
									<th>Date added</th>
									<th>iGEM Registry ID</th>
									<th>Backbone</th>
									<th>Strain</th>
									<th>Created by</th>
									</tr>
									<tr>
										<td colspan="2"></td>
										<td><?php echo $data['eid']; ?></td>
										<td><?php echo $data['cmt']; ?></td>
										<td><?php echo $data['year']; ?></td>
										<td><?php echo $data['date']; ?></td>
										<td><?php echo $data['biobrick']; ?></td>
										<td><?php echo $data['bname']; ?></td>
										<td><?php echo $data['sname']; ?></td>
										<td><?php echo $data['fname']." ".$data['lname']; ?></td>
									</tr>
									<?php
								}
								?>
								<tr>
									<th colspan="2"></th>
									<th colspan="6">Old data</th>
								</tr>
								<?php
								if ($has_history) {
									?>
									<tr>
										<th>Time recorded</th>
										<th>Event type</th>
										<th>Username</th>
										<th>Name</th>
										<th>E-mail address</th>
										<th>Phone number</th>
										<th>Admin</th>
									</tr>
									<?php
									while ($data = mysqli_fetch_assoc($old_info_query)) {
										?>
										<tr>
											<td><?php echo $data['time']; ?></td>
											<td><?php echo $data['type']; ?></td>
											<td><?php echo $data['year']; ?></td>
											<td><?php echo $data['date']; ?></td>
											<td><?php echo $data['biobrick']; ?></td>
											<td><?php echo $data['bname']; ?></td>
											<td><?php echo $data['sname']; ?></td>
											<td><?php echo $data['fname']." ".$data['lname']; ?></td>
										</tr>
										<?php										
									}
								} else {
									?>
									<tr>
										<td colspan="8"><strong>No old data recorded.</td>
									</tr>
									<?php
								}
								?>
								</table>
							<p>
							
							<?php
						} else {
							echo "This should never happen";
						}
					}
					
					?>
					
					
				</div>
				
				<!-- Desired content is displayed here -->
				<div class="control-panel-show">
				
					<?php if ($current_content == "manage_users") {
						$current_url = "control_panel.php?content=manage_users";
						?>
						
						<h3>Manage users</h3>
					
						<?php
						
						// Perform form requests
						if($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['history'])) {
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
							$is_admin = (mysqli_fetch_array($check_admin_query)[0] == '1');
							$is_active = (mysqli_fetch_array($check_admin_query)[1] == '1');
							
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
									<col><col><col><col><col><col><col><col><col><col><col>
									<tr>
										<th>User ID</th>
										<th>Username</th>
										<th>Name</th>
										<th>E-mail address</th>
										<th>Phone number</th>
										<th>User level</th>
										<th colspan="4">Actions</th>
									</tr>
									
									<?php
									while($user = mysqli_fetch_assoc($userquery)) {
										?>
										<tr>
											<td><a href="user.php?user_id=<?php echo $user['user_id']; ?>"><?php echo $user['user_id']; ?></a></td>
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
												<form class="control-panel" action="<?php echo $current_url; ?>&history=user" method="POST">
													<input type="hidden" name="history" value="<?php echo $user['user_id']; ?>">
													<button type="submit" class="control-panel-history" title="View user info history"/>
												</form>
											</td>
											<td>
												<form class="control-panel" action="<?php echo $current_url; ?>" method="POST">
													<input type="hidden" name="admin" value="<?php echo $user['user_id']; ?>">
													<button type="submit" class="control-panel-admin" title="Make admin" onclick="confirmAction(event, 'Really want to make this user admin?')"/>
												</form>
											</td>
											<td>
												<form class="control-panel" action="<?php echo $current_url; ?>" method="POST">
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
						$current_url = "control_panel.php?content=manage_entries";
						?>
						<h3>Manage entries</h3>
						
						<?php
						if($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['history'])) {
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
										<th colspan="3">Actions</th>
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
													<button class="control-panel-edit" title="Edit entry"/>
												</form>
											</td>
											<td>
												<form class="control-panel" action="<?php echo $current_url; ?>&history=entry" method="POST">
													<input type="hidden" name="history" value="<?php echo $row['eid']; ?>">
													<button type="submit" class="control-panel-history" title="View entry history"/>
												</form>
											</td>
											<td>
												<form class="control-panel" action="<?php echo $current_url; ?>" method="POST">
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
						$current_url = "control_panel.php?content=event_log";
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
								</tr>
								
								<?php
								while ($log = mysqli_fetch_assoc($logquery)) {
									?>
									<tr>
										<td><?php echo $log['time']; ?></td>
										<td><?php echo $log['type']; ?></td>
										<td><?php echo $log['object_id']; ?></td>
										<td><?php echo $log['object']; ?></td>
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