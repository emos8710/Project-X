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
					if(basename($_SERVER['PHP_SELF'])=="index.php"){
						echo "class=\"active\" ";
					} 
				?> href="index.php">Home</a>
				
			<!-- Search -->
			<a <?php 
					if(basename($_SERVER['PHP_SELF'])=="search.php"){
						echo "class=\"active\" ";
					} 
				?> href="search.php">Search</a>
			
			<!--  New Entry -->
			<a <?php 
					if(basename($_SERVER['PHP_SELF'])=="new_insert.php"){
						echo "class=\"active\" ";
					} 
				?> href="new_insert.php">New entry</a>
				
			<!-- Help -->
			<a <?php 
					if(basename($_SERVER['PHP_SELF'])=="help.php"){
						echo "class=\"active\" ";
					} 
				?> href="help.php">Help</a>
			
			<!-- Login -->
			<a class="login 
				<?php 
					if(basename($_SERVER['PHP_SELF'])=="logsyst.php"){
						echo " active";
					} 
				?>" href="logsyst.php">Log in</a>
		
		
		<!-- Quick search -->
		<div class="quicksearch">
			<form action="entry.php">
				<input type="text" placeholder="Search UpStrain ID" name="upstrain_id"></input>
				<button type="submit"><img class="quicksearch" src="images/search_button.png"></img></button>
			</form>
			<a href="search.php">Advanced search</a>
		</div>
	</nav>