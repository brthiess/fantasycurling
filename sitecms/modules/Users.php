<?php

//Dashboard widget
if(SECTION_ID == 4){
	$total_accounts = $CMSBuilder->get_record_count('accounts');
	$CMSBuilder->set_widget(7, 'Total Users', $total_accounts);
}

if(SECTION_ID == 3 || SECTION_ID == 7){

	//Define vars
	$imagedir = "../images/users/";
	$cropimages = array();
	$errors = false;
	$required = array();
	
	$allusers = array();
	$roles = array();
	$permissions = array();
	$master = false;
	$params = array();
	
	//Current user access
	$fullaccess = false;
	if($CMSBuilder->check_permissions(7) || MASTER_USER){
		$fullaccess = true;
	}
	
	//Can only access self through account section
	if(SECTION_ID == 3 && (ACTION != 'edit' || ITEM_ID != USER_LOGGED_IN)){
		header('Location: '.PAGE_URL.'?action=edit&item_id='.USER_LOGGED_IN);
		exit();
	}
	
	//Search users
	$_GET['search'] = $CMSBuilder->system_search(SECTION_ID);
	
	if(SECTION_ID == 7 && isset($_GET['search'])){
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
			$row['roles'] = array_keys($Account->get_account_roles($row['account_id']));
			$row['permissions'] = array_keys($Account->get_account_permissions($row['account_id']));
			$row['master'] = $Account->account_has_role('Master', $row['account_id']);
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
				if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
					unlink($imagedir.$_POST['old_image']);
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
		
		//Can only update roles and permissions you have access
		if($fullaccess){
			
			//If updating a master account
			if($Account->account_has_role('Master', ITEM_ID)){
				$roles = array('Admin', 'Master');
			
			//Get roles and permissions
			}else{
				$roles = (isset($_POST['roles']) ? $_POST['roles'] : array());
				$roles[] = 'Admin'; //all cms users are admin accounts by default
				$permissions = (isset($_POST['permissions']) ? $_POST['permissions'] : array());
			}	
		}else{
			$roles = $allusers[ITEM_ID]['roles'];
			$permissions = $allusers[ITEM_ID]['permissions'];	
		}
		
		//Validate
		if(ITEM_ID == ''){
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
		if(!empty($_FILES['image']['size']) && $_FILES['image']['size'] > 20480000){
			$errors[] = 'Image filesize is too large.';
		}
	
		if(!$errors){
			
			//Upload image
			$image = ($_POST['old_image'] != '' ? $_POST['old_image'] : NULL);
			if(!empty($_FILES['image']['name'])){
				$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
				$newname = date("ymdhis").'.'.$ext;
				$imageUpload = new ImageUpload();
				$imageUpload->load($_FILES['image']['tmp_name']);
				$imageUpload->fit(300,300);
				$imageUpload->save($imagedir, $newname);
				if(file_exists($imagedir.$newname)){
					$image = $newname;
					if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
						unlink($imagedir.$_POST['old_image']);
					}
					array_push($cropimages, array('dir' => $imagedir, 'src' => $image, 'width' => 150, 'height' => 150, 'label' => 'Profile Image'));
				}
			}else if(isset($_POST['deleteimage'])){
				if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
					unlink($imagedir.$_POST['old_image']);
				}
				$image = NULL;
			}
			
			//Set params
			$params = array();
			$params[] = array('param' => 'team_name', 'value' => $_POST['team_name'], 'label' => 'Team Name', 'required' => true, 'unique' => true, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'email', 'value' => $_POST['email'], 'label' => 'Email Address', 'required' => true, 'unique' => true, 'validate' => 'email', 'hash' => false);
			$params[] = array('param' => 'first_name', 'value' => $_POST['first_name'], 'label' => 'First Name', 'required' => false, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'last_name', 'value' => $_POST['last_name'], 'label' => 'Last Name', 'required' => false, 'unique' => false, 'validate' => false, 'hash' => false);
			$params[] = array('param' => 'photo', 'value' => $image, 'label' => 'Profile Image', 'required' => false, 'unique' => false, 'validate' => false, 'hash' => false);
			
			//Create new account
			if(ITEM_ID == ""){
				$params[] = array('param' => 'password', 'value' => $_POST['password'], 'label' => 'Password', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => true);
				try{
					$account_id = $Account->register($params, $roles, true, false, false);
					
					//Insert account permissions
					$db->new_transaction();
					foreach($permissions as $perm){
						$insert = $db->query("INSERT INTO `cms_permissions`(`section_id`, `account_id`) VALUES(?,?)", array($perm, $account_id));
					}
					if(!$db->error()){
						$db->commit();
					}else{
						$errors[] = 'Unable to insert account permissions. ' .$db->error();	
					}
					
				}catch(Exception $e){
					$errors[] = 'Unable to update user. ' .$e->getMessage();
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
					
					//Update account roles
					try{
						$Account->update_account_roles($roles, ITEM_ID);
					}catch(Exception $e){
						$errors[] = 'Unable to update account roles. ' .$e->getMessage();
					}
					
					//Update account permissions
					$db->new_transaction();
					$delete = $db->query("DELETE FROM `cms_permissions` WHERE `account_id` = ?", array(ITEM_ID));
					foreach($permissions as $perm){
						$insert = $db->query("INSERT INTO `cms_permissions`(`section_id`, `account_id`) VALUES(?,?)", array($perm, ITEM_ID));
					}
					if(!$db->error()){
						$db->commit();
					}else{
						$errors[] = 'Unable to update account permissions. ' .$db->error();	
					}
					
				}catch(Exception $e){
					$errors[] = 'Unable to update user. ' .$e->getMessage();
				}
			}

			if(count($cropimages) == 0 && !$errors){
				$CMSBuilder->set_system_alert('User was successfully saved.', true);
				header("Location: " .PAGE_URL. (SECTION_ID == 3 ? "?action=edit&item_id=".ITEM_ID : ""));
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
	
	//Crop images
	}else if(isset($_POST['crop'])){
		include("includes/jcropimages.php");
		$CMSBuilder->set_system_alert('User was successfully saved.', true);
		header("Location: " .PAGE_URL. (SECTION_ID == 3 ? "?action=edit&item_id=".ITEM_ID : ""));
		exit();
	}

}

?>