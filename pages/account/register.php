<?php

//form validator
$captcha = "";
$obj = new SPAF_FormValidator();
if (isset($_POST['code'])) {
  if ($obj->validRequest($_POST['code'])) {
    $captcha = true;
    $obj->destroy();
  }
  else {
    $captcha = false;
	$obj->destroy();
  }
}

//get form data
$first_name = str_replace("'", "&rsquo;", stripslashes($_POST['first_name']));
$last_name = str_replace("'", "&rsquo;", stripslashes($_POST['last_name']));
$company = str_replace("'", "&rsquo;", stripslashes($_POST['company']));
$email = str_replace("'", "&rsquo;", stripslashes($_POST['email']));
$phone1 = str_replace("'", "&rsquo;", stripslashes($_POST['phone1']));
$phone2 = str_replace("'", "&rsquo;", stripslashes($_POST['phone2']));
$phone3 = str_replace("'", "&rsquo;", stripslashes($_POST['phone3']));
$phone = $phone1.$phone2.$phone3;
$password = str_replace("'", "&rsquo;", stripslashes($_POST['password']));
$password2 = str_replace("'", "&rsquo;", stripslashes($_POST['password2']));
$address1 = str_replace("'", "&rsquo;", stripslashes($_POST['address1']));
$address2 = str_replace("'", "&rsquo;", stripslashes($_POST['address2']));
$city = str_replace("'", "&rsquo;", stripslashes($_POST['city']));
$province = str_replace("'", "&rsquo;", stripslashes($_POST['province']));
$postalcode = str_replace("'", "&rsquo;", stripslashes($_POST['postalcode']));
$country = str_replace("'", "&rsquo;", stripslashes($_POST['country']));

$displayform = true;
if(isset($_POST['register'])){

	//validate
	if($_POST['xid'] == $_COOKIE['xid'] && $captcha && isset($_POST['terms']) && $password == $password2){
		
		//set parameters
		$params[] = array('param' => 'email', 'value' => $email, 'label' => 'Email Address', 'required' => true, 'unique' => true, 'validate' => 'email', 'hash' => false);
		$params[] = array('param' => 'password', 'value' => $password, 'label' => 'Password', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => true);
		
		$params[] =  array('param' => 'first_name', 'value' => $first_name, 'label' => 'First Name', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
		$params[] =  array('param' => 'last_name', 'value' => $last_name, 'label' => 'Last Name', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
		$params[] =  array('param' => 'company', 'value' => $company, 'label' => 'Company Name', 'required' => false, 'unique' => false, 'validate' => false, 'hash' => false);
		$params[] =  array('param' => 'address1', 'value' => $address1, 'label' => 'Street Address', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
		$params[] =  array('param' => 'address2', 'value' => $address2, 'label' => 'Address Line 2', 'required' => false, 'unique' => false, 'validate' => false, 'hash' => false);
		$params[] =  array('param' => 'city', 'value' => $city, 'label' => 'City/Town', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
		$params[] =  array('param' => 'province', 'value' => $province, 'label' => 'Province/State', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
		$params[] =  array('param' => 'postalcode', 'value' => $postalcode, 'label' => 'Postal/Zip Code', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
		$params[] =  array('param' => 'country', 'value' => $country, 'label' => 'Country', 'required' => true, 'unique' => false, 'validate' => false, 'hash' => false);
		$params[] =  array('param' => 'phone', 'value' => $phone, 'label' => 'Phone Number', 'required' => true, 'unique' => false, 'validate' => 'phone', 'hash' => false);
	
		//register
		try{
			$Account->register($params, true, false, false); //<-- see class for documentation on these paramaters
			$displayform = false;
			
			echo '<h3>Confirm Account</h3>
			<p>Your account has been successfully created and is awaiting activation. A confirmation email has been sent to your inbox with instructions on how to activate your new account. <small>(Be sure to check your junk mail folder!)</small></p>
			<h5 class="uppercase hilite"><strong>You must activate your account before you can login!</strong></h5>';
			
		
		//register error
		}catch(Exception $e){
			$alert = $Account->alert($e->getMessage(), false);
		}
		
	}else if(!$captcha){
		$alert = $Account->alert('Please enter the correct security code.', false);
	}else if(!isset($_POST['terms'])){
		$alert = $Account->alert('Please agree to the terms and conditions.', false);
	}else if($password != $password2){
		$alert = $Account->alert('Your passwords do not match. Please try again.', false);
	}else{
		$alert = $Account->alert('Unable to submit registration. Please try again.', false);	
	}
	
}

//display form
if($displayform){
	echo $alert;
?>

<form name="register_form" id="register_form" action="" method="post">
    <p><small>Required Fields</small> <strong class="required">*</strong></p>
    
    <label>First Name: <strong class="required">*</strong></label>
    <input type="text" name="first_name" class="input" value="<?php echo $first_name; ?>" /><br />
    
    <label>Last Name: <strong class="required">*</strong></label>
    <input type="text" name="last_name" class="input" value="<?php echo $last_name; ?>" /><br />
    
    <label>Company Name:</label>
    <input type="text" name="company" class="input" value="<?php echo $company; ?>" /><br />
    
    <label>Email Address: <strong class="required">*</strong></label>
    <input type="text" name="email" class="input" value="<?php echo $email; ?>" /><br />
    
    <label>Phone Number: <strong class="required">*</strong></label>
    (<input type="text" name="phone1" class="input input_sm" value="<?php echo $phone1; ?>" maxlength="3" />) - 
    <input type="text" name="phone2" class="input input_sm" value="<?php echo $phone2; ?>" maxlength="3" /> - 
    <input type="text" name="phone3" class="input input_sm" value="<?php echo $phone3; ?>" maxlength="4" /><br />
    
    <label>Password: <strong class="required">*</strong></label>
    <input type="password" name="password" class="input" value="<?php echo $password; ?>" /><br />
    
    <label>Re-Enter Password: <strong class="required">*</strong></label>
    <input type="password" name="password2" class="input" value="<?php echo $password2; ?>" /><br />

    <label>Street Address: <strong class="required">*</strong></label>
    <input type="text" name="address1" class="input" value="<?php echo $address1; ?>" /><br />
    
    <label></label>
    <input type="text" name="address2" class="input" value="<?php echo $address2; ?>" /><br />
    
    <label>City/Town: <strong class="required">*</strong></label>
    <input type="text" name="city" class="input" value="<?php echo $city; ?>" /><br />
    
    <label>Province/State: <strong class="required">*</strong></label>
    <select name="province" class="select">
    <?php
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
    <input type="text" name="postalcode" class="input" value="<?php echo $postalcode; ?>" /><br />
    
    <label>Country: <strong class="required">*</strong></label>
    <select name="country" class="select">
        <option value="CA"<?php if($country == "CA"){ echo " selected"; } ?>>Canada</option>
        <option value="USA"<?php if($country == "USA"){ echo " selected"; } ?>>United States</option>
    </select><br />
    
    <label>Security Code: <strong class="required">*</strong></label><input type="text" name="code" class="input input_med f_left" value="" /><img src="<?php echo $path; ?>includes/plugins/formvalidator/img.php?<?php echo time(); ?>" width="100px" height="34px" class="f_left" />
    
    <p><label id="terms_label">
    	<input type="checkbox" name="terms" value="true"<?php if(isset($_POST['terms'])){ echo " checked"; } ?> /> I agree to the terms and conditions below:
    </label></p>
    <div id="terms_box"><p>Terms and conditions go here.</p></div>
    
    <input type="submit" name="register" class="submit" value="Create Account" />
    <input type="hidden" name="xid" value="<?php echo $_COOKIE['xid']; ?>" />
</form>

<?php } ?>