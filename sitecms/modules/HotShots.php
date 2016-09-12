<?php

//Dashboard widget
if(SECTION_ID == 4){
	$total_teams = $CMSBuilder->get_record_count('teams');
	$CMSBuilder->set_widget(13, 'Total teams', $total_teams);
}

if(SECTION_ID == 17){

	$imagedir = "../images/teams/";
	$cropimages = array();
	$errors = false;
	$required = array();
	
	//Get cta boxes
	$teams = array();
	$params = array();
	$_GET['search'] = $CMSBuilder->system_search(SECTION_ID);
	$query = $db->query("SELECT * FROM `hotshots`" .(isset($_GET['search']) ? "" : ""). " ORDER BY `hotshot_id`", $params);
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){
			$teams[$row['hotshot_id']] = $row;
		}
	}else{
		$CMSBuilder->set_system_alert('Unable to retrieve data. '.$db->error(), false);	
	}
	
	//Not found
	if(ACTION == 'edit'){
		if(!array_key_exists(ITEM_ID, $teams)){
			$CMSBuilder->set_system_alert('Requested item was not found. Please select from the list below.', false);
			header('Location:' .PAGE_URL);
			exit();
		}else{
			$row = $teams[ITEM_ID];
		}
	}
	
	//Delete item
	if(isset($_POST['delete'])){
		
		//Delete from table, foreign key constraints will cascade
		$delete = $db->query("DELETE FROM `teams` WHERE `team_id` = ?", array(ITEM_ID));
		if($delete && !$db->error()){
			if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
				unlink($imagedir.$_POST['old_image']);
			}
			$CMSBuilder->set_system_alert('team was successfully deleted.', true);
		}else{
			$CMSBuilder->set_system_alert('Unable to delete record. ' .$db->error(), false);	
		}
		header("Location: " .PAGE_URL);
		exit();
	
	//Save item
	}else if(isset($_POST['save'])){
		
		//Validate
		if(!empty($_FILES['image']['size']) && $_FILES['image']['size'] > 20480000){
			$errors[] = 'Image filesize is too large.';
		}
		if(!isset($_POST['showhide'])){
			$_POST['showhide'] = 1;
		}
	
		if(!$errors){
			 
			 
			 //Upload image
			$image = ($_POST['old_image'] != '' ? $_POST['old_image'] : NULL);
			if(!empty($_FILES['image']['name'])){
				$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
				$newname = date("ymdhis").'.'.$ext;
				$imageUpload = new ImageUpload();
				$imageUpload->load($_FILES['image']['tmp_name']);
				$imageUpload->fit(400,400);
				$imageUpload->save($imagedir, $newname);
				if(file_exists($imagedir.$newname)){
					$image = $newname;
					if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
						unlink($imagedir.$_POST['old_image']);
					}
				}
			}
		
	
			//Insert to db
			$params = array(
				ITEM_ID, 
				$_POST['name'], 
				$_POST['province'], 
				$_POST['name'], 
				$_POST['province']				
			);
			$insert = $db->query("INSERT INTO `hotshots` (`hotshot_id`, `name`, `province`) VALUES (?,?,?) ON DUPLICATE KEY UPDATE `name` = ?, `province` = ?", $params);
			if($insert && !$db->error()){
				if(count($cropimages) == 0){
					$CMSBuilder->set_system_alert('Hotshot was successfully saved.', true);
					header("Location: " .PAGE_URL);
					exit();
				}
				
			}else{
				$CMSBuilder->set_system_alert('Unable to update record. '.$db->error(), true);
			}
	
		}else{
			$CMSBuilder->set_system_alert(implode('<br />', $errors), false);
			foreach($_POST AS $key=>$data){
				$row[$key] = $data;
			}	
		}
	
	//Crop images
	}else if(isset($_POST['crop'])){
		include("includes/jcropimages.php");
		$CMSBuilder->set_system_alert('Team was successfully saved.', true);
		header("Location: " .PAGE_URL);
		exit();
	}

}

?>