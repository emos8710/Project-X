<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
	session_start();
}

$title = "UpStrain";
?>

<!DOCTYPE html>

<?php include 'top.php'; ?>

<body>
	<!-- Main content goes here -->
	<main>
		<div class="innertube">
			<h1>TADAA</h1>
			<p>Här står det lite bra information om UpStrain och grejer. Och så kanske det är någon fin design. </p>
			<?php 
				if(basename($_SERVER['PHP_SELF'])=="logout.php"){ ?>
				<h1 class="loginss">You have been logged out!</h1>
				<?php 
				} ?>
				
		</div>
	</main>
	
	<?php include 'bottom.php'; ?>
	
</body>
</html>