<!--open login-wrapper-->
<div id="login-wrapper">

<!--open login-form-->
<div id="login-form" class="panel clearfix">
    <div class="panel-header"><h1>CMS<small>Content Management System</small></h1></div>
    <div class="panel-content">
        <form name="login-form" action="" method="post">
            <?php 
			$system_alert = $CMSBuilder->system_alert();
            echo (!is_null($system_alert) ? '<div id="login-alert"'.($system_alert[count($system_alert)-1]['status'] == true ? " class='success'": "").'>' .$system_alert[count($system_alert)-1]['message']. '</div>' : '');
            ?>
            <div class="form-field">
                <label>Username</label>
                <input type="text" name="user_login" class="input" tabindex="1" />
            </div>
            <div class="form-field">
                <label>Password <small class="f_right"><a href="<?php echo $path; ?>reset/">Forgot Password?</a></small></label>
                <input type="password" name="user_password" class="input" tabindex="2" />
            </div>
            <input type="checkbox" class="checkbox" name="user_reme" id="reme" value="1"<?php echo (!empty($_COOKIE['auth']['reme_id']) ? ' checked' : ''); ?> /> 
            <label for="reme" class="f_left"><small>Remember Me</small></label>
            
            <button type="submit" name="login" class="button f_right" tabindex="3"><i class="fa fa-lock"></i>Login</button>
            <input type="hidden" name="xssid" value="<?php echo $_COOKIE['xssid']; ?>" />
        </form>
    </div>
</div><!--close login-form-->
    
</div><!--close login-wrapper-->