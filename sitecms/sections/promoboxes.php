<?php

//Table listing
if(ACTION == ''){
	
	include("includes/widgets/searchform.php");
	echo "<p class='f_right'><a href='" .PAGE_URL. "?action=add' class='button'><i class='fa fa-plus'></i>Add New</a></p>";
	
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Promo Boxes 
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content nopadding'>";
			echo "<table cellpadding='0' cellspacing='0' border='0' class='tablesorter'>";
		
			echo "<thead>";
			echo "<th width='40px' class='{sorter:false}'></th>";	
			echo "<th width='25px' class='nopadding'></th>";	
			echo "<th width='350px'>Title</th>";
			echo "<th width='70px'>Visible</th>";
			echo "<th class='{sorter:false}'>&nbsp;</th>";
			echo "</thead>";
			
			echo "<tbody>";
			foreach($promoboxes as $row){
				echo "<tr>";
					echo "<td>" .($row['image'] != "" ? "<a href='" .$path.$imagedir.$row['image']. "' rel='prettyPhoto' title='" .$row['title']. "'>" .renderGravatar($imagedir.$row['image']). "</a>" : ""). "</td>";
					echo "<td class='center nopadding'><small>" .($row['ordering'] == 101 ? "" : $row['ordering']). "</small></td>";
					echo "<td>" .$row['title']. "</td>";
					echo "<td>".$CMSBuilder->showhide_toggle($record_db, $record_id, $row[$record_id], $row['showhide'])."</td>";
					echo "<td class='right'><a href='" .PAGE_URL. "?action=edit&item_id=" .$row[$record_id]. "' class='button-sm'><i class='fa fa-pencil'></i>Edit</a></td>";
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
		$data = $promoboxes[ITEM_ID];
		$image = $data['image'];	
		if(!isset($_POST['save'])){
			$row = $data;
		}
		
	}else if(ACTION == 'add' && !isset($_POST['save'])){
		$image = '';		
		unset($row);
	}

	echo "<form action='' method='post' enctype='multipart/form-data'>";
	
	//Promo details
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Promo Box Details
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			<div class='panel-switch'>
				<label>Show Promo Box</label>
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
				<label>Promo Box Title <span class='required'>*</span></label>
				<input type='text' name='title' value='" .(isset($row['title']) ? $row['title'] : ''). "' class='input" .(in_array('title', $required) ? ' required' : ''). "' />
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
	echo "</div>"; //Promo details
	
	//Promo image
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Promo Box Image
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
			echo "<div class='form-field'>
				<label>Alt Text: <small>(SEO)</small> " .$CMSBuilder->tooltip('Alt Text', 'Provide a brief description of this image.'). "</label>
				<input type='text' name='image_alt' value='" .(isset($row['image_alt']) ? $row['image_alt'] : ''). "' class='input' />
			</div>";
		echo "</div>";
	echo "</div>"; //Promo image

	//Sticky footer
	include("includes/widgets/formbuttons.php");
	
	echo "<input type='hidden' name='xssid' value='" .$_COOKIE['xssid'] ."' />";
	echo "</form>";
	
}

?>