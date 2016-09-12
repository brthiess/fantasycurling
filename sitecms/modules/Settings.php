<?php

if(SECTION_ID == 5){
	
	//Define vars
	$imagedir = "../images/banners/";
	$cropimages = array();
	$errors = false;
	$required = array();

	//Save changes
	if(isset($_POST['save'])){
		
		//Validate
		if($_POST['company_name'] == ""){
			$errors[] = 'Please fill out all the <span class="required">*</span> required fields.';
			array_push($required, 'company_name');
		}
		if(!checkmail($_POST['contact_email'])){
			$errors[] = 'Please enter a valid email address.';
			array_push($required, 'contact_email');
		}
		if(!isset($_POST['google_map'])){
			$_POST['google_map'] = 0;
		}
		if(!isset($_POST['show_hours'])){
			$_POST['show_hours'] = 0;
		}
		if(!$errors){
			
			//Upload image
			$image = ($_POST['old_image'] != '' ? $_POST['old_image'] : NULL);
			if(!empty($_FILES['image']['name'])){
				$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
				$newname = date("ymdhis").'.'.$ext;
				$imageUpload = new ImageUpload();
				$imageUpload->load($_FILES['image']['tmp_name']);
				$imageUpload->fit(1920,1920);
				$imageUpload->save($imagedir, $newname);
				if(file_exists($imagedir.$newname)){
					$image = $newname;
					if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
						unlink($imagedir.$_POST['old_image']);
					}
					array_push($cropimages, array('dir' => $imagedir, 'src' => $image, 'width' => 1920, 'height' => 500, 'label' => 'Page Banner Image'));
				}
			}else{
				if(isset($_POST['deleteimage']) && $_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
					unlink($imagedir.$_POST['old_image']);
					$image = "";
				}
			}
		
			//Start transaction
			$db->new_transaction();
			
			//Update global settings
			$params = array(
				$_POST['company_name'],
				$_POST['hotshots_deadline'],
				$_POST['disclaimer'],
				$image,
				$_POST['image_alt'],
				$_POST['contact_address'],
				$_POST['contact_address2'],
				$_POST['contact_city'],
				$_POST['contact_province'],
				$_POST['contact_postal_code'],
				$_POST['contact_country'],
				$_POST['contact_phone'],
				$_POST['contact_fax'],
				$_POST['contact_toll_free'],
				$_POST['contact_email'],
				$_POST['email_contactform'],
				$_POST['google_map'],
				$_POST['gpslat'],
				$_POST['gpslong'],
				$_POST['zoom'],
				($_POST['timezone'] != "" ? $_POST['timezone'] : NULL),
				$_POST['show_hours'],
				$_POST['meta_title'],
				$_POST['meta_description'],
				$_POST['hotshots_winner']
			);
			$query = $db->query("UPDATE `global_settings` SET `company_name` = ?, `hotshots_deadline` = ?, `disclaimer` = ?, `banner_image` = ?, `banner_image_alt` = ?, `contact_address` = ?, `contact_address2` = ?, `contact_city` = ?, `contact_province` = ?, `contact_postal_code` = ?, `contact_country` = ?, `contact_phone` = ?, `contact_fax` = ?, `contact_toll_free` = ?, `contact_email` = ?,`email_contactform` = ?, `google_map` = ?, `gpslat` = ?, `gpslong` = ?, `zoom` = ?, `timezone` = ?, `show_hours` = ?, `meta_title` = ?, `meta_description` = ?, hotshots_winner = ? WHERE id = 1", $params);
			
			//Update phone numbers
			if(isset($_POST['phone']) && is_array($_POST['phone'])){
				foreach($_POST['phone'] as $index=>$number){
					$id = $_POST['number_id'][$index];
					$tollfree = (isset($_POST['tollfree_'.$id]) ? $_POST['tollfree_'.$id] : 0);
					$hearingimpaired = (isset($_POST['hearingimpaired_'.$id]) ? $_POST['hearingimpaired_'.$id] : 0);
					
					$params = array($number, $tollfree, $hearingimpaired, $id);
					$query = $db->query("UPDATE `global_numbers` SET `phone` = ?, `tollfree` = ?, `hearingimpaired` = ? WHERE `number_id` = ?", $params);
				}
			}
		
			//Update social links
			foreach($global['global_social'] as $key=>$social){
				$query = $db->query("UPDATE `global_social` SET `url` = ? WHERE `id` = ?", array($_POST['social_'.$social['id']], $social['id']));
			}
		
			//Update global business hours
			for($i=0; $i<7; $i++){
				if(isset($_POST['hours_id'.$i])){
					$start_time = (isset($_POST['start_time'.$i]) ? $_POST['start_time'.$i] : 0);
					$end_time = (isset($_POST['end_time'.$i]) ? $_POST['end_time'.$i] : 0);
					$closed = (isset($_POST['closed'.$i]) ? $_POST['closed'.$i] : 0);
					
					$params = array($start_time, $end_time, $closed, $_POST['hours_id'.$i]);
					$query = $db->query("UPDATE `global_hours` SET `start_time` = ?, `end_time` = ?, `closed` = ? WHERE `hours_id` = ?", $params);
				}
			}
			
			//Commit transaction
			if(!$db->error()){
				$db->commit(); 
				
				if(count($cropimages) == 0){
					$CMSBuilder->set_system_alert('Global website settings updated!', true);
					header('Location: '.PAGE_URL);
					exit();
				}
			
			//Transaction error
			}else{
				$CMSBuilder->set_system_alert('Unable to update global settings. '.$db->error(), false);
			}
			
		}else{
			$CMSBuilder->set_system_alert(implode('<br />', $errors), false);	
		}
		
	//Crop images
	}else if(isset($_POST['crop'])){
		include("includes/jcropimages.php");
		$CMSBuilder->set_system_alert('Global website settings updated!', true);
		header("Location: " .PAGE_URL);
		exit();
	}
}

?>