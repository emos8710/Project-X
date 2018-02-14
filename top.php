<?php
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
	$loggedin = TRUE;
} else {
	$loggedin = FALSE;
}

if(isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
	$admin = TRUE;
} else {
	$admin = FALSE;
}
?>

<!-- Navigation bar and logo -->

<nav class="navigation">

	<!-- Logo -->
	<div class="logo">
		<h1>UpStrain</h1>
		<p1>The plasmid database for iGEM Uppsala</p1>
	</div>
	
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
		<?php if($loggedin) {
			?>
			
			<a <?php
					if(preg_match("#^user.php?user_id=".$_SESSION['user_id']."#i", basename($_SERVER['REQUEST_URI'])) === 1) {
						echo "class=\"active\"";
					}
				?> href="user.php?user_id=<?php echo $_SESSION['user_id']; ?>">My profile</a>
			<?php
		}
		?>
		
		<!-- Control Panel (if admin) -->
		<?php if($admin) {
			?>
			
			<a <?php
					if(basename($_SERVER['PHP_SELF']) == "control_panel.php") {
						echo "class=\"active\"";
					}
				?> href="control_panel.php">Control Panel</a>
			<?php
		}
		?>	
			
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
	
	
	<!-- Quick search -->
	<div class="quicksearch">
		<form action="entry.php">
			<input type="text" placeholder="Search UpStrain ID" name="upstrain_id"></input>
			<button type="submit"><img class="quicksearch" src="images/search_button.png"></img></button>
		</form>
		<a href="search.php">Advanced search</a>
	</div>
</nav>