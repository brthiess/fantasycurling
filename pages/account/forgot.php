<?php

include("../includes/database.php");
include("../includes/functions.php");
include("../includes/config.php");
mysql_escape();

//initialize account
include("../modules/classes/Emogrifier.class.php");
include("../modules/classes/Account.class.php");
$Account = new Account();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Forgot Password | <?php echo $global['meta_title']; ?></title>

<meta name="description" content="<?php echo $global['meta_description']; ?>" />
<meta name="keywords" content="<?php echo $global['meta_keywords']; ?>" />

<!--stylesheets-->
<link rel="stylesheet" href="<?php echo $path; ?>css/global_stylesheet.css" />
<link rel="stylesheet" href="<?php echo $path; ?>css/stylesheet.css" />

</head>

<body class="iframe">
<div class="iframe_wrap">

	<h2>Forgot Password</h2>

	<p>Enter your email address below to have a password reset request emailed to you. Follow the instructions to reset your password.</p>
    
	<?php 
	$email = str_replace("'", "&rsquo;", stripslashes($_POST['email']));
    if(isset($_POST['submit'])){
		if(trim($email) != ""){
			try{
				$Account->forgot_password($email);
				echo $Account->alert('Password reset request has been sent.', true);
			}catch(Exception $e){
				echo $Account->alert($e->getMessage(), false);
			}
		}else{
			echo $Account->alert('Please enter your email address.', false);	
		}
    }
	?>
    
    <form action="" method="post" name="forgot_form" id="forgot_form">
    <fieldset>
    	<label class="label_sm">Email Address:</label>
    	<input class="submit f_right" type="submit" value="Submit" name="submit" />
        <input type="text" name="email" value="<?php echo $email; ?>" class="input f_right" />
    </fieldset>
    </form>
         
</div>
</body>
</html>