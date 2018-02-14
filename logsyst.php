<?php 
/* This is the login system page! */
require 'db.php';
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login/Registration System</title>
	<link href="css/logstyle.css" rel="stylesheet">
	<?php include 'top.php'; ?>
</head>

<body>
	<div class="form">
      
		<ul class="tab-group">
		<li class="tab active"><a href="#login">Log In</a></li>
		<li class="tab"><a href="#register">Register</a></li>
		</ul>
	  
<?php 
if($_SERVER['REQUEST_METHOD']=='POST'){ 	// Checks if the post method was used to access the page, post is set right after "Welcome to UpStrain" for example 
    if (isset($_POST['login'])) { 			// The login page is shown
        require 'login.php';   
    }
    elseif (isset($_POST['register'])) { 	// The registration form
        require 'register.php';   
    }
}
?>
      
	<div class="tab-content">
		<!-- Creating login page -->
		<div id="login"> 
		<h1>Welcome to UpStrain!</h1>
		
			<!-- Form is created, method is set to post, the id is login -->
			<form action="logsyst.php" method="post" autocomplete="off">
								
				<!-- Username field -->
				<div class="field-wrap"> 	
					<label> 
						Username <span class="req">*</span>
					</label>
					<input type="text" required autocomplete="on" name="username"/>
				</div>
								
				<!-- Password field -->
				<div class="field-wrap">
					<label> 
						Password <span class="req">*</span>
					</label>
					<input type="password" required autocomplete="off" name="password"/>
				</div>
          		  
				<!-- Link to Reset Password -->
				<p class="forgot"><a href="forgot.php">Reset password</a></p>
          		  
				<!-- Login button -->
				<button class="button button-block" name="login" />Log In</button>
          	</form>
        </div>
          
		  
		<!-- Creating registration page -->
        <div id="register">   
		<h1>Register a new account</h1>
			
			<!-- Form is created, method is set to post, the id is register -->
			<form action="logsyst.php" method="post" autocomplete="off">
				
				<!-- First name field -->
				<div class="top-row">
				<div class="field-wrap">
					<label> 
						First Name <span class="req">*</span>
					</label>
					<input type="text" required autocomplete="off" name='firstname' />
				</div>
        
				<!-- Last name field -->
				<div class="field-wrap">
					<label> 
						Last Name <span class="req">*</span>
					</label>
					<input type="text"required autocomplete="off" name='lastname' />
				</div>
				</div>

				<!-- Email address field -->
				<div class="field-wrap">
					<label>
						Email Address <span class="req">*</span>
					</label>
					<input type="email"required autocomplete="off" name='email' />
				</div>
				
				<!-- Phone number field -->
				<div class="field-wrap">
					<label>
						Phone number
					</label>
					<input type="number" autocomplete="off" name='phone' />
				</div>
				
				<!-- Username field -->
				<div class="field-wrap">
					<label>
						Username <span class="req">*</span>
					</label>
					<input type="text" required autocomplete="off" name='username' />
				</div>
				
				<!-- Password field -->
				<div class="top-row">
				<div class="field-wrap">
					<label>
						Password <span class="req">*</span>
					</label>
					<input type="password"required autocomplete="off" name='password'/>
				</div>
				
				<!-- Confirm password field -->
				<div class="field-wrap">
					<label>
						Confirm password <span class="req">*</span>
					</label>
					<input type="password"required autocomplete="off" name='confpassword'/>
				</div>
				</div> 
				<!-- Registration button -->
				<button type="submit" class="button button-block" name="register" />Register</button>
			
			</form>
				 
		</div>
      
	</div>
	<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>

    <script src="js/index.js"></script>
	
	

</body>
</html>
<?php include 'bottom.php'; ?>
