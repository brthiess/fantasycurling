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
			echo "<th>Account ID</th>";
			echo "<th>Username</th>";
			echo "<th>Name</th>";
			echo "<th>Email</th>";
			echo "<th>Status</th>";
			echo "<th class='{sorter:false}'>&nbsp;</th>";
			echo "</thead>";
			
			echo "<tbody>";
			foreach($allusers as $row){
				echo "<tr>";

				if($row['master']){
					echo "<td><strong>" .$row['account_id']. "</strong></td>";
					echo "<td><strong>" .$row['username']. "</strong></td>";
					echo "<td><strong>" .$row['first_name']. " " .$row['last_name']. "</strong></td>";
					echo "<td><strong>" .$row['email']. "</strong></td>";
					echo "<td><strong>" .$row['status']. "</strong></td>";
				}else{
					echo "<td>" .$row['account_id']. "</td>";
					echo "<td>" .$row['username']. "</td>";
					echo "<td>" .$row['first_name']. " " .$row['last_name']. "</td>";
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
	
//Display form	
}else{
	
	if(ACTION == 'edit'){
		$data = $allusers[ITEM_ID];
		$master = $data['master'];	
		if(!isset($_POST['save'])){
			$row = $data;
		}
		
	}else if(ACTION == 'add' && !isset($_POST['save'])){	
		unset($row);
	}

	echo "<form action='' method='post' enctype='multipart/form-data'>";
	
	//Account details
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Account Details
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content clearfix'>";
			echo "<div class='form-field'>
				<label>First Name <span class='required'>*</span></label>
				<input type='text' name='first_name' value='" .(isset($row['first_name']) ? $row['first_name'] : ''). "' class='input" .(in_array('first_name', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>
				<label>Last Name <span class='required'>*</span></label>
				<input type='text' name='last_name' value='" .(isset($row['last_name']) ? $row['last_name'] : ''). "' class='input" .(in_array('last_name', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>
				<label>Email Address <span class='required'>*</span></label>
				<input type='text' name='email' value='" .(isset($row['email']) ? $row['email'] : ''). "' class='input" .(in_array('email', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>	
				<label>Username <span class='required'>*</span></label>
				<input type='text' name='username' value='" .(isset($row['username']) ? $row['username'] : ''). "' class='input" .(in_array('username', $required) ? ' required' : ''). "' />
			</div>";
			if(isset($row['master']) && $row['master'] == false) {
				echo "<div class='form-field'>
					<label>Status <span class='required'>*</span></label>
					<select name='status' class='select".(in_array('status', $required) ? ' required' : '')."'>";
						if(isset($row['status']) && $row['status'] == 'Pending') {
							echo "<option value='Pending'" .(isset($row['status']) && $row['status'] == 'Pending' ? " selected" : ""). ">Pending</option>";
						}
						echo "<option value='Active'" .(isset($row['status']) && $row['status'] == 'Active' ? " selected" : ""). ">Active</option>
						<option value='Suspended'" .(isset($row['status']) && $row['status'] == 'Suspended' ? " selected" : ""). ">Suspended</option>
					</select>
				</div>";
			} else {
				echo "<input type='hidden' name='status' value='".(isset($row['status']) ? $row['status'] : 'Pending')."' />";
			}
		echo "</div>";
	echo "</div>"; //Account details

	// Personal Info
	echo "<div class='panel'>";
		echo "<div class='panel-header'>Personal Information
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>";
		echo "<div class='panel-content clearfix'>";
			echo "<div class='form-field'>
				<label>Company Name </label>
				<input type='text' name='company' value='" .(isset($row['company']) ? $row['company'] : ''). "' class='input' />
			</div>";
			echo "<div class='form-field'>
				<label>Phone Number <span class='required'>*</span></label>
				<input type='text' name='phone' value='" .(isset($row['phone']) ? $row['phone'] : ''). "' class='input" .(in_array('phone', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>
				<label>Fax Number </label>
				<input type='text' name='fax' value='" .(isset($row['fax']) ? $row['fax'] : ''). "' class='input' />
			</div>";
			echo "<br class='clear'/>";
			echo "<div class='form-field'>
				<label>Street Address <span class='required'>*</span></label>
				<input type='text' name='address1' value='" .(isset($row['address1']) ? $row['address1'] : ''). "' class='input" .(in_array('address1', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>
				<label>Unit No. </label>
				<input type='text' name='address2' value='" .(isset($row['address2']) ? $row['address2'] : ''). "' class='input' />
			</div>";
			echo "<div class='form-field'>
				<label>City/Town <span class='required'>*</span></label>
				<input type='text' name='city' value='" .(isset($row['city']) ? $row['city'] : ''). "' class='input" .(in_array('city', $required) ? ' required' : ''). "' />
			</div>";
			echo "<div class='form-field'>
				<label>Province/State <span class='required'>*</span></label>
				<select name='province' class='select".(in_array('province', $required) ? ' required' : '')."'>";
					echo "<option value=''>- Select -</option>";
					echo "<optgroup label='Canada'>";
						for($p=1; $p<=count($provinces); $p++){
							echo "<option value='" .$provinces[$p][1]. "'" .((isset($row['province']) && $row['province'] == $provinces[$p][1]) ? " selected" : ""). ">" .$provinces[$p][0]. "</option>";	
						}
					echo "</optgroup>";
					echo "<optgroup label='United States'>";
						for($p=1; $p<=count($states); $p++){
							echo "<option value='" .$states[$p][1]. "'" .((isset($row['province']) && $row['province'] == $states[$p][1]) ? " selected" : ""). ">" .$states[$p][0]. "</option>";	
						}
					echo "</optgroup>";
				echo "</select>
			</div>";
			echo "<div class='form-field'>
				<label>Country <span class='required'>*</span></label>
				<select name='country' class='select".(in_array('country', $required) ? ' required' : '')."'>
					<option value=''>- Select -</option>
					<option value='Canada'" .(isset($row['country']) && $row['country'] == 'Canada' ? " selected" : ""). ">Canada</option>
					<option value='United States'" .(isset($row['country']) && $row['country'] == 'United States' ? " selected" : ""). ">United States</option>
				</select>
			</div>";
			echo "<div class='form-field'>
				<label>Postal/Zip Code <span class='required'>*</span></label>
				<input type='text' name='postalcode' value='" .(isset($row['postalcode']) ? $row['postalcode'] : ''). "' class='input" .(in_array('postalcode', $required) ? ' required' : ''). "' />
			</div>";
		echo "</div>";
	echo "</div>"; // Personal Info
	
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

	//Sticky footer
	echo "<footer id='cms-footer' class='resize'>";
		echo "<button type='submit' class='button f_right' name='save' value='save'><i class='fa fa-check'></i>Save Changes</button>";
		if(ITEM_ID != ""){
			echo ($master ? $CMSBuilder->tooltip('Delete User', 'Master accounts cannot be deleted.').'&nbsp;' : '');
			echo "<button type='button' name='delete' value='delete' class='button delete'" .($master ? " disabled" : ""). "><i class='fa fa-trash'></i>Delete</button>";
		}
		
		echo "<a href='" .PAGE_URL. "' class='cancel'>Cancel</a>";
	echo "</footer>";
	
	echo "<input type='hidden' name='xssid' value='" .$_COOKIE['xssid'] ."' />";
	echo "</form>";
	
}

?>