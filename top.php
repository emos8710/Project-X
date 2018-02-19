<?php
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 120)) {
    $_SESSION['logged_in']=false;
	session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
	$timeout_message = "You have been logged out due to inactivity. Please log in again.";
}
else {
	$_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
	$timeout_message = "";
}

if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
	$loggedin = TRUE;
} else {
	$loggedin = FALSE;
}

if(isset($_SESSION['admin']) && $_SESSION['admin']==1) {
	$admin=TRUE;
}
else {
	$admin=FALSE;
}
?>

<!-- Navigation bar and logo -->

<nav class="navigation">
	<!-- Logo -->
	<div class="logo">
		<h1>UpStrain</h1>
		<p>The plasmid database for iGEM Uppsala</p>
	</div>
	
	<div class="nav-wrapper">
		<!-- NAVIGATION BUTTONS -->
		<!-- Home -->
		<a <?php 
				if(basename($_SERVER['PHP_SELF']) == "index.php"){
					echo "class=\"active\" ";
				} 
			?> href="index.php">Home</a>
			
		<!-- Search -->
		<a <?php 
				if(basename($_SERVER['PHP_SELF']) == "search.php"){
					echo "class=\"active\" ";
				} 
			?> href="search.php">Search</a>
		
		<!--  New Entry -->
		<a <?php 
				if(basename($_SERVER['PHP_SELF']) == "new_insert.php"){
					echo "class=\"active\" ";
				} 
			?> href="new_insert.php">New entry</a>
			
		<!-- Help -->
		<a <?php 
				if(basename($_SERVER['PHP_SELF']) == "help.php"){
					echo "class=\"active\" ";
				} 
			?> href="help.php">Help</a>

		<!-- Profile (if logged in) -->			
		<?php
		if($loggedin && isset($_SESSION['user_id'])) { ?>
			<a <?php
				if(basename($_SERVER['REQUEST_URI']) == "user.php?user_id=".$_SESSION['user_id'] || basename($_SERVER['REQUEST_URI']) == "user.php?user_id=".$_SESSION['user_id']."&edit") {
					echo "class=\"active\" ";
				} 
				?> href="user.php?user_id=<?php echo $_SESSION['user_id']; ?>">My Profile</a>
		<?php
		}
		?> 

		<!-- Control Panel (if admin) -->		
		<?php if($loggedin && $admin && isset($_SESSION['user_id'])) { ?>
			<a <?php
					if(basename($_SERVER['PHP_SELF'])=="control_panel.php") {
						echo "class=\"active\"";
					}
				?> href="control_panel.php">Control Panel</a>
		<?php 
		}
		?>
		
		<?php echo "<strong style=\"font-size:10 color:red\">".$timeout_message."<strong>"; ?>
		
	</div>
	
	<div class="right-wrapper">
		<!-- Quick search -->
		<div class="quicksearch">
			<form class="quicksearch" action="entry.php">
				<input class ="quicksearch" type="text" placeholder="Search UpStrain ID" name="upstrain_id"></input>
				<button class="quicksearch" type="submit"><img class="quicksearch" src="images/search_button.png"></img></button>
			</form>
			<a href="search.php">Advanced search</a>
		</div>
		
		<!-- Login -->
		<?php			
			if($loggedin) {
		?>
				<a class="login" href="logout.php">Log out</a>
		<?php 
			}
			else{ 
		?>
				<a class="login 
				<?php 
					if(basename($_SERVER['PHP_SELF'])=="logsyst.php"){
						echo " active";
				}?>" href="logsyst.php">Log in</a>	
				<?php
			}
			?>
	</div>
</nav>