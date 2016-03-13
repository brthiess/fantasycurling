<?php
	$query = $db->query("SELECT g.*, t1.team_name AS team1_name, t1.skip_name AS skip1_name, t2.team_name AS team2_name, t2.skip_name AS skip2_name, d.*  FROM games g ".
			   "INNER JOIN teams t1 ON t1.team_id = g.team1_id ".
			   "INNER JOIN teams t2 ON t2.team_id = g.team2_id ".
			   "INNER JOIN draws d ON d.draw_id = g.draw_id ".
			   "ORDER BY d.ordering");
	$game = array();
	if($query && !$db->error()){
		$games = $db->fetch_array();
	}
	echo "<section class='picks-container-wrapper'>";
	if (count($games) > 0) {
		echo "<section class='picks-container'>";
			echo "<h3 class='picks-header'>My Picks</h3>";
			echo "<table class='picks'>
				<tr>
					<th>Draw</th>
					<th>Teams</th>
					<th>Pick</th>
				</tr>";
			$i = 0;
			foreach($games as $game){
				echo "<tr " . ($i % 2 == 0 ? "class='light-gray-bg'" : "") . ">";
				echo "<td rowspan='2'>" . $game['number'] . "</td>";			
				echo "<td>" . $game['team1_name'] . "</td>";
				echo "<td><input class='radio-button' id='g" . $game['game_id'] . "1' type='radio' name='" . $game['game_id'] . "'/><label class='radio-label' for='g" . $game['game_id'] . "1'></label></td>";
				echo "</tr>";
				
				echo "<tr " . ($i % 2 == 0 ? "class='light-gray-bg'" : "") . ">";							
				echo "<td class='vs-logo'>" . $game['team2_name'] . "</td>";
				echo "<td><input class='radio-button' id='g" . $game['game_id'] . "2' type='radio' name='" . $game['game_id'] . "'/><label class='radio-label' for='g" . $game['game_id'] . "2'></label></td>";
				echo "</tr>";
				$i++;
			}
			echo "</table>";
		echo "</section>";
	}
	echo "</section>";
?>