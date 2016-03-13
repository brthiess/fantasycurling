<?php
	$query = $db->query("SELECT g.*, t1.team_name AS team1_name, t1.skip_name AS skip1_name, t2.team_name AS team2_name, t2.skip_name AS skip2_name, d.*  FROM games g ".
			   "INNER JOIN teams t1 ON t1.team_id = g.team1_id ".
			   "INNER JOIN teams t2 ON t2.team_id = g.team2_id ".
			   "INNER JOIN draws d ON d.draw_id = g.draw_id ".
			   "ORDER BY d.ordering");
	$draws = array();
	if($query && !$db->error()){
		$draws = $db->fetch_array();
	}
	echo "<section class='picks-container-wrapper'>";
	if (count($draws) > 0) {
		echo "<section class='picks-container'>";
			echo "<h3 class='picks-header'>My Picks</h3>";
			echo "<table class='picks'>
				<tr>
					<th>Draw</th>
					<th>Teams</th>
					<th>Pick</th>
				</tr>";
			$i = 0;
			foreach($draws as $draw){
				echo "<tr>";
				echo "<td>" . $draw['number'] . "</td>";			
				echo "<td>" . $draw['team1_name'] . "</td>";
				echo "<td><input type='radio'/></td>";
				echo "</tr>";
				
				echo "<tr>";
				echo "<td>" . $draw['number'] . "</td>";			
				echo "<td>" . $draw['team2_name'] . "</td>";
				echo "<td><input type='radio'/></td>";
				echo "</tr>";
				
			}
			echo "</table>";
		echo "</section>";
	}
	echo "</section>";
?>