<?php
if (session_status() == PHP_SESSION_DISABLED || session_status() == PHP_SESSION_NONE) {
	session_start();
}
?>

<!DOCTYPE html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>UpStrain</title>
	<link href="css/upstrain.css" rel="stylesheet">
</head>

<body>
	
	<?php include 'top.php'; ?>
	
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