<?php

//Table listing
if(ACTION == ''){
	
	include("includes/widgets/searchform.php");
	echo "<p class='f_right'><a href='" .PAGE_URL. "?action=add' class='button'><i class='fa fa-plus'></i>Add New</a></p>";
	
	echo "<div class='panel'>";
		echo "<div class='panel-header'>hotshots
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content nopadding'>";
			echo "<table cellpadding='0' cellspacing='0' border='0' class='tablesorter'>";
		
			echo "<thead>";	
			
			echo "<th width='350px'>Name</th>";
			echo "<th width='70px'>Province</th>";
			echo "<th class='{sorter:false}'>&nbsp;</th>";
			echo "</thead>";
			
			echo "<tbody>";
			foreach($teams as $row){
				echo "<tr>";			
					echo "<td>" .$row['name'] . "</td>";
					echo "<td>" .$row['province'] . "</td>";
					echo "<td class='right'><a href='" .PAGE_URL. "?action=edit&item_id=" .$row['hotshot_id']. "' class='button-sm'><i class='fa fa-pencil'></i>Edit</a></td>";
				echo "</tr>";	
			}
			echo "</tbody>";
			echo "</table>";
			
			//Pager
			$CMSBuilder->tablesorter_pager();
		
		echo "</div>";	
	echo "</div>";
	

//Image cropping

}else if(count($cropimages) > 0){
	include("includes/jcropimages.php");
}else{
	
	if(ACTION == 'edit'){
		$data = $teams[ITEM_ID];
		$image = $data['image'];
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
		echo "<div class='panel-header'>hotshot Details
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			
		</div>";
		echo "<div class='panel-content clearfix'>";			
			echo "<div class='form-field'>
				<label>Name</label>
				<input type='text' name='name' value='" .(isset($row['name']) ? $row['name'] : ''). "' class='input' />
			</div>";
			echo "<div class='form-field'>
				<label>Province</label>
				<input type='text' name='province' value='" .(isset($row['province']) ? $row['province'] : ''). "' class='input' />
			</div>";
			
		echo "</div>";
	echo "</div>"; //CTA details
	

	//Sticky footer
	include("includes/widgets/formbuttons.php");
	
	echo "<input type='hidden' name='xssid' value='" .$_COOKIE['xssid'] ."' />";
	echo "</form>";
	
}

?>