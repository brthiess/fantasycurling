<section class='main-content'>
<h2 class='leaderboard-header'><div class='right-side-leaderboard'><span class='gold'>Leaderboard</span></div></h2>

<?php 
	$query = $db->query(	"SELECT COUNT(*) AS points, a.team_name, a.image ".
							"FROM 			user_picks up ".
							"INNER JOIN 	accounts a ON a.account_id = up.account_id ".
							"INNER JOIN		games g ON g.game_id = up.game_id ".
							"INNER JOIN 	draws d ON g.draw_id = d.draw_id ".
							"WHERE 			((d.date = CURDATE() AND d.time < CURTIME()) OR (d.date > CURDATE())) ".			
							"AND 			g.winner_id = up.team_id ".
							"GROUP BY		a.account_id ".
							"ORDER BY 		points DESC");
	$leaderboard_arr = array();
	if($query && !$db->error()){
		$leaderboard_arr = $db->fetch_array();
	}	
	?>
	
	<section class='leaderboard-container'>
		
		<?php 
			$position = 1;
			echo "<div class='leaderboard-row header'>
					<div class='position'>Pos.</div>
					<div class='avatar'></div>
					<div class='team-name'>Team Name</div>
					<div class='points'>Pts.</div>
				</div>";
			foreach($leaderboard_arr as $account){
				echo "<div class='leaderboard-row'>
					<div class='position'>" . $position . "</div>
					<div class='avatar'><img src='" . $path . "images/users/" . ($account['image'] != '' ? $account['image'] : 'default' . rand(1,7) . '.png') . "'/></div>
					<div class='team-name'>" . $account['team_name'] . "</div>
					<div class='points'>" . $account['points'] . "</div>
				</div>";
				$position++;
			}
		?>
	</section>
</section>
