<!--open login-wrapper-->
<div id="login-wrapper">

<!--open login-form-->
<div id="login-form" class="panel clearfix">
    <div class="panel-header"><h1>CMS<small>Content Management System</small></h1></div>
    <div class="panel-content">
        <form name="login-form" action="" method="post" class='clearfix'>
            <?php 
			$system_alert = $CMSBuilder->system_alert();
            echo (!is_null($system_alert) ? '<div id="login-alert"'.($system_alert[count($system_alert)-1]['status'] == true ? " class='success'": "").'>' .$system_alert[count($system_alert)-1]['message']. '</div>' : '');
            ?>
            
            <?php if($displayform){ ?>
            
            <p><small>Enter your email address below to have a password reset request emailed to you. Follow the instructions to reset your password.</small></p>
            
            <div class="form-field">
                <label>Email Address</label>
                <input type="text" name="email" class="input" tabindex="1" />
            </div>
            
            <button type="submit" name="forgot" class="button f_right" tabindex="2"><i class="fa fa-key"></i>Reset Password</button>
            <input type="hidden" name="xssid" value="<?php echo $_COOKIE['xssid']; ?>" />
           
            <?php } else if(isset($_GET['reset'])){ ?>
	            
	        	<p>Please enter your new password below.</p>
	        	
	        	<div class="form-field">
	                <label>New Password</label>
	                <input type="password" name="new_password" class="input" value="" tabindex="1" />
	            </div>
	            
	            <div class="form-field">
	                <label>Re-enter Password</label>
	                <input type="password" name="confirm_password" class="input" value="" tabindex="2" />
	            </div>
	            
	            <button type="submit" name="reset" class="button f_right" tabindex="2"><i class="fa fa-key"></i>Reset Password</button>
				<input type="hidden" name="xssid" value="<?php echo $_COOKIE['xssid']; ?>" />
	        		            
            <?php } ?>
            
        </form>
        
        <p><br/><a href='<?php echo $path; ?>login/' class='f_right'><i class='fa fa-lock'></i> Back to Login</a></p>
        
    </div>
</div><!--close login-form-->
    
</div><!--close login-wrapper-->