<?php
//System files
include("../../includes/config.php");
include("../../includes/database.php");
include("../../includes/functions.php");
include("../../includes/utils.php");


include("../../modules/classes/Account.class.php");
$Account = new Account();
$user_loggedin = $Account->login_status();

$query = $db->query("SELECT 		* FROM games g ".
					"INNER JOIN		draws d ON d.draw_id = g.draw_id ".
					"WHERE 			game_id = ?", array($_POST['game_id']));
if ($query && !$db->error()){
	$result = $db->fetch_array();
	$draw = $result[0];
}
$data = array();
if (strtotime($draw['date'] . " " . $draw['time']) > strtotime("now")) {
	$params = array(
		$Account->account_id,
		$_POST['game_id'],
		$_POST['team_id'],
		$_POST['team_id']
	);
	$query = $db->query("INSERT INTO user_picks (account_id, game_id, team_id) VALUES(?,?,?) ON DUPLICATE KEY UPDATE team_id = ?", $params);
	if ($query && !$db->error()){
		$data['success'] = true;
	}
}
else {
	$data['success'] = false;
	$data['reason'] = 'Past Deadline';
}

echo json_encode($data);
?>