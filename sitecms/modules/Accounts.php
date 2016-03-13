<?php

if(SECTION_ID == [REPLACE_ME_WITH_CORRECT_ID]){

	//Define vars
	$imagedir = "../images/users/";
	$errors = false;
	$required = array();
	
	$allusers = array();
	$master = false;
	$params = array();
	
	//Search users
	$_GET['search'] = $CMSBuilder->system_search(SECTION_ID);
	
	if(SECTION_ID == [REPLACE_ME_WITH_CORRECT_ID] && isset($_GET['search'])){
		$params[] = '';
		$params[] = ' ';
		$params[] = '';
		$params[] = '%' .$_GET['search']. '%';
		$params[] = '%' .$_GET['search']. '%';
		$params[] = '%' .$_GET['search']. '%';
		$params[] = '%' .$_GET['search']. '%';
		$params[] = '';
		$params[] = ' ';
		$params[] = '';
		$params[] = '%' .$_GET['search']. '%';
		
		$query = $db->query("SELECT `accounts`.*, `account_profiles`.*, CONCAT(IFNULL(`account_profiles`.`first_name`, ?), ?, IFNULL(`account_profiles`.`last_name`, ?)) AS `fullname` FROM `accounts` LEFT JOIN `account_profiles` ON `accounts`.`account_id` = `account_profiles`.`account_id` LEFT JOIN `account_permissions` ON `accounts`.`account_id` = `account_permissions`.`account_id` LEFT JOIN `account_roles` ON `account_permissions`.`role_id` = `account_roles`.`role_id` WHERE (`accounts`.`account_id` LIKE ? OR `accounts`.`username` LIKE ? OR `accounts`.`email` LIKE ?  OR `accounts`.`status` LIKE ? OR CONCAT(IFNULL(`account_profiles`.`first_name`, ?), ?, IFNULL(`account_profiles`.`last_name`, ?)) LIKE ?) GROUP BY `accounts`.`account_id` ORDER BY `accounts`.`account_id`", $params);
	
	//Get users	
	}else{
		$query = $db->query("SELECT `accounts`.*, `account_profiles`.* FROM `accounts` LEFT JOIN `account_profiles` ON `accounts`.`account_id` = `account_profiles`.`account_id` GROUP BY `accounts`.`account_id` ORDER BY `accounts`.`account_id`");	
	}
	
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){	
			$row['master'] = $Account->account_has_role('Master', $row['account_id']);
			$row['roles'] = array_keys($Account->get_account_roles($row['account_id']));

			$allusers[$row['account_id']] = $row;
		}
	}else{
		$CMSBuilder->set_system_alert('Unable to retrieve data. '.$db->error(), false);	
	}
	
	//Not found
	if(ACTION == 'edit'){
		if(!array_key_exists(ITEM_ID, $allusers)){
			$CMSBuilder->set_system_alert('Requested item was not found. Please select from the list below.', false);
			header('Location:' .PAGE_URL);
			exit();
		}else{
			$row = $allusers[ITEM_ID];
		}
	}
	
	//Delete item
	if(isset($_POST['delete'])){
		
		//Cannot delete master accounts
		if($Account->account_has_role('Master', ITEM_ID)){
			$CMSBuilder->set_system_alert('Unable to delete record. Master accounts cannot be deleted.', false);
		
		}else{
		
			//Delete from table, foreign key constraints will cascade
			$delete = $db->query("DELETE FROM `accounts` WHERE `account_id` = ?", array(ITEM_ID));
			if($delete && !$db->error()){
				if($allusers[ITEM_ID]['photo'] != '' && file_exists($imagedir.$allusers[ITEM_ID]['photo'])){
					unlink($imagedir.$allusers[ITEM_ID]['photo']);
				}
				$CMSBuilder->set_system_alert('User was successfully deleted.', true);
			}else{
				$CMSBuilder->set_system_alert('Unable to delete record. ' .$db->error(),false);	
			}
			header("Location: " .PAGE_URL);
			exit();
			
		}
	
	//Save item
	}else if(isset($_POST['save'])){
				
		//Validate
		$required_fields[] = 'status';
		$required_fields[] = 'username';
		$required_fields[] = 'first_name';
		$required_fields[] = 'last_name';
		$required_fields[] = 'phone';
		$required_fields[] = 'address1';
		$required_fields[] = 'city';
		$required_fields[] = 'province';
		$required_fields[] = 'country';
		$required_fields[] = 'postalcode';
		if(ITEM_ID == ''){
			$required_fields[] = 'password';
			$required_fields[] = 'password2';
			$roles = array();
		} else {
			$roles = $allusers[ITEM_ID]['roles'];
		}
		foreach($required_fields as $field){
			if(trim($_POST[$field]) == ""){
				$errors[0] = 'Please fill out all the <span class="required">*</span> required fields.';
				array_push($required, $field);
			}
		}
		if(!checkmail($_POST['email'])){
			$errors[] = 'Please enter a valid email address.';
			array_push($required, 'email');
		}
		if($_POST['password'] != $_POST['password2']){
			$errors[] = 'Your passwords do not match. Please try again.';
			array_push($required, 'password');
			array_push($required, 'password2');
		}

		if(!$errors){			
			//Set params
			$params = array();
			$params[] = array('param' => 'status', 'value' => $_POST['status'], 'label' => 'Status', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'username', 'value' => $_POST['username'], 'label' => 'Username', 'required' => true, 'unique' => true, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'email', 'value' => $_POST['email'], 'label' => 'Email Address', 'required' => true, 'unique' => true, 'validate' => 'email', 'hash' => false);
			$params[] = array('param' => 'first_name', 'value' => $_POST['first_name'], 'label' => 'First Name', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'last_name', 'value' => $_POST['last_name'], 'label' => 'Last Name', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'company', 'value' => $_POST['company'], 'label' => 'Company Name', 'required' => false, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'phone', 'value' => $_POST['phone'], 'label' => 'Phone Number', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'fax', 'value' => $_POST['fax'], 'label' => 'Fax Number', 'required' => false, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'address1', 'value' => $_POST['address1'], 'label' => 'Street Address', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'address2', 'value' => $_POST['address2'], 'label' => 'Unit No.', 'required' => false, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'city', 'value' => $_POST['city'], 'label' => 'City/Town', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'province', 'value' => $_POST['province'], 'label' => 'Province/State', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'country', 'value' => $_POST['country'], 'label' => 'Country', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'postalcode', 'value' => $_POST['postalcode'], 'label' => 'Postal/Zip Code', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);

			//Create new account
			if(ITEM_ID == ""){
				$params[] = array('param' => 'password', 'value' => $_POST['password'], 'label' => 'Password', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => true);
				
				try{
					$account_id = $Account->register($params, $roles, true, false, false);
										
				}catch(Exception $e){
					$errors[] = 'Unable to update account. ' .$e->getMessage();
				}
			
			//Update account
			}else{
				try{
					$Account->update_profile($params, ITEM_ID);
					
					//If updating password
					if(trim($_POST['password']) != ''){
						try{
							$Account->reset_password(trim($_POST['password']), trim($_POST['password2']),ITEM_ID);
						}catch(Exception $e){
							$errors[] = 'Unable to update password. ' .$e->getMessage();
						}
					}
					
				}catch(Exception $e){
					$errors[] = 'Unable to update user. ' .$e->getMessage();
				}
			}

			if(!$errors) {
				$CMSBuilder->set_system_alert('User was successfully saved.', true);
				header("Location: " .PAGE_URL. (SECTION_ID == [REPLACE_ME_WITH_CORRECT_ID] ? "?action=edit&item_id=".ITEM_ID : ""));
				exit();
			}
			
		}
		
		//Errors occured
		if(is_array($errors) && count($errors) > 0){
			foreach($_POST as $key=>$data){
				$row[$key] = $data;
			}	
			$CMSBuilder->set_system_alert(implode('<br />', $errors), false);
		}

	}

}

?>