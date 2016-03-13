<?php

//Dashboard widget
if(SECTION_ID == 4){
	$total_inquiries = $CMSBuilder->get_record_count('inquiries');
	$CMSBuilder->set_widget(20, 'Total Inquiries', $total_inquiries);
}

if(SECTION_ID == 20){
	
	//Get inquiries
	$inquiries = array();
	$params = array();

	if($searchterm != ""){
		$params[] = '%' .$searchterm. '%';
		$params[] = '%' .$searchterm. '%';
	}
	$query = $db->query("SELECT * FROM `inquiries`" .($searchterm != "" ? " WHERE `name` LIKE ? OR `email` LIKE ?" : ""). " ORDER BY `timestamp` DESC", $params);
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){
			$inquiries[$row['inquiry_id']] = $row;
		}
	}else{
		$CMSBuilder->set_system_alert('Unable to retrieve data. '.$db->error(), false);	
	}
	
	//Not found
	if(ACTION == 'edit'){
		if(!array_key_exists(ITEM_ID, $inquiries)){
			$CMSBuilder->set_system_alert('Requested item was not found. Please select from the list below.', false);
			header('Location:' .PAGE_URL);
			exit();
		}else{
			$row = $inquiries[ITEM_ID];
		}
	}
	
}
	
?>