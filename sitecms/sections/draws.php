<?php

//Table listing
if(ACTION == ''){
	
	include("includes/widgets/searchform.php");
	echo "<p class='f_right'><a href='" .PAGE_URL. "?action=add' class='button'><i class='fa fa-plus'></i>Add New</a></p>";
	
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Draws
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content nopadding'>";
			echo "<table cellpadding='0' cellspacing='0' border='0' class='tablesorter'>";
		
			echo "<thead>";	
			echo "<th width='350px'>Time</th>";
			echo "<th width='70px'>Number</th>";
			echo "<th class='{sorter:false}'>&nbsp;</th>";
			echo "</thead>";
			
			echo "<tbody>";
			foreach($draws as $row){
				echo "<tr>";
					echo "<td><small>" . $row['ordering'] . "</small> " . date("M j", strtotime($row['date'])) . ", " . date("g:i a", strtotime($row['time'])) . "</td>";
					echo "<td>" .$row['number'] . "</td>";
					echo "<td class='right'><a href='" .PAGE_URL. "?action=edit&item_id=" .$row['draw_id']. "' class='button-sm'><i class='fa fa-pencil'></i>Edit</a></td>";
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
		$data = $draws[ITEM_ID];	
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
		echo "<div class='panel-header'>Draw Details
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			
		</div>";
		echo "<div class='panel-content clearfix'>";
			echo "<div class='form-field'>
				<label>Time</label>
				<select name='time' class='select'>";
				for($s=0; $s<24; $s++) {
					echo "<option value='" .$s. ":00' ".(strtotime($row['time']) == strtotime($s.":00") ? "selected" : "").">" .date("g:i a", strtotime($s.":00")). "</option>";
					echo "<option value='" .$s. ":30' ".(strtotime($row['time']) == strtotime($s.":30") ? "selected" : "").">" .date("g:i a", strtotime($s.":30")). "</option>";
				}
				echo "</select>
			</div>";
			echo "<div class='form-field'>
				<label>Date</label>
				<input type='text' name='date' value='" .(isset($row['date']) ? $row['date'] : ''). "' class='datepicker input' />
			</div>";
			echo "<div class='form-field'>
				<label>Number</label>
				<input type='text' name='number' value='" .(isset($row['number']) ? $row['number'] : ''). "' class='input' />
			</div>";
			echo "<div class='form-field'>
				<label>Numerical Order" .$CMSBuilder->tooltip('Numerical Order', 'Items will be displayed in the order they were added unless specified here. Items set to &quot;Default&quot; will appear after items with numerical ordering.'). "</label>
				<select name='ordering' class='select'>
					<option value='101'>Default</option>";
					for($i=1; $i<101; $i++){
						echo "<option value='" .$i. "' " .(isset($row['ordering']) && $row['ordering'] == $i ? "selected" : ""). ">" .$i. "</option>";	
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