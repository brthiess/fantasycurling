<section class='main-content'>
	<h2 class='leaderboard-header'><div class='right-side-leaderboard'><span class='gold'>My Picks</span></div></h2>
<?php
	 

		$query = $db->query("SELECT 		g.*, t2.team_id, t1.team_name AS team1_name, t1.skip_name AS skip1_name, t1.image AS team1_image, t2.image AS team2_image, t2.team_name AS team2_name, t2.skip_name AS skip2_name, d.*  ".
							"FROM 			games g ".
							"INNER JOIN 	teams t1 ON t1.team_id = g.team1_id ".
							"INNER JOIN 	teams t2 ON t2.team_id = g.team2_id ".
							"INNER JOIN 	draws d ON d.draw_id = g.draw_id ".										
							"ORDER BY 		d.ordering");
	$game = array();
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){
			$games[$row['game_id']] = $row;
		}
		
	}
	$query = $db->query("SELECT		* ".
						"FROM 		user_picks up ".
						"WHERE		account_id = ?",array($Account->account_id));
	$user_picks = array();
	if($query && !$db->error()){
		$result = $db->fetch_array();
		foreach($result as $row){
			$user_picks[$row['game_id']] = $row;
		}
	}
	
	foreach($games as $game){
		$games[$game['game_id']]['pick_id'] = (isset($user_picks[$game['game_id']]['team_id']) ? $user_picks[$game['game_id']]['team_id'] : "");
	}
	
	echo "<section class='picks-wrapper'>";
		echo "<form method='post' action='' id='picks-form'>";
			
			$query = $db->query("SELECT * FROM hotshots");
			$hotshots_arr = array();
			if($query && !$db->error()){
				$hotshots_arr = $db->fetch_array();
			}
			
			$query = $db->query("SELECT * FROM user_hotshots WHERE account_id = ?", array($Account->account_id));
			$user_hotshots_id1 = "";
			$user_hotshots_id2 = "";
			if($query && !$db->error()){
				$result = $db->fetch_array();
				if(count($result) > 0){
					$user_hotshots_id1 = $result[0]['hotshot_id'];
					if(count($result) > 1){
						$user_hotshots_id2 = $result[1]['hotshot_id'];
					}
				}			
			}
			echo "<div class='draw-wrapper'>";
				$past_hotshots_deadline = false;
				if(strtotime($global['hotshots_deadline']) < strtotime("now")){
					$past_hotshots_deadline = true;
					echo "<span class='fa fa-lock'></span> ";
				}
				echo "Hotshots Picks
			</div>";
			echo "<div class='select-wrapper'>
				<div class='col-6'>
					<select name='hot_shots_1' class='input' data-type='hotshot' " . ($past_hotshots_deadline == true ? ' disabled ' : '') . ">";
						echo "<option value='-1'>-- Hot Shots Pick 1 --</option>";
						foreach($hotshots_arr as $hotshot){
							echo "<option value='" . $hotshot['hotshot_id'] . "' " . ($user_hotshots_id1 == $hotshot['hotshot_id'] ? " selected " : "") . ">" . $hotshot['name'] . "</option>";
						}
						
					echo "</select>
				</div>
				<div class='col-6'>
					<select name='hot_shots_2' class='input' data-type='hotshot' " . ($past_hotshots_deadline == true ? ' disabled ' : '') . ">";
						echo "<option value='-1'>-- Hot Shots Pick 2 --</option>";
						foreach($hotshots_arr as $hotshot){
							echo "<option value='" . $hotshot['hotshot_id'] . "' " . ($user_hotshots_id2 == $hotshot['hotshot_id'] ? " selected " : "") . ">" . $hotshot['name'] . "</option>";
						}
						
					echo "</select>
				</div>
			</div>";
			$current_draw = -99;
			foreach($games as $game){
				
				
				$query = $db->query("SELECT count(*) AS num_picks, team_id FROM user_picks WHERE game_id =  ? GROUP BY team_id", array($game['game_id']));
				$trend_arr = array();
				if ($query && !$db->error()){
					$result = $db->fetch_array();
					foreach($result as $row){
						$trend_arr[$row['team_id']] = $row;
					}
				}
				$total = 0;
				foreach($trend_arr as $trend){
					$total += $trend['num_picks'];
				}
				foreach($trend_arr as $trend){
					$trend_arr[$trend['team_id']]['popularity'] = $trend['num_picks'] / $total;
				}
				
				if(!isset($trend_arr[$game['team1_id']])){
					$trend_arr[$game['team1_id']]['popularity'] = 0;
				}
				if(!isset($trend_arr[$game['team2_id']])){
					$trend_arr[$game['team2_id']]['popularity'] = 0;
				}
				
				$game_status = (strtotime("now") > strtotime($game['date'] . " " . $game['time']) ? ($game['winner_id'] != -1 ? ($game['pick_id'] == $game['winner_id'] ? "correct" : "wrong") : "locked") : "current");			
				
				$game_status = (strtotime("now") > strtotime($game['date'] . " " . $game['time']) ? ($game['winner_id'] != -1 ? ($game['pick_id'] == $game['winner_id'] ? "correct" : "wrong") : "locked") : "current");			
				if ($current_draw != $game['number']){
					echo "<div class='draw-wrapper'>";
						if ($game_status != 'current') {
							echo "<span class='fa fa-lock''></span> ";
						}
						echo "Draw " . $game['number'] . " - " . date("M j", strtotime($game['date'])) . " " . date("g:ia", strtotime($game['time']));
					echo "</div>";
					$current_draw = $game['number'];
				}
				echo "<div class='game-wrapper " . $game_status . "'>";
					echo "<input class='hidden game-input input' data-type='game' id='radio-" . $game['game_id'] . "-" . $game['team1_id'] . "' type='radio' value='" . $game['team1_id'] . "' name='" . $game['game_id'] . "' " .($game['pick_id'] == $game['team1_id'] ? 'checked' : '') . "/>";
					echo "<label for='radio-" . $game['game_id'] . "-" . $game['team1_id'] . "'  data-id='" . $game['game_id'] . "' team-id='" . $game['team1_id'] . "' class='game-row   " . ($game['pick_id'] == $game['team1_id'] ? $game_status : "") . "	'>
						<div class='game-team'>						
							<div class='team-info'>
								<div class='team-name'>". $game['team1_name'] . "</div>
								<div class='team-skip-name'>" . $game['skip1_name'] . "</div>
							</div>
							<img src='" . $path . "images/teams/" . $game['team1_image'] . "'/>
							<div class='trend-chart'><div class='fill' style='width: " .  number_format(($trend_arr[$game['team1_id']]['popularity'] * 100), 0) . "%;'>" . number_format($trend_arr[$game['team1_id']]['popularity'] * 100, 0) . "%</div></div>
						</div>
					</label>";
					echo "<div class='vs'>";				
						if ($game_status == 'locked'){
							echo "<span class='fa fa-lock'></span>";
						}
						else if ($game_status == 'correct'){
							echo "<span class='fa fa-check'></span>";
						}
						else if ($game_status == 'wrong'){
							echo "<span class='fa fa-times'></span>";
						}
						else if ($game_status == 'current'){
							echo "VS.";
						}
					echo "</div>";
					
					
					echo "<input class='hidden game-input input' data-type='game'  id='radio-" . $game['game_id'] . "-" . $game['team2_id'] . "' type='radio' value='" . $game['team2_id'] . "'  name='" . $game['game_id'] . "' " .($game['pick_id'] == $game['team2_id'] ? 'checked' : '') . "/>";
					echo "<label for='radio-" . $game['game_id'] . "-" . $game['team2_id'] . "' data-id='" . $game['game_id'] . "' team-id='" . $game['team2_id'] . "'  class='game-row  " . ($game['pick_id'] == $game['team2_id'] ? $game_status : "") . "'>
						<div class='game-team'>		
											
							<div class='team-info'>
								<div class='team-name'>". $game['team2_name'] . "</div>
								<div class='team-skip-name'>" . $game['skip2_name'] . "</div>
							</div>	
							<img src='" . $path . "images/teams/" . $game['team2_image'] . "'/>	
							<div class='trend-chart'><div class='fill' style='width: " .  number_format(($trend_arr[$game['team2_id']]['popularity'] * 100 ), 0) . "%;'>" . number_format($trend_arr[$game['team2_id']]['popularity'] * 100, 0) . "%</div></div>						
						</div>
					</label>";											
				echo "</div>";
			}
		echo "</form>";
	echo "</section>";
	echo "<section class='save-picks-wrapper'>";
		echo "<div class='save-picks-container'>";
			echo "<a class='cancel-link' href='" . $sitemap[8]['page_url'] . "'>Cancel</a>";
			echo "<button type='submit' class='solid-button blue rounded' onclick='savePicks(event, \"#picks-form\", this);'>Save Picks</button>";
		echo "</div>";
	echo "</section>";

?>
</section>