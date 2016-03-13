<?php  

//Dashboard widget
if(SECTION_ID == 4) {
	$total_slides = $CMSBuilder->get_record_count('slideshow');
	$CMSBuilder->set_widget(12, 'Total Slides', $total_slides);
}

if(SECTION_ID == 12) {
	// Define vars
	$record_db = 'slideshow';
	$record_id = 'slide_id';
	$record_name = 'Slide';

	$imagedir = "../images/slides/";
	$cropimages = array();
	$errors = false;
	$required = array();
	$required_fields = array('title' => 'Title'); // for validation

	// Get Records
	$records_arr = array();
	$params = array();

	if($searchterm != ""){
		$params[] = '%' .$searchterm. '%';
		$params[] = '%' .$searchterm. '%';
	}
	$query = $db->query("SELECT * FROM `$record_db`" .($searchterm != "" ? " WHERE `title` LIKE ? OR `content` LIKE ?" : ""). " ORDER BY `ordering`, `$record_id`", $params);
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){
			$records_arr[$row[$record_id]] = $row;
		}
	}else{
		$CMSBuilder->set_system_alert('Unable to retrieve data. '.$db->error(), false);	
	}

	//Not found
	if(ACTION == 'edit'){
		if(!array_key_exists(ITEM_ID, $records_arr)){
			$CMSBuilder->set_system_alert('Requested item was not found. Please select from the list below.', false);
			header('Location:' .PAGE_URL);
			exit();
		}else{
			$row = $records_arr[ITEM_ID];	
		}
	}

	//Delete item
	if(isset($_POST['delete'])){
		
		//Multiple queries so utilize transactions
		$db->new_transaction();
		$delete = $db->query("DELETE FROM `$record_db` WHERE `$record_id` = ?", array(ITEM_ID));
		if(!$db->error()){
			$db->commit(); 
			if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
				unlink($imagedir.$_POST['old_image']);
			}
			$CMSBuilder->set_system_alert($record_name.' was successfully deleted.', true);
		}else{
			$CMSBuilder->set_system_alert('Unable to delete record. ' .$db->error(),false);	
		}
		header("Location: " .PAGE_URL);
		exit();
	
	//Save item
	} else if(isset($_POST['save'])){

		// Validate
		$required_missing = false;
		if(!empty($required_fields)) {
			foreach($required_fields as $field_key => $field_name) {
				if(isset($_POST[$field_key])) {
					if(trim($_POST[$field_key]) == '') {
						$required_missing = true;
						array_push($required, $field_key);
					}
				} else {
					$required_missing = true;
					array_push($required, $field_key);
				}
			}
		}
		if($required_missing) {
			$errors[] = 'Please fill out all the <span class="required">*</span> required fields.';
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

		if(!$errors) {
			// Upload Image
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
					array_push($cropimages, array('dir' => $imagedir, 'src' => $image, 'width' => 1920, 'height' => 500, 'label' => 'Slide'));
				}
			}

			//Insert to db
			$params = array(
				ITEM_ID, 
				$_POST['title'], 
				$_POST['content'], 
				$image, 
				$_POST['image_alt'], 
				$_POST['url'], 
				$_POST['url_target'], 
				$_POST['ordering'], 
				$_POST['showhide'], 
				$_POST['title'], 
				$_POST['content'], 
				$image, 
				$_POST['image_alt'], 
				$_POST['url'], 
				$_POST['url_target'], 
				$_POST['ordering'], 
				$_POST['showhide']
			);
			$insert = $db->query("INSERT INTO `$record_db` (`$record_id`, `title`, `content`, `image`, `image_alt`, `url`, `url_target`, `ordering`, `showhide`) VALUES (?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE title = ?, content = ?, image = ?, image_alt = ?, url = ?, url_target = ?, ordering = ?, showhide = ?", $params);
			if($insert && !$db->error()){
				if(count($cropimages) == 0){
					$CMSBuilder->set_system_alert($record_name.' was successfully saved.', true);
					header("Location: " .PAGE_URL);
					exit();
				}
			} else {
				$CMSBuilder->set_system_alert('Unable to update record. '.$db->error(), true);
			}

		} else{
			$CMSBuilder->set_system_alert(implode('<br />', $errors), false);
			foreach($_POST AS $key=>$data){
				$row[$key] = $data;
			}	
		}

	} else if(isset($_POST['crop'])){
		include("includes/jcropimages.php");
		$CMSBuilder->set_system_alert($record_name.' was successfully saved.', true);
		header("Location: " .PAGE_URL);
		exit();
	}
}

?>