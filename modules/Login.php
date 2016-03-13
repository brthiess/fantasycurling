<?php

if ($page['page_id'] == 3){
	
	//check for activation code
	if(!empty($_GET['activate'])){
		$public_key = $_GET['activate'];
		try{
			$Account->activate_account($public_key);
			$login_alert = $Account->alert('Account has been activated and you can login!', true);
		}catch(Exception $e){
			$login_alert = $Account->alert($e->getMessage(), false);
		}
	}

	//check if already logged in
	if($user_loggedin){
				
	}else{

		//login new user
		if(isset($_POST['login'])){
			$user_login = str_replace("'", "&rsquo;", stripslashes($_POST['user_login']));
			$user_password = str_replace("'", "&rsquo;", stripslashes($_POST['user_password']));
			$user_reme = $_POST['user_reme'];
			$credentials = array(
				'unique_id' => array('param' => 'email', 'value' => $user_login),
				'password' => array('param' => 'password', 'value' => $user_password)
			);
			try{
				$Account->login($credentials, $user_reme);
				$Account = new Account();
				$user_loggedin = $Account->login_status();
				if(isset($_GET['redirect'])){
					header('Location:'.urldecode($_GET['redirect']));
				}else{
					header('Location:' .$path);
				}
				
			}catch(Exception $e){
				$login_alert = $Account->alert($e->getMessage(), false);
			}
		}
		
		if(isset($_POST['register'])){
			$email = str_replace("'", "&rsquo;", stripslashes($_POST['email']));
			$password = str_replace("'", "&rsquo;", stripslashes($_POST['password']));
			$password2 = str_replace("'", "&rsquo;", stripslashes($_POST['password2']));
			$team_name = str_replace("'", "&rsquo;", stripslashes($_POST['team_name']));

			//validate
			if($_POST['xid'] == $_COOKIE['xid'] && $password == $password2){
				
				//set parameters
				$params[] = array('param' => 'email', 'value' => $email, 'label' => 'Email Address', 'required' => true, 'unique' => true, 'validate' => 'email', 'hash' => false);
				$params[] = array('param' => 'password', 'value' => $password, 'label' => 'Password', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => true);
				
				$params[] = array('param' => 'team_name', 'value' => $team_name, 'label' => 'Team Name', 'required' => true, 'unique' => true, 'validate' => false, 'hash' => false);
				$params[] = array('param' => 'photo', 'value' => 'default.jpg', 'label' => 'Photo', 'required' => false, 'unique' => false, 'validate' => false, 'hash' => false);
				
				
			
				//register
				try{
					$registered = $Account->register($params, array(), false, false, true); //<-- see class for documentation on these paramaters
					
					
					if($registered){
						header('Location:'. $path);
					}
					
				
				//register error
				}catch(Exception $e){
					$alert = $Account->alert($e->getMessage(), false);
				}
				
			}else if($password != $password2){
				$alert = $Account->alert('Your passwords do not match. Please try again.', false);
			}else{
				$alert = $Account->alert('Unable to submit registration. Please try again.', false);	
			}
			
		}
	}
}

?>