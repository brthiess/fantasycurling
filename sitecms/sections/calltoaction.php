<?php

//Table listing
if(ACTION == ''){
	
	include("includes/widgets/searchform.php");
	echo "<p class='f_right'><a href='" .PAGE_URL. "?action=add' class='button'><i class='fa fa-plus'></i>Add New</a></p>";
	
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Call To Actions
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content nopadding'>";
			echo "<table cellpadding='0' cellspacing='0' border='0' class='tablesorter'>";
		
			echo "<thead>";
			echo "<th width='40px' class='{sorter:false}'></th>";		
			echo "<th width='350px'>Title</th>";
			echo "<th width='70px'>Visible</th>";
			echo "<th class='{sorter:false}'>&nbsp;</th>";
			echo "</thead>";
			
			echo "<tbody>";
			foreach($ctaboxes as $row){
				echo "<tr>";
					echo "<td>" .($row['image'] != "" ? "<a href='" .$path.$imagedir.$row['image']. "' rel='prettyPhoto' title='" .$row['title']. "'>" .renderGravatar($imagedir.$row['image']). "</a>" : ""). "</td>";
					echo "<td>" .$row['title'].($row['subtitle'] != "" ? "<br /><small>" .$row['subtitle']. "</small>" : ""). "</td>";
					echo "<td>".$CMSBuilder->showhide_toggle('pages_cta', 'cta_id', $row['cta_id'], $row['showhide'])."</td>";
					echo "<td class='right'><a href='" .PAGE_URL. "?action=edit&item_id=" .$row['cta_id']. "' class='button-sm'><i class='fa fa-pencil'></i>Edit</a></td>";
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

//Display form	
}else{
	
	if(ACTION == 'edit'){
		$data = $ctaboxes[ITEM_ID];
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
		echo "<div class='panel-header'>Call To Action Details
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			<div class='panel-switch'>
				<label>Show Call To Action</label>
				<div class='onoffswitch'>
					<input type='checkbox' name='showhide' id='showhide' value='0'" .(isset($row['showhide']) && $row['showhide'] ? "" : " checked"). " />
					<label for='showhide'>
						<span class='inner'></span>
						<span class='switch'></span>
					</label>
				</div>
			</div>
		</div>";
		echo "<div class='panel-content clearfix'>";
			echo "<div class='form-field'>
				<label>Title <span class='required'>*</span></label>
				<input type='text' name='title' value='" .(isset($row['title']) ? $row['title'] : ''). "' class='input" .(in_array('title', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>
				<label>Description</label>
				<input type='text' name='subtitle' value='" .(isset($row['subtitle']) ? $row['subtitle'] : ''). "' class='input' />
			</div>";
			echo "<div class='form-field'>
				<label>Button Text " .$CMSBuilder->tooltip('Button Text', 'If a link is provided, a clickable button will be displayed with this text.'). "</label>
				<input type='text' name='url_text' value='" .(isset($row['url_text']) ? $row['url_text'] : ''). "' class='input' />
			</div>";
			echo "<div class='form-field'>
				<label>Link to Page <small>(e.g. http://www.pixelarmy.ca)</small></label>
				<input type='text' name='url' value='" .(isset($row['url']) ? $row['url'] : ''). "' class='input' />
			</div>";
			echo "<div class='form-field'>
				<label>Open Link in</label>
				<select name='url_target' class='select'>
					<option value='0' " .(isset($row['url_target']) && $row['url_target'] == '0' ? "selected" : ""). ">Same Window</option>
					<option value='1' " .(isset($row['url_target']) && $row['url_target'] == '1' ? "selected" : ""). ">New Window</option>
				</select>
			</div>";
		echo "</div>";
	echo "</div>"; //CTA details
	
	//CTA image
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Call To Action Image
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
				<label>Upload Image <span class='required'>*</span> " .$CMSBuilder->tooltip('Upload Image', 'Image must be smaller than 20MB.'). "</label>
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