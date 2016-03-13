<?php 

//reset request
if(!empty($_GET['reset'])){
	$public_key = $_GET['reset'];
	$account_id = $Account->validate_public_key($public_key);
	if($account_id){
		$displayform = true;
		$alert = "";
		
		if(isset($_POST['reset'])){
			if($_POST['xid'] == $_COOKIE['xid']){
			
				$new_password = str_replace("'", "&rsquo;", stripslashes($_POST['new_password']));
				$confirm_password = str_replace("'", "&rsquo;", stripslashes($_POST['confirm_password']));
				
				try{
					$Account->reset_password($new_password, $confirm_password, $account_id);
					$displayform = false;
					echo $Account->alert('Password has been updated.', true);
					echo '<p>Go to the <a href="' .$path. '">home page</a> or <a href="' .$path. 'login/">login</a>.</p>';
					
				}catch(Exception $e){
					$alert = $Account->alert($e->getMessage(), false);	
				}
			}else{
				echo $Account->alert('Unable to update password.', false);	
			}
		}
		
		if($displayform){
			echo '<form name="login_form" id="login_form" action="" method="post" class="f_left">
			<fieldset>' .$alert. '
				<label>New Password:</label><input type="password" name="new_password" class="input" value="" /><br />
				<label>Re-enter Password:</label><input type="password" name="confirm_password" class="input" value="" /><br />
				<input type="submit" name="reset" class="submit f_right" value="Reset" />
				<input type="hidden" name="xid" value="' .$_COOKIE['xid']. '" />
			</fieldset>
			</form>';
		}
				
	}else{
		echo $Account->alert('Password reset request has expired.', false);
		echo '<p>You must request a new <a href="' .$path. 'pages/forgot.php?iframe=true&width=600&height=330" rel="prettyPhoto">password reset</a>.</p>';
	}
	
//cancel request
}else if(!empty($_GET['cancel'])){
	$public_key = $_GET['cancel'];
	
	if($Account->validate_public_key($public_key)){
		if($Account->clear_public_key($public_key)){
			echo $Account->alert('Password reset request has been cancelled.', true);
			echo '<p>Go to the <a href="' .$path. '">home page</a>.</p>';
		}else{
			echo $Account->alert('Unable to cancel password reset request.', false);	
		}
	}else{
		echo $Account->alert('Password reset request has expired.', false);
		echo '<p>You must request a new <a href="' .$path. 'pages/forgot.php?iframe=true&width=600&height=330" rel="prettyPhoto">password reset</a>.</p>';		
	}

//empty public key
}else{
	echo $Account->alert('Unable to retrieve password reset information.', false);
	echo '<p>You must request a new <a href="' .$path. 'pages/forgot.php?iframe=true&width=600&height=330" rel="prettyPhoto">password reset</a>.</p>';
}

?>
