<?php

//Table listing
if(ACTION == ''){
	
	include("includes/widgets/searchform.php");
	echo "<p class='f_right'><a href='" .PAGE_URL. "?action=add' class='button'><i class='fa fa-plus'></i>Add New</a></p>";
	
	echo "<div class='panel'>";
		echo "<div class='panel-header'>teams
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content nopadding'>";
			echo "<table cellpadding='0' cellspacing='0' border='0' class='tablesorter'>";
		
			echo "<thead>";	
			echo "<th width='40px' class='{sorter:false}'></th>";
			echo "<th width='350px'>Province</th>";
			echo "<th width='70px'>Skip</th>";
			echo "<th class='{sorter:false}'>&nbsp;</th>";
			echo "</thead>";
			
			echo "<tbody>";
			foreach($teams as $row){
				echo "<tr>";
					echo "<td>" .($row['image'] != "" ? "<a href='" .$path.$imagedir.$row['image']. "' rel='prettyPhoto' title='" .$row['team_name']." - " .$row['skip_name']. "'>" .renderGravatar($imagedir.$row['image']). "</a>" : renderGravatar($imagedir.'default.jpg')). "</td>";
					echo "<td>" .$row['team_name'] . "</td>";
					echo "<td>" .$row['skip_name'] . "</td>";
					echo "<td class='right'><a href='" .PAGE_URL. "?action=edit&item_id=" .$row['team_id']. "' class='button-sm'><i class='fa fa-pencil'></i>Edit</a></td>";
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
		echo "<div class='panel-header'>team Details
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			
		</div>";
		echo "<div class='panel-content clearfix'>";			
			echo "<div class='form-field'>
				<label>Team Name</label>
				<input type='text' name='team_name' value='" .(isset($row['team_name']) ? $row['team_name'] : ''). "' class='input' />
			</div>";
			echo "<div class='form-field'>
				<label>Skip Name</label>
				<input type='text' name='skip_name' value='" .(isset($row['skip_name']) ? $row['skip_name'] : ''). "' class='input' />
			</div>";
			
		echo "</div>";
	echo "</div>"; //CTA details
	
	
		//CTA image
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Team Image
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content clearfix'>";
			if(isset($image) && $image != '' && file_exists($imagedir.$image)){
				echo "<div class='img-holder'>
					<a href='" .$path.$imagedir.$row['image']. "' rel='prettyphoto' target='_blank' title=''>
						<img src='" .$path.$imagedir.$image. "' alt='' />
					</a>
				</div>";
			}
			echo "<div class='form-field'>
				<label>Upload Image  " .$CMSBuilder->tooltip('Upload Image', 'Image must be smaller than 20MB.'). "</label>
				<input type='file' class='input" .(in_array('image', $required) ? ' required' : ''). "' name='image' value='' />
				<input type='hidden' name='old_image' value='" .(isset($image) && $image != '' && file_exists($imagedir.$image) ? $image : ''). "' />
			</div>";
		echo "</div>";
	echo "</div>"; //CTA image

	//Sticky footer
	include("includes/widgets/formbuttons.php");
	
	echo "<input type='hidden' name='xssid' value='" .$_COOKIE['xssid'] ."' />";
	echo "</form>";
	
}

?>