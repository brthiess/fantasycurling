<?php

//Dashboard widget
if(SECTION_ID == 4){
	$total_ctas = $CMSBuilder->get_record_count('pages_cta');
	$CMSBuilder->set_widget(11, 'Total Call To Actions', $total_ctas);
}

if(SECTION_ID == 11){

	//Define vars
	$imagedir = "../images/cta/";
	$cropimages = array();
	$errors = false;
	$required = array();
	
	//Get cta boxes
	$ctaboxes = array();
	$params = array();
	$_GET['search'] = $CMSBuilder->system_search(SECTION_ID);
	if(isset($_GET['search'])){
		$params[] = '%' .$_GET['search']. '%';
		$params[] = '%' .$_GET['search']. '%';
		$params[] = '%' .$_GET['search']. '%';
	}
	$query = $db->query("SELECT * FROM `pages_cta`" .(isset($_GET['search']) ? " WHERE `title` LIKE ? || `subtitle` LIKE ? || `url` LIKE ?" : ""). " ORDER BY `cta_id`", $params);
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){
			$ctaboxes[$row['cta_id']] = $row;
		}
	}else{
		$CMSBuilder->set_system_alert('Unable to retrieve data. '.$db->error(), false);	
	}
	
	//Not found
	if(ACTION == 'edit'){
		if(!array_key_exists(ITEM_ID, $ctaboxes)){
			$CMSBuilder->set_system_alert('Requested item was not found. Please select from the list below.', false);
			header('Location:' .PAGE_URL);
			exit();
		}else{
			$row = $ctaboxes[ITEM_ID];
		}
	}
	
	//Delete item
	if(isset($_POST['delete'])){
		
		//Delete from table, foreign key constraints will cascade
		$delete = $db->query("DELETE FROM `pages_cta` WHERE `cta_id` = ?", array(ITEM_ID));
		if($delete && !$db->error()){
			if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
				unlink($imagedir.$_POST['old_image']);
			}
			$CMSBuilder->set_system_alert('Call To Action was successfully deleted.', true);
		}else{
			$CMSBuilder->set_system_alert('Unable to delete record. ' .$db->error(), false);	
		}
		header("Location: " .PAGE_URL);
		exit();
	
	//Save item
	}else if(isset($_POST['save'])){
		
		//Validate
		if($_POST['title'] == ""){
			$errors[] = 'Please fill out all the <span class="required">*</span> required fields.';
			array_push($required, 'title');
		}
		if($_POST['old_image'] == '' && empty($_FILES['image']['name'])){
			$errors[] = 'Please select an image to upload.';
			array_push($required, 'image');
		}
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
				$imageUpload->fit(1920,1920);
				$imageUpload->save($imagedir, $newname);
				if(file_exists($imagedir.$newname)){
					$image = $newname;
					if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
						unlink($imagedir.$_POST['old_image']);
					}
					array_push($cropimages, array('dir' => $imagedir, 'src' => $image, 'width' => 1920, 'height' => 400, 'label' => $section['name'].' Image'));
				}
			}
	
			//Insert to db
			$params = array(
				ITEM_ID, 
				$_POST['title'], 
				$_POST['subtitle'], 
				$image, 
				$_POST['url'], 
				$_POST['url_target'], 
				$_POST['url_text'], 
				$_POST['showhide'],
				$_POST['title'], 
				$_POST['subtitle'], 
				$image, 
				$_POST['url'], 
				$_POST['url_target'], 
				$_POST['url_text'], 
				$_POST['showhide']
			);
			$insert = $db->query("INSERT INTO `pages_cta` (`cta_id`, `title`, `subtitle`, `image`, `url`, `url_target`, `url_text`, `showhide`) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE title = ?, subtitle = ?, image = ?, url = ?, url_target = ?, url_text = ?, showhide = ?", $params);
			if($insert && !$db->error()){
				if(count($cropimages) == 0){
					$CMSBuilder->set_system_alert('Call To Action was successfully saved.', true);
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
		$CMSBuilder->set_system_alert('Call To Action was successfully saved.', true);
		header("Location: " .PAGE_URL);
		exit();
	}

}

?>