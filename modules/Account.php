<?php

if($page['page_id'] == 9){
	
	//if not logged in
	if(!$user_loggedin){
		header('Location:' .$path. 'login/?redirect='.urlencode($_SERVER['REQUEST_URI']));
		exit();
	}
	
	//update profile
	if(isset($_POST['saveprofile'])){
		
		//xss protection
		if($_POST['xid'] == $_COOKIE['xid']){
		
			//sanitize form data
			foreach($_POST as $key=>$data){
				$_POST[$key] = str_replace("'", "&rsquo;", stripslashes($data));	
			}
		
			//set parameters
			$params[] = array('param' => 'email', 'label' => 'Email Address', 'value' => $_POST['email'], 'required' => true, 'unique' => true, 'validate' => 'email', 'hash' => false);
			$params[] =  array('param' => 'username', 'label' => 'Username', 'value' => $_POST['username'], 'required' => true, 'unique' => true, 'validate' => false, 'hash' => false);
			$params[] =  array('param' => 'first_name', 'label' => 'First Name', 'value' => $_POST['first_name'], 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] =  array('param' => 'last_name', 'label' => 'Last Name', 'value' => $_POST['last_name'], 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] =  array('param' => 'company', 'label' => 'Company', 'value' => $_POST['company'], 'required' => false, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] =  array('param' => 'address1', 'label' => 'Street Address', 'value' => $_POST['address1'], 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] =  array('param' => 'address2', 'label' => 'Address Line 2', 'value' => $_POST['address2'], 'required' => false, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] =  array('param' => 'city', 'label' => 'City', 'value' => $_POST['city'], 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] =  array('param' => 'province', 'label' => 'Province/State', 'value' => $_POST['province'], 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] =  array('param' => 'postalcode', 'label' => 'Postal/Zip Code', 'value' => $_POST['postalcode'], 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] =  array('param' => 'country', 'label' => 'Country', 'value' => $_POST['country'], 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] =  array('param' => 'phone', 'label' => 'Phone Number', 'value' => $_POST['phone1'].$_POST['phone2'].$_POST['phone3'], 'required' => true, 'unique' => false, 'validate' => 'phone', 'hash' => false);
		
			//update account
			try{
				$Account->update_profile($params, $user_loggedin);
				$account_alert = $Account->alert('Profile was successfully updated.', true);
			}catch(Exception $e){
				$account_alert = $Account->alert($e->getMessage(), false);
			}
			
		}else{
			$account_alert = $Account->alert('Unable to validate session. Please logout and try again.', true);	
		}
	}
	
	//change password
	if(isset($_POST['savepassword'])){
		
		//xss protection
		if($_POST['xid'] == $_COOKIE['xid']){
		
			//sanitize form data
			foreach($_POST as $key=>$data){
				$_POST[$key] = str_replace("'", "&rsquo;", stripslashes($data));	
			}
			
			//save password
			try{
				$Account->change_password($_POST['old_password'], $_POST['new_password'], $_POST['confirm_password'], $user_loggedin);
				$account_alert = $Account->alert('Password was successfully changed.', true);
			}catch(Exception $e){
				$account_alert = $Account->alert($e->getMessage(), false);
			}
			
		}else{
			$account_alert = $Account->alert('Unable to validate session. Please logout and try again.', true);		
		}
	}
	
}

?>