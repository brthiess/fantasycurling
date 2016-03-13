<?php

echo '<p><strong>Welcome Back, '. $Account->first_name.'!</strong></p>';
echo (isset($account_alert) ? $account_alert : '');

?>

<form name="profile_form" id="profile_form" action="" method="post">

    <h3>Update Profile</h3>
    <p><small>Required Fields</small> <strong class="required">*</strong></p>

    <label>First Name: <strong class="required">*</strong></label>
    <input type="text" name="first_name" class="input" value="<?php echo (isset($_POST['first_name']) ? $_POST['first_name'] : $Account->first_name); ?>" /><br />
    
    <label>Last Name: <strong class="required">*</strong></label>
    <input type="text" name="last_name" class="input" value="<?php echo (isset($_POST['last_name']) ? $_POST['last_name'] : $Account->last_name); ?>" /><br />
    
    <label>Username: <strong class="required">*</strong></label>
    <input type="text" name="username" class="input" value="<?php echo (isset($_POST['username']) ? $_POST['username'] : $Account->username); ?>" /><br />
    
    <label>Company Name:</label>
    <input type="text" name="company" class="input" value="<?php echo (isset($_POST['company']) ? $_POST['company'] : $Account->company); ?>" /><br />
    
    <label>Email Address: <strong class="required">*</strong></label>
    <input type="text" name="email" class="input" value="<?php echo (isset($_POST['email']) ? $_POST['email'] : $Account->email); ?>" /><br />
    
    <label>Phone Number: <strong class="required">*</strong></label>
    (<input type="text" name="phone1" class="input input_sm" value="<?php echo (isset($_POST['phone1']) ? $_POST['phone1'] : substr($Account->phone, 0, 3)); ?>" maxlength="3" />) - 
    <input type="text" name="phone2" class="input input_sm" value="<?php echo (isset($_POST['phone2']) ? $_POST['phone2'] : substr($Account->phone, 3, -4)); ?>" maxlength="3" /> - 
    <input type="text" name="phone3" class="input input_sm" value="<?php echo (isset($_POST['phone3']) ? $_POST['phone3'] : substr($Account->phone, -4)); ?>" maxlength="4" /><br />
    
    <label>Street Address: <strong class="required">*</strong></label>
    <input type="text" name="address1" class="input" value="<?php echo (isset($_POST['address1']) ? $_POST['address1'] : $Account->address1); ?>" /><br />
    
    <label>&nbsp;</label>
    <input type="text" name="address2" class="input" value="<?php echo (isset($_POST['address2']) ? $_POST['address2'] : $Account->address2); ?>" /><br />
    
    <label>City/Town: <strong class="required">*</strong></label>
    <input type="text" name="city" class="input" value="<?php echo (isset($_POST['city']) ? $_POST['city'] : $Account->city); ?>" /><br />
    
    <label>Province/State: <strong class="required">*</strong></label>
    <select name="province" class="select">
    	<option value="">- Select -</option>
		<?php
		$province = (isset($_POST['province']) ? $_POST['province'] : $Account->province);
        echo "<optgroup label='Canada'>";
        for($p=1; $p<=count($provinces); $p++){
            echo "<option value='" .$provinces[$p][1]. "'" .(($province == $provinces[$p][1]) ? " selected" : ""). ">" .$provinces[$p][0]. "</option>";	
        }
        echo "</optgroup>";
        echo "<optgroup label='United States'>";
        for($p=1; $p<=count($states); $p++){
            echo "<option value='" .$states[$p][1]. "'" .(($province == $states[$p][1]) ? " selected" : ""). ">" .$states[$p][0]. "</option>";	
        }
        echo "</optgroup>";
        ?>
    </select><br />
    
    <label>Postal/Zip Code: <strong class="required">*</strong></label>
    <input type="text" name="postalcode" class="input" value="<?php echo (isset($_POST['postalcode']) ? $_POST['postalcode'] : $Account->postalcode); ?>" /><br />
    
    <label>Country: <strong class="required">*</strong></label>
    <select name="country" class="select">
    	<option value="">- Select -</option>
        <?php
		$country = (isset($_POST['country']) ? $_POST['country'] : $Account->country);
		echo '<option value="CA"' .($country == 'CA' ? ' selected' : ''). '>Canada</option>';
        echo '<option value="USA"' .($country == 'USA' ? ' selected' : ''). '>United States</option>';
		?>
    </select><br />
    
    <input type="submit" name="saveprofile" class="submit f_right" value="Save Changes &rsaquo;" />
    <input type="hidden" name="xid" value="<?php echo $_COOKIE['xid']; ?>" />

</form>

    
<form name="password_form" id="password_form" action="" method="post">

    <h3>Change Password</h3>
    <p><small>Required Fields</small> <strong class="required">*</strong></p>
      
    <label>Old Password: <strong class="required">*</strong></label>
    <input type="password" name="old_password" class="input" value="" /><br />
    
    <label>New Password: <strong class="required">*</strong></label>
    <input type="password" name="new_password" class="input" value="" /><br />
    
    <label>Re-Enter Password: <strong class="required">*</strong></label>
    <input type="password" name="confirm_password" class="input" value="" /><br />
   
    <input type="submit" name="savepassword" class="submit f_right" value="Save Changes" />
    <input type="hidden" name="xid" value="<?php echo $_COOKIE['xid']; ?>" />

</form>
