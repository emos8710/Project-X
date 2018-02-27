<?php
/* Displays all error messages */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$title = "Login Error";
?>
<!DOCTYPE html>

<?php include 'top.php'; ?>

<body>
	<main>
		<div class="form">
			<h1 class="login">Error</h1>
			<p class="login">
				<?php 
				if( isset($_SESSION['message']) AND !empty($_SESSION['message']) ): 
					echo $_SESSION['message'];    
				else:
					header( "location: logsyst.php" );
				endif;
				?>
			</p>     
		</div>
	</main>
	<?php include 'bottom.php'; ?>
</body>
</html>
