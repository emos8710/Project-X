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

<body>
	
	<?php include 'top.php';
	
	if($admin) {
		?>
		
		<main>
			<div class="innertube">
				<h2>Control Panel</h2>
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