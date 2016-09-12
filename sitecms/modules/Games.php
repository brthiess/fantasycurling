<?php

//Dashboard widget
if(SECTION_ID == 4){
	$total_games = $CMSBuilder->get_record_count('games');
	$CMSBuilder->set_widget(16, 'Total games', $total_games);
}

if(SECTION_ID == 16){


	$errors = false;
	$required = array();
	
	//Get cta boxes
	$games = array();
	$params = array();
	$_GET['search'] = $CMSBuilder->system_search(SECTION_ID);
	$query = $db->query("SELECT g.*, t1.team_name AS team1_name, t2.team_name AS team2_name, t3.team_name AS winner_name, d.time AS draw_time, d.number AS draw_number FROM `games` g LEFT JOIN teams t1 ON t1.team_id = g.team1_id LEFT JOIN teams t2 ON t2.team_id = g.team2_id LEFT JOIN teams t3 ON t3.team_id = g.winner_id LEFT JOIN draws d ON d.draw_id = g.draw_id ORDER BY d.ordering, `game_id`", $params);
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){
			$games[$row['game_id']] = $row;
		}
	}else{
		$CMSBuilder->set_system_alert('Unable to retrieve data. '.$db->error(), false);	
	}
	
	//Not found
	if(ACTION == 'edit'){
		if(!array_key_exists(ITEM_ID, $games)){
			$CMSBuilder->set_system_alert('Requested item was not found. Please select from the list below.', false);
			header('Location:' .PAGE_URL);
			exit();
		}else{
			$row = $games[ITEM_ID];
		}
	}
	
	//Delete item
	if(isset($_POST['delete'])){
		
		//Delete from table, foreign key constraints will cascade
		$delete = $db->query("DELETE FROM `games` WHERE `game_id` = ?", array(ITEM_ID));
		if($delete && !$db->error()){
			if($_POST['old_image'] != '' && file_exists($imagedir.$_POST['old_image'])){
				unlink($imagedir.$_POST['old_image']);
			}
			$CMSBuilder->set_system_alert('game was successfully deleted.', true);
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
				$_POST['draw_id'], 
				$_POST['team1_id'], 
				$_POST['team2_id'], 
				$_POST['winner_id'], 
				$_POST['draw_id'], 
				$_POST['team1_id'], 
				$_POST['team2_id'], 
				$_POST['winner_id']				
			);
			$insert = $db->query("INSERT INTO `games` (`game_id`, `draw_id`, `team1_id`, `team2_id`, `winner_id`) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE `draw_id` = ?, `team1_id` = ?, `team2_id` = ?, `winner_id` = ?", $params);
			if($insert && !$db->error()){
				$CMSBuilder->set_system_alert('game was successfully saved.', true);
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