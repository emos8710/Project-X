<main>
	<div class="innertube">
	
	<?php
	if($isadmin) {
		?>

		<?php
		include 'scripts/db.php';
		
		//do some database shit
		
		mysqli_close($link);
		?>
		
		<!-- do some edit form shit -->
		<p>
		In the future, there will be an amazing form for editing entries here.
		<br>
		It will be the best form you've ever seen.
		</p>
		
		<?php
		
		//handle some form return shit
		
		?>
		
		<br>
		<p>
		Down here, there will be some sublime messages that will give you an absolute confidence
		<br>
		in whether your edit was amazingly successful, or a total mess. 
		<br>
		It will be <strong><u>glorious</u></strong>.
		</p>
	
		<?php
		} else {
		
			?>
			<h3 style="color:red">You are not allowed to edit entries (you are not an admin).</h3>
			<br>
			<a href="entry.php?upstrain_id=<?php echo "$upstrain_id" ?> ">Go back to entry page</a>
			<?php
			
		}
		?>
		
	</div>
</main>