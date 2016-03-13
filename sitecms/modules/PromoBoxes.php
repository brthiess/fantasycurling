<?php

//Dashboard widget
if(SECTION_ID == 4){
	$total_promos = $CMSBuilder->get_record_count('promo_boxes');
	$CMSBuilder->set_widget(10, 'Total Promo Boxes', $total_promos);
}

if(SECTION_ID == 10){

	//Define vars
	$record_db = 'promo_boxes';
	$record_id = 'promo_id';
	
	$imagedir = "../images/promos/";
	$cropimages = array();
	$errors = false;
	$required = array();
	
	//Get promo boxes
	$promoboxes = array();
	$params = array();

	if($searchterm != ""){
		$params[] = '%' .$searchterm. '%';
	}
	$query = $db->query("SELECT * FROM `$record_db`" .($searchterm != "" ? " WHERE `title` LIKE ?" : ""). " ORDER BY `ordering`, `$record_id`", $params);
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){
			$promoboxes[$row[$record_id]] = $row;
		}
	}else{
		$CMSBuilder->set_system_alert('Unable to retrieve data. '.$db->error(), false);	
	}
	
	//Not found
	if(ACTION == 'edit'){
		if(!array_key_exists(ITEM_ID, $promoboxes)){
			$CMSBuilder->set_system_alert('Requested item was not found. Please select from the list below.', false);
			header('Location:' .PAGE_URL);
			exit();
		}else{
			$row = $promoboxes[ITEM_ID];	
		}
	}
	
	//Delete item
	if(isset($_POST['delete'])){
		$delete = $db->query("DELETE FROM `$record_db` WHERE `$record_id` = ?", array(ITEM_ID));
		if($delete && !$db->error()){
			if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
				unlink($imagedir.$_POST['old_image']);
			}
			$CMSBuilder->set_system_alert('Promo box was successfully deleted.', true);
		}else{
			$CMSBuilder->set_system_alert('Unable to delete record. ' .$db->error(),false);	
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
				$imageUpload->fit(600,600);
				$imageUpload->save($imagedir, $newname);
				if(file_exists($imagedir.$newname)){
					$image = $newname;
					if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
						unlink($imagedir.$_POST['old_image']);
					}
					array_push($cropimages, array('dir' => $imagedir, 'src' => $image, 'width' => 300, 'height' => 200, 'label' => 'Promo Box Image'));
				}
			}
	
			//Insert to db
			$params = array(
				ITEM_ID, 
				$_POST['title'], 
				$_POST['url'], 
				$_POST['url_target'], 
				$_POST['showhide'], 
				$_POST['ordering'], 
				$image, 
				$_POST['image_alt'], 
				$_POST['title'], 
				$_POST['url'], 
				$_POST['url_target'], 
				$_POST['showhide'], 
				$_POST['ordering'], 
				$image, 
				$_POST['image_alt']
			);
			$insert = $db->query("INSERT INTO `$record_db` (`$record_id`, `title`, `url`, `url_target`, `showhide`, `ordering`, `image`, `image_alt`) VALUES (?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE title = ?, url = ?, url_target = ?, showhide = ?, ordering = ?, image = ?, image_alt = ?", $params);
			if($insert && !$db->error()){
				if(count($cropimages) == 0){
					$CMSBuilder->set_system_alert('Promo box was successfully saved.', true);
					header("Location: " .PAGE_URL);
					exit();
				}
			}else{
				$CMSBuilder->set_system_alert('Unable to update record. '.$db->error(), false);
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
		$CMSBuilder->set_system_alert('Promo box was successfully saved.', true);
		header("Location: " .PAGE_URL);
		exit();
	}

}

?>