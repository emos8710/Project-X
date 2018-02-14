			<?php
			if($admin || $isuser) {
				?>

				<?php
				include 'scripts/db.php';
				
				//do some database shit
				
				mysqli_close($link);
				?>
				
				<!-- do some edit form shit -->
				<p>
				In the future, there will be an amazing form for editing user info here.
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
			<h3 style="color:red">You are not allowed to edit this profile (you are not the owner or an admin).</h3>
			<br>
			<a href="entry.php?upstrain_id=<?php echo "$user_id" ?> ">Go back to the user page</a>
			<?php
			}
			?>