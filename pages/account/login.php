<div class='login-logo'>
	<img src='<?php echo $path;?>images/logo-small.png'/>
</div>
<h1>Brier<span class='login-title'>King</span></h1>
<div class='login-container'>
	<h2>Login</h2>
	<form name="login_form" id="login_form" class='login-form-container' action="<?php echo $path; ?>" method="post">
		<fieldset class='login-form'>	
			<?php if(isset($login_alert)){ echo $login_alert; } ?>
			<input type="text" name="user_login" placeholder='email' class="input email" value="" /><br />
			<input type="password" name="user_password" class="input password" placeholder='password' value="" /><br />
		   
			<label class="label_sm hidden"><input type="checkbox" name="user_reme" class="checkbox" value="1" checked /> 
				<small>Remember Me</small>
			</label>
			<input type="submit" name="login" class="submit button" value="Login" />
		</fieldset>
	</form>

	<p class='forgot-container'>
		<a href="<?php echo $path; ?>pages/forgot.php?iframe=true&width=600&height=330" rel="prettyPhoto" class='forgot'>Forgot Password?</a> &nbsp; 
	</p>
</div>
<?php

$displayform = true;


//display form
if($displayform){
	echo (isset($alert) ? $alert : "");
?>

<div class='register-container'>

	<h2>Register</h2>
	<form name="register_form" id="register_form" class='register-form-container' action="" method="post">

		<fieldset class='register-form'>
	  
			<label>Email Address:</label>
			<input type="text" name="email" class="input"  /><br />    
		  
			<label>Team Name:</label>
			<input type="text" name="team_name" class="input"  /><br /> 
			
			<label>Password:</label>
			<input type="password" name="password" class="input"  /><br />
			
			<label>Re-Enter Password:</label>
			<input type="password" name="password2" class="input" /><br />   
			
			<input type="submit" name="register" class="submit button" value="Create Account" />
			<input type="hidden" name="xid" value="<?php echo $_COOKIE['xid']; ?>" />
		</fieldset>
	</form>
</div>

<?php } ?>