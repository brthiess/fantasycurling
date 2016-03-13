<?php

//Table listing
if(ACTION == ''){
	
	include("includes/widgets/searchform.php");
	echo "<p class='f_right'><a href='" .PAGE_URL. "?action=add' class='button'><i class='fa fa-plus'></i>Add New</a></p>";
	
	echo "<div class='panel'>";
		echo "<div class='panel-header'>games
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content nopadding'>";
			echo "<table cellpadding='0' cellspacing='0' border='0' class='tablesorter'>";
		
			echo "<thead>";	
			echo "<th width='120px'>Draw</th>";
			echo "<th width='120px'>Team 1</th>";
			echo "<th width='120px'>Team 2</th>";
			echo "<th width='120px'>Winner</th>";
			
			echo "<th class='{sorter:false}'>&nbsp;</th>";
			echo "</thead>";
			
			echo "<tbody>";
			foreach($games as $row){
				echo "<tr>";
					echo "<td>" . '<small>#' . $row['draw_number'] . "</small> &nbsp;" . date('g:i a', strtotime($row['draw_time']))  . "</td>";
					echo "<td>" .$row['team1_name'] . "</td>";
					echo "<td>" .$row['team2_name'] . "</td>";
					echo "<td>" .($row['winner_id'] == -1 ? "<small>TBD<small>" : $row['winner_name']) . "</td>";
					
					echo "<td class='right'><a href='" .PAGE_URL. "?action=edit&item_id=" .$row['game_id']. "' class='button-sm'><i class='fa fa-pencil'></i>Edit</a></td>";
				echo "</tr>";	
			}
			echo "</tbody>";
			echo "</table>";
			
			//Pager
			$CMSBuilder->tablesorter_pager();
		
		echo "</div>";	
	echo "</div>";
	

//Image cropping
}else{
	
	if(ACTION == 'edit'){
		$data = $games[ITEM_ID];	
		if(!isset($_POST['save'])){
			$row = $data;
		}
		
	}else if(ACTION == 'add' && !isset($_POST['save'])){
		$image = '';		
		unset($row);
	}

	echo "<form action='' method='post' enctype='multipart/form-data'>";
	
	//CTA details
	echo "<div class='panel'>";
		echo "<div class='panel-header'>game Details
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			
		</div>";
		$db->query("SELECT * FROM draws");
		$draws = $db->fetch_array();
		echo "<div class='panel-content clearfix'>";
			echo "<div class='form-field'>
				<label>Draw</label>
				<select name='draw_id' class='select'>";
				foreach($draws as $draw) {
					echo "<option value='" .$draw['draw_id'] . "' ".(isset($row['draw_id']) && $row['draw_id'] == $draw['draw_id'] ? "selected" : "").">" .date("g:i a", strtotime($draw['time'])) . ' - ' . $draw['number'] . "</option>";

				}
				echo "</select>
			</div>";
			$db->query("SELECT * FROM teams");
			$teams = $db->fetch_array();
			echo "<div class='form-field'>
				<label>Team 1</label>
				<select name='team1_id' class='select'>";
				foreach($teams as $team) {
					echo "<option value='" .$team['team_id'] . "' ".(isset($row['team1_id']) && $row['team1_id'] == $team['team_id'] ? "selected" : "").">" . $team['team_name'] . ' - ' . $team['skip_name'] . "</option>";
				}
				echo "</select>
			</div>";
			echo "<div class='form-field'>
				<label>Team 2</label>
				<select name='team2_id' class='select'>";
				foreach($teams as $team) {
					echo "<option value='" .$team['team_id'] . "' ".(isset($row['team2_id']) && $row['team2_id'] == $team['team_id'] ? "selected" : "").">" . $team['team_name'] . ' - ' . $team['skip_name'] . "</option>";

				}
				echo "</select>
			</div>";
			echo "<div class='form-field'>
				<label>Winner</label>
				<select name='winner_id' class='select'>";
				echo "<option value='-1'>TBD</option>";
				foreach($teams as $team) {
					echo "<option value='" .$team['team_id'] . "' ".(isset($row['winner_id']) && $row['winner_id'] == $team['team_id'] ? "selected" : "").">" . $team['team_name'] . ' - ' . $team['skip_name'] . "</option>";

				}
				echo "</select>
			</div>";
			
		echo "</div>";
	echo "</div>"; //CTA details

	//Sticky footer
	include("includes/widgets/formbuttons.php");
	
	echo "<input type='hidden' name='xssid' value='" .$_COOKIE['xssid'] ."' />";
	echo "</form>";
	
}

?>