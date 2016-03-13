<?php

//User must be logged in
if(SECTION_ID != 1 && SECTION_ID != 2){
	if(!USER_LOGGED_IN){
		header('Location:' .$path. 'login/');
		exit();
		
	//Check user permissions
	}else{
		if(!$error404){
			if(!$CMSBuilder->check_permissions(SECTION_ID) && !$CMSBuilder->check_permissions(PARENT_ID) && !MASTER_USER){
				$CMSBuilder->set_system_alert('You do not have permission to access `' .$section['name']. '`.', false);
				header("Location:" .$path);
				exit();
			}
		}	
	}

//Login page
}else{
	
	//Check for activation code
	if(isset($_GET['activate']) && !empty($_GET['activate'])){
		
		//If already logged in another account
		if(USER_LOGGED_IN){
			try{
				$Account->logout();
				header('Location:' .$path. 'login/?activate='.$_GET['activate']);
				exit();
			}catch(Exception $e){
			}
		}
		
		//Activate new account
		$public_key = $_GET['activate'];
		try{
			$Account->activate_account($public_key);
			$CMSBuilder->set_system_alert('Account has been activated! Please log in with the credentials that were created for you. It is recommended to change your password as soon as you log in.', true);
		}catch(Exception $e){
			$CMSBuilder->set_system_alert($e->getMessage(), false);
		}
	}
	
	//Login new user
	if(isset($_POST['login'])){
		
		//If already logged in another account
		if(USER_LOGGED_IN){
			try{
				$Account->logout();
			}catch(Exception $e){
			}
		}
		
		$user_login = $_POST['user_login'];
		$user_password = $_POST['user_password'];
		$user_reme = (isset($_POST['user_reme']) ? $_POST['user_reme'] : '');
		$credentials = array(
			'unique_id' => array('param' => 'username', 'value' => $user_login),
			'password' => array('param' => 'password', 'value' => $user_password)
		);
		try{
			$Account->login($credentials, $user_reme);
			header('Location:' .$path);
			exit();
			
		}catch(Exception $e){
			$CMSBuilder->set_system_alert($e->getMessage(), false);	
		}
	}
	
	//Already logged in
	if(USER_LOGGED_IN){
		header('Location:' .$path);
		exit();
	}
}

?>