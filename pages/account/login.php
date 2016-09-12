<?php $page['no_footer'] = true;?>
<section class='login-register-page'>
	<div class='login-logo'>
		<img src='<?php echo $path;?>images/logo-small.png'/>
	</div>
	

	<div class='login-container hidden'>
		<h2>Sign In</h2>
		<form name="login_form" id="login_form" class='login-form-container' action="<?php echo $path; ?>" method="post">
			<fieldset class='login-form'>	
				<?php if(isset($login_alert)){ echo $login_alert; } ?>
				<label class='login-label'><img src='<?php echo $path;?>images/email-icon.png'/></label>
				<div class='input-container'>	
					<input type="email" name="user_login" placeholder='Email' class="input login-input"  />	
					<label class='input-label'>Email</label>
				</div>
				<label class='login-label'><img src='<?php echo $path;?>images/password-icon.png'/></label>
				<div class='input-container'>	
					<input type="password" name="user_password" placeholder='Password' class="input login-input"  />	
					<label class='input-label'>Password</label>
				</div>
			   
				<label class="label_sm hidden"><input type="checkbox" name="user_reme" class="checkbox" value="1" checked /> 
					<small>Remember Me</small>
				</label>
				<input type="submit" name="login" class="submit button rounded no-border blue" value="Login" />
			</fieldset>
		</form>

		<p class='forgot-container'>
			<a href="<?php echo $path; ?>pages/forgot.php?iframe=true&width=600&height=330" rel="prettyPhoto" class='white forgot'>Forgot Password?</a> &nbsp; 
		</p>
	</div>
	<?php

	$displayform = true;


	//display form
	if($displayform){
		echo (isset($alert) ? $alert : "");
	?>

	<div class='register-container'>

		<h2>Sign Up</h2>
		<form name="register_form" id="register_form" class='register-form-container' action="" method="post">

			<fieldset class='register-form'>
				
				<label class='login-label'><img src='<?php echo $path;?>images/email-icon.png'/></label>
				<div class='input-container'>	
					<input type="email" name="email" placeholder='Email' class="required input login-input"  />	
					<label class='input-label'>Email</label>
				</div>
				<label class='login-label'><img src='<?php echo $path;?>images/team-name-icon.png'/></label>
				<div class='input-container'>	
					<input type="text" name="team_name" placeholder='Team Name' class=" required input login-input"  />	
					<label class='input-label'>Team Name</label>
				</div>
				<label class='login-label'><img src='<?php echo $path;?>images/password-icon.png'/></label>
				<div class='input-container'>	
					<input type="password" name="password" placeholder='Password' class=" required input login-input"  />	
					<label class='input-label'>Password</label>
				</div>
			
				<input type='hidden' name='register'/>
				<input onclick='submitAndValidateForm(event, "#register_form");' type="submit" name="register" class="submit button rounded no-border blue" value="Create Account" />
				<input type="hidden" name="xid" value="<?php echo $_COOKIE['xid']; ?>" />
			</fieldset>
		</form>
		
	</div>



	<?php } 


	if(isset($_POST['register'])){

		//validate
		if($_POST['xid'] == $_COOKIE['xid']){
			
			//set parameters
			$params[] = array('param' => 'email', 'value' => $email, 'label' => 'Email Address', 'required' => true, 'unique' => true, 'validate' => 'email', 'hash' => false);
			$params[] = array('param' => 'password', 'value' => $password, 'label' => 'Password', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => true);		
			$params[] =  array('param' => 'team_name', 'value' => $team_name, 'label' => 'Team Name', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			
			//register
			try{
				$Account->register($params, array(), false, false, true); //<-- see class for documentation on these paramaters				
			//register error
			}catch(Exception $e){
				$alert = $Account->alert($e->getMessage(), false);
			}
		}else{
			$alert = $Account->alert('Unable to submit registration. Please try again.', false);	
		}
		
	}



	?>
</section>
<p class='faded toggle-container'>
			<span class='register-footer'>Already have an account? <button class='button-link white' onclick='toggleLogin();'>Sign in</button></span>
			<span class='login-footer hidden'>Don't have an account? <button class='button-link white' onclick='toggleLogin();'>Sign up</button></span>
		</p>

