<?php
	//System files
	include("../../includes/config.php");
	include("../../includes/database.php");
	include("../../includes/functions.php");
	include("../../includes/utils.php");
	
	
	include("../../modules/classes/Account.class.php");
	$Account = new Account();
	$user_loggedin = $Account->login_status();
	$data = array();
	$data['success'] = 'true';
	
	if($user_loggedin){
		$deadline = '';
		$pick_number = 1;
		foreach($_POST['hotshotss'] as $hotshot){			
			$query = $db->query("SELECT * FROM global_settings");
			if($query && !$db->error()){
				$result = $db->fetch_array();
				if(count($result) > 0){
					$deadline = $result[0]['hotshots_deadline'];
				}
			}
			if(strtotime($deadline) > strtotime("now")){
				$params = array(
					$Account->account_id,
					$pick_number,
					$hotshot['hotshot_id'],
					$hotshot['hotshot_id']
				);
				$insert = $db->query("INSERT INTO user_hotshots (account_id, pick_number, hotshot_id) VALUES(?,?,?) ON DUPLICATE KEY UPDATE hotshot_id = ?", $params);
			}
			$pick_number++;
		}
		
		foreach($_POST['gamess'] as $pick){		
			$query = $db->query("SELECT 		* FROM games g ".
								"INNER JOIN		draws d ON d.draw_id = g.draw_id ".
								"WHERE 			game_id = ?", array($pick['game_id']));
			if ($query && !$db->error()){
				$result = $db->fetch_array();
				$draw = $result[0];
			}
		
			if (strtotime($draw['date'] . " " . $draw['time']) > strtotime("now")) {
				$params = array(
					$Account->account_id,
					$pick['game_id'],
					$pick['team_id'],
					$pick['team_id']
				);
				
				$insert = $db->query("INSERT INTO user_picks (account_id, game_id, team_id) VALUES(?,?,?) ON DUPLICATE KEY UPDATE team_id = ?", $params);
				if($insert && !$db->error()){
				}
				else {
					$data['success'] = 'false';
				}
			}		
		}
	}
	echo json_encode($data);
?>