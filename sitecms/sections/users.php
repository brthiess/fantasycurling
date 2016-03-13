<?php

//Table listing
if(ACTION == ''){
	
	include("includes/widgets/searchform.php");
	echo "<p class='f_right'><a href='" .PAGE_URL. "?action=add' class='button'><i class='fa fa-plus'></i>Add New</a></p>";
	
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Users 
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content nopadding'>";
			echo "<table cellpadding='0' cellspacing='0' border='0' class='tablesorter'>";
		
			echo "<thead>";
			echo "<th width='40px' class='{sorter:false}'></th>";
			echo "<th>User ID</th>";
			echo "<th>Team Name</th>";
			echo "<th>Email</th>";
			echo "<th>Status</th>";
			echo "<th class='{sorter:false}'>&nbsp;</th>";
			echo "</thead>";
			
			echo "<tbody>";
			foreach($allusers as $row){
				echo "<tr>";
					echo "<td>" .($row['photo'] != "" ? "<a href='" .$path.$imagedir.$row['photo']. "' rel='prettyPhoto' title='" .$row['first_name']." " .$row['last_name']. "'>" .renderGravatar($imagedir.$row['photo']). "</a>" : renderGravatar($imagedir.'default.jpg')). "</td>";
				
				if($row['master']){
					echo "<td><strong>" .$row['account_id']. "</strong></td>";
					echo "<td><strong>" .$row['team_name']. "</strong></td>";
					echo "<td><strong>" .$row['email']. "</strong></td>";
					echo "<td><strong>" .$row['status']. "</strong></td>";
				}else{
					echo "<td>" .$row['account_id']. "</td>";
					echo "<td>" .$row['team_name']. "</td>";
					echo "<td>" .$row['email']. "</td>";
					echo "<td>" .$row['status']. "</td>";
				}
					echo "<td class='right'><a href='" .PAGE_URL. "?action=edit&item_id=" .$row['account_id']. "' class='button-sm'><i class='fa fa-pencil'></i>Edit</a></td>";
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
		$data = $allusers[ITEM_ID];
		$image = $data['photo'];
		$master = $data['master'];	
		if(!isset($_POST['save'])){
			$row = $data;
			$roles = $data['roles'];
			$permissions = $data['permissions'];
		}
		
	}else if(ACTION == 'add' && !isset($_POST['save'])){
		$image = '';		
		unset($row);
	}

	echo "<form action='' method='post' enctype='multipart/form-data'>";
	
	//Account details
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Account Details
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content clearfix'>";
			echo "<div class='form-field hidden'>
				<label>First Name <span class='required'>*</span></label>
				<input type='text' name='first_name' value='" .(isset($row['first_name']) ? $row['first_name'] : ''). "' class='input" .(in_array('first_name', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field hidden'>
				<label>Last Name <span class='required'>*</span></label>
				<input type='text' name='last_name' value='" .(isset($row['last_name']) ? $row['last_name'] : ''). "' class='input" .(in_array('last_name', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>
				<label>Email Address <span class='required'>*</span></label>
				<input type='text' name='email' value='" .(isset($row['email']) ? $row['email'] : ''). "' class='input" .(in_array('email', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>	
				<label>Team Name </label>
				<input type='text' name='team_name' value='" .(isset($row['team_name']) ? $row['team_name'] : ''). "' class='input" .(in_array('team_name', $required) ? ' required' : ''). "' />
			</div>";
		echo "</div>";
	echo "</div>"; //Account details
	
	//Account permissions
	if($fullaccess){
		echo "<div class='panel hidden'>";
			echo "<div class='panel-header'>Account Permissions
				<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			</div>";
			echo "<div class='panel-content clearfix'>";
				if($master){
					echo "<em>Master accounts have full access.</em>";
				}else{
					echo "<p>User will have access to view/edit/delete entries in the following sections:</p><p>";
					foreach($Account->get_account_roles() as $role){
						if($role['role_id'] > 3){
							echo "<input type='checkbox' value='" .$role['role_id']. "' id='role_" .$role['role_id']. "' name='roles[]' class='checkbox'" .(in_array($role['role_id'], $roles) ? " checked" : ""). "><label for='role_" .$role['role_id']. "'>" .$role['role_name']. "</label><br />";
						}
					}
					foreach($navigation as $nav){
						if($nav['section_id'] > 4){
							echo "<input type='checkbox' value='" .$nav['section_id']. "' id='section_" .$nav['section_id']. "' name='permissions[]' class='checkbox'" .(in_array($nav['section_id'], $permissions) ? " checked" : ""). "><label for='section_" .$nav['section_id']. "'>" .$nav['name']. "</label><br />";
						}
					}
				}
			echo "</p></div>";
		echo "</div>";
	} //Account permissions
	
	//Account password
	echo "<div class='panel'>";
		echo "<div class='panel-header'>" .(ITEM_ID != '' ? 'Change' : 'Account'). " Password
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content clearfix'>";
			echo "<input type='password' name='' class='hidden' />"; 
			echo "<div class='form-field'>
				<label>Password <span class='required'>*</span></label>
				<input type='password' name='password' value='' class='input" .(in_array('password', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>
				<label>Re-enter Password <span class='required'>*</span></label>
				<input type='password' name='password2' value='' class='input" .(in_array('password2', $required) ? ' required' : ''). "' />
			</div>";
		echo "</div>";
	echo "</div>"; //Account password
	
	//Profile image
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Profile Image
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content clearfix'>";
			if(isset($image) && $image != '' && file_exists($imagedir.$image)){
				echo "<div class='img-holder'>
					<a href='" .$path.$imagedir.$image. "' rel='prettyphoto' target='_blank' title=''>
						<img src='" .$path.$imagedir.$image. "' alt='' />
					</a>
					<input type='checkbox' class='checkbox' name='deleteimage' id='deleteimage' value='true' />
					<label for='deleteimage'><small>Remove Current Image</small></label>
				</div>";
			}
			echo "<div class='form-field'>
				<label>Upload Image " .$CMSBuilder->tooltip('Upload Image', 'Image must be smaller than 20MB.'). "</label>
				<input type='file' class='input" .(in_array('image', $required) ? ' required' : ''). "' name='image' value='' />
				<input type='hidden' name='old_image' value='" .(isset($image) && $image != '' && file_exists($imagedir.$image) ? $image : ''). "' />
			</div>";
		echo "</div>";
	echo "</div>"; //Profile image

	//Sticky footer
	echo "<footer id='cms-footer' class='resize'>";
		echo "<button type='submit' class='button f_right' name='save' value='save'><i class='fa fa-check'></i>Save Changes</button>";
		if(SECTION_ID != 3){
			if(ITEM_ID != ""){
				echo ($master ? $CMSBuilder->tooltip('Delete User', 'Master accounts cannot be deleted.').'&nbsp;' : '');
				echo "<button type='button' name='delete' value='delete' class='button delete'" .($master ? " disabled" : ""). "><i class='fa fa-trash'></i>Delete</button>";
			}
			
			echo "<a href='" .PAGE_URL. "' class='cancel'>Cancel</a>";
		}
	echo "</footer>";
	
	echo "<input type='hidden' name='xssid' value='" .$_COOKIE['xssid'] ."' />";
	echo "</form>";
	
}

?>