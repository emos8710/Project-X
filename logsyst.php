<?php
/* This is the login system page! */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$title = "Login/Registration System";

include 'top.php';
?>
<main>
    <div class="form">
        <div class="tab-content">
            <!-- Creating login page -->
            <div id="login"> 
                <h1 class="login">Welcome to UpStrain!</h1>
                <p class="tab"><a class="login" href="#register">New? Click here to register!</a></p>

                <!-- Form is created, method is set to post, the id is login -->
                <form action="login.php" method="post" autocomplete="off">

                    <!-- Username field -->
                    <div class="field-wrap"> 	
                        <label> 
                            Username <span class="req">*</span>
                        </label>
                        <input class="login" type="text" required autocomplete="on" name="username"/>
                    </div>

                    <!-- Password field -->
                    <div class="field-wrap">
                        <label> 
                            Password <span class="req">*</span>
                        </label>
                        <input class="login" type="password" required autocomplete="off" name="password"/>
                    </div>

                    <!-- Link to Reset Password -->
                    <a class="login" id="right" href="forgot.php">Reset password</a>

                    <!-- Login button -->
                    <button class="button" name="login" />Log In</button>
                </form>
            </div>


            <!-- Creating registration page -->
            <div id="register">   
                <h1 class="login">Register a new account</h1>
                <p class="tab"><a class="login" href="#login">Back to log in</a></p>

                <!-- Form is created, method is set to post, the id is register -->
                <form action="register.php" method="post" autocomplete="off">

                    <!-- First name field -->
                    <div class="top-row">
                        <div class="field-wrap">
                            <label> 
                                First Name <span class="req">*</span>
                            </label>
                            <input class="login" type="text" required autocomplete="off" name='firstname' />
                        </div>

                        <!-- Last name field -->
                        <div class="field-wrap">
                            <label> 
                                Last Name <span class="req">*</span>
                            </label>
                            <input class="login" type="text"required autocomplete="off" name='lastname' />
                        </div>
                    </div>

                    <!-- Email address field -->
                    <div class="field-wrap">
                        <label>
                            Email Address <span class="req">*</span>
                        </label>
                        <input class="login" type="email"required autocomplete="off" name='email' />
                    </div>

                    <!-- Phone number field -->
                    <div class="field-wrap">
                        <label>
                            Phone number
                        </label>
                        <input class="login" type="number" autocomplete="off" name='phone' />
                    </div>

                    <!-- Username field -->
                    <div class="field-wrap">
                        <label>
                            Username <span class="req">*</span>
                        </label>
                        <input class="login" pattern=".{3,50}" type="text" required autocomplete="off" name='username' required title="The username must be between 3-50 characters"/>
                    </div>

                    <!-- Password field -->
                    <div class="top-row">
                        <div class="field-wrap">
                            <label>
                                Password <span class="req">*</span>
                            </label>
                            <input class="login" pattern=".{8,}" type="password" required autocomplete="off" name='password' required title="The password must be at least 8 characters"/>
                        </div>

                        <!-- Confirm password field -->
                        <div class="field-wrap">
                            <label>
                                Confirm password <span class="req">*</span>
                            </label>
                            <input class="login" pattern=".{8,}" type="password" required autocomplete="off" name='confpassword' required title="The password must be at least 8 characters"/>
                        </div>
                    </div> 

                    <div class="field-wrap">
                        <div class="g-recaptcha" data-sitekey="6LfhRkoUAAAAAP2LpjbADUpLBMI1mkwFtAn8vbJn">
                        </div>
                    </div>

                    <!-- Registration button -->
                    <button type="submit" class="button" name="register" />Register</button>

                </form> 
            </div>
        </div>
    </div>
    <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
    <script src="js/index.js"></script>
</main>
<?php include 'bottom.php'; ?>
