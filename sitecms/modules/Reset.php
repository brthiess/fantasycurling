<?php

if(SECTION_ID == 2){
		
	$displayform = true;
	
	//Send password reset request
	if(isset($_POST['forgot'])){
		$email = $_POST['email'];
		if(trim($email) != ""){
			try{
				$Account->forgot_password($email,ltrim($path,'/').'includes/emailtemplates/forgotpassword.htm', ltrim($path,'/').'css/base.css');
				$CMSBuilder->set_system_alert('Password reset request has been sent.', true);
				header('Location:' .$path. 'login/');
				exit();
			}catch(Exception $e){
				$CMSBuilder->set_system_alert($e->getMessage(), false);	
			}
		}else{
			$CMSBuilder->set_system_alert('Please enter your email address.', false);	
		}
	}
	
	//reset request
	if(!empty($_GET['reset'])){
		$public_key = $_GET['reset'];
		$account_id = $Account->validate_public_key($public_key);
		if($account_id){
			$displayform = false;
			
			if(isset($_POST['reset'])){
				if($_POST['xssid'] == $_COOKIE['xssid']){
				
					$new_password = $_POST['new_password'];
					$confirm_password = $_POST['confirm_password'];
					
					try{
						$Account->reset_password($new_password, $confirm_password, $account_id);
						$CMSBuilder->set_system_alert('Password has been updated. Please login below.', true);
						header('Location:' .$path. 'login/');
						exit();
					}catch(Exception $e){
						$CMSBuilder->set_system_alert($e->getMessage(), false);
					}
				}else{
					$CMSBuilder->set_system_alert('Unable to update password.', false);
				}
			}			
					
		}else{
			$CMSBuilder->set_system_alert('Password reset request has expired. You must request a new password reset below.', false);
		}
		
	//cancel request
	}else if(!empty($_GET['cancel'])){
		$public_key = $_GET['cancel'];
		
		if($Account->validate_public_key($public_key)){
			if($Account->clear_public_key($public_key)){
				$CMSBuilder->set_system_alert('Password reset request has been cancelled.', true);
				header('Location:' .$path. 'login/');
				exit();
			}else{
				$CMSBuilder->set_system_alert('Unable to cancel password reset request.', false);
			}
		}else{
			$CMSBuilder->set_system_alert('Password reset request has expired. You must request a new password reset below.', false);
			$expired = true;		
		}
	
	}
		
}

?>