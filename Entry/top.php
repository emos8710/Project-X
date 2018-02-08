<!-- Navigation bar and logo -->
	<nav class="navigation">
	
		<!-- Logo -->
		<div class="logo">
			<h1>UpStrain</h1>
			<p>The plasmid database for iGEM Uppsala</p>
		</div>
		
		<!-- Navigation buttons -->
		<a <?php if(basename($_SERVER['PHP_SELF'])=="index.php"){echo "class=\"active\" ";} ?> href="index.php">Home</a>
		<a <?php if(basename($_SERVER['PHP_SELF'])=="search.php"){echo "class=\"active\" ";} ?> href="search.php">Search</a>
		<a <?php if(basename($_SERVER['PHP_SELF'])=="insert.php"){echo "class=\"active\" ";} ?> href="insert.php">New entry</a>
		<a <?php if(basename($_SERVER['PHP_SELF'])=="help.php"){echo "class=\"active\" ";} ?> href="help.php">Help</a>
		<a class="login<?php if(basename($_SERVER['PHP_SELF'])=="login.php"){echo " active";} ?>" href="login.php">Log in</a>
		
		<!-- Quick search -->
		<div class="quicksearch">
			<form action="entry.php">
				<input type="text" placeholder="Search UpStrain ID" name="upstrain_id"></input>
				<button type="submit"><img class="quicksearch" src="images/search_button.png"></img></button>
			</form>
			<a href="search.php">Advanced search</a>
		</div>
	</nav>