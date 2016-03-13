<?php

//Dashboard widget
if(SECTION_ID == 4){
	$total_draws = $CMSBuilder->get_record_count('draws');
	$CMSBuilder->set_widget(15, 'Total Draws', $total_draws);
}

if(SECTION_ID == 15){


	$errors = false;
	$required = array();
	
	//Get cta boxes
	$draws = array();
	$params = array();
	$_GET['search'] = $CMSBuilder->system_search(SECTION_ID);
	$query = $db->query("SELECT * FROM `draws`" .(isset($_GET['search']) ? "" : ""). " ORDER BY ordering", $params);
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){
			$draws[$row['draw_id']] = $row;
		}
	}else{
		$CMSBuilder->set_system_alert('Unable to retrieve data. '.$db->error(), false);	
	}
	
	//Not found
	if(ACTION == 'edit'){
		if(!array_key_exists(ITEM_ID, $draws)){
			$CMSBuilder->set_system_alert('Requested item was not found. Please select from the list below.', false);
			header('Location:' .PAGE_URL);
			exit();
		}else{
			$row = $draws[ITEM_ID];
		}
	}
	
	//Delete item
	if(isset($_POST['delete'])){
		
		//Delete from table, foreign key constraints will cascade
		$delete = $db->query("DELETE FROM `draws` WHERE `draw_id` = ?", array(ITEM_ID));
		if($delete && !$db->error()){
			if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
				unlink($imagedir.$_POST['old_image']);
			}
			$CMSBuilder->set_system_alert('Draw was successfully deleted.', true);
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
			 
		
	
			//Insert to db
			$params = array(
				ITEM_ID, 
				$_POST['time'], 
				$_POST['date'], 
				$_POST['number'], 
				$_POST['ordering'], 
				$_POST['time'], 
				$_POST['date'], 
				$_POST['number'],				
				$_POST['ordering']			
			);
			$insert = $db->query("INSERT INTO `draws` (`draw_id`, `time`, `date`, `number`, `ordering`) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE `time` = ?, `date` = ?, `number` = ?, `ordering` = ?", $params);
			if($insert && !$db->error()){
				$CMSBuilder->set_system_alert('Draw was successfully saved.', true);
				header("Location: " .PAGE_URL);
				exit();
				
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
	}

}

?>