<?php

//Image cropping
if(count($cropimages) > 0){
	include("includes/jcropimages.php");

//Display form
}else{

	echo "<form action='' method='post' enctype='multipart/form-data'>";
	
		//General settings
		echo "<div class='panel'>";
			echo "<div class='panel-header'>General Information
				<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			</div>";
			echo "<div class='panel-content clearfix'>";
				echo "<div class='form-field'>
					<label>Company Name <span class='required'>*</span></label>
					<input type='text' name='company_name' value='" .(isset($global['company_name']) ? $global['company_name'] : ''). "' class='input" .(in_array('company_name', $required) ? ' required' : ''). "' />
					<label>Slogan/Tagline</label>
					<input type='text' name='slogan' value='" .(isset($global['slogan']) ? $global['slogan'] : ''). "' class='input' />
				</div>";
				echo "<div class='form-field'>
					<label>Email Address <span class='required'>*</span> " .$CMSBuilder->tooltip('Email Address', 'This is the default email address that will appear on the website and where all form submissions will send to. This does NOT support multiple email addresses.'). "</label>
					<input type='text' name='contact_email' value='" .(isset($global['contact_email']) ? $global['contact_email'] : ''). "' class='input" .(in_array('contact_email', $required) ? ' required' : ''). "' />
					<label>Contact Form Email Address " .$CMSBuilder->tooltip('Contact Form Email', 'This is the address that all contact form submissions will send to. If left blank, submissions will send to the default email.</p><small><strong>Note:</strong> Comma separate emails if sending to more than one email. (e.g. info@pixelarmy.ca, example@pixelarmy.ca)</small>'). "</label>
					<input type='text' name='email_contactform' value='" .(isset($global['email_contactform']) ? $global['email_contactform'] : ''). "' class='input' />	
				</div>";
				echo "<div class='form-field'>
					<label>Disclaimer " .$CMSBuilder->tooltip('Website Disclaimer', 'This text will display in the footer of your website. This is suitable for legal disclaimers.'). "</label>
					<textarea name='disclaimer' class='textarea'>" .(isset($global['disclaimer']) ? $global['disclaimer'] : ''). "</textarea>
				</div>";
				echo "<div class='form-field'>
					<label>Timezone</label>
					<select name='timezone' class='select'>
						<option value=''>Default to Server</option>";
						$timezones = timezone_list();
						foreach($timezones as $zone => $zone_name){
							echo "<option value='".$zone."'" .((isset($global['timezone']) && $global['timezone'] == $zone) ? " selected" : ""). ">".$zone_name."</option>";
						}
					echo "</select>
				</div>";
			echo "</div>";
		echo "</div>"; //General settings
		
		//Default page banner
		echo "<div class='panel page-content" .(isset($row['type']) && $row['type'] == 1 ? " hidden" : ""). "'>";
			echo "<div class='panel-header'>Default Page Banner
				<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			</div>";
			echo "<div class='panel-content clearfix'>";
				if(isset($global['banner_image']) && $global['banner_image'] != '' && file_exists($imagedir.$global['banner_image'])){
					echo "<div class='img-holder'>
						<a href='" .$path.$imagedir.$global['banner_image']. "' rel='prettyphoto' target='_blank' title=''>
							<img src='" .$path.$imagedir.$global['banner_image']. "' alt='' />
						</a>
						<input type='checkbox' class='checkbox' name='deleteimage' id='deleteimage' value='1'>
						<label for='deleteimage'>Delete Current Image</label>
					</div>";
				}
				echo "<div class='form-field'>
					<label>Upload Image " .$CMSBuilder->tooltip('Upload Image', 'This image will be used as the default banner image for any page without one. Image must be smaller than 20MB.'). "</label>
					<input type='file' class='input" .(in_array('image', $required) ? ' required' : ''). "' name='image' value='' />
					<input type='hidden' name='old_image' value='" .(isset($global['banner_image']) && $global['banner_image'] != '' && file_exists($imagedir.$global['banner_image']) ? $global['banner_image'] : ''). "' />
				</div>";
				echo "<div class='form-field'>
					<label>Alt Text: <small>(SEO)</small> " .$CMSBuilder->tooltip('Alt Text', 'Provide a brief description of this image.'). "</label>
					<input type='text' name='image_alt' value='" .(isset($global['banner_image_alt']) ? $global['banner_image_alt'] : ''). "' class='input' />
				</div>";
			echo "</div>";
		echo "</div>"; //Default page banner
		
		//Contact numbers
		echo "<div class='panel'>";
			echo "<div class='panel-header'>Contact Numbers
				<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			</div>";
			echo "<div class='panel-content clearfix'>";
				echo "<div class='form-field'>
					<label>Phone Number</label>
					<input type='text' name='contact_phone' value='" .(isset($global['contact_phone']) ? $global['contact_phone'] : ''). "' class='input' placeholder='(xxx) xxx-xxxx' />
				</div>";
				echo "<div class='form-field'>
					<label>Fax Number</label>
					<input type='text' name='contact_fax' value='" .(isset($global['contact_fax']) ? $global['contact_fax'] : ''). "' class='input' placeholder='(xxx) xxx-xxxx' />
				</div>";
				echo "<div class='form-field'>
					<label>Toll Free Number</label>
					<input type='text' name='contact_toll_free' value='" .(isset($global['contact_toll_free']) ? $global['contact_toll_free'] : ''). "' class='input' placeholder='x (xxx) xxx-xxxx' />
				</div>";
				echo "<br class='clear' />";
				
				//These numbers are used for Organization microdata on the contact page 
				if(is_array($global['global_numbers'])){
					foreach($global['global_numbers'] as $phone){
						echo "<div class='form-field'>
							<label>" .ucwords($phone['type']). "</label>
							<input type='text' name='phone[]' value='" .(isset($phone['phone']) ? $phone['phone'] : ''). "' class='input' placeholder='(xxx) xxx-xxxx' style='margin-bottom:10px;' />
							<p class='clearfix'>
								<input type='checkbox' class='checkbox' id='tf" .$phone['number_id']. "' name='tollfree_" .$phone['number_id']. "' value='1'" .($phone['tollfree'] == 1 ? " checked" : ""). " /> 
								<label for='tf" .$phone['number_id']. "'><small>Toll Free</small></label>
								<input type='checkbox' class='checkbox' id='hi" .$phone['number_id']. "' name='hearingimpaired_" .$phone['number_id']. "' value='1'" .($phone['hearingimpaired'] == 1 ? " checked" : ""). " />
								<label for='hi" .$phone['number_id']. "'><small>Hearing Impaired</small></label>
							</p>
							<input type='hidden' name='number_id[]' value='" .$phone['number_id']. "' />
						</div>";
					}
				}
				
			echo "</div>";
		echo "</div>"; //Contact numbers
		
		//Location
		echo "<div class='panel'>";
			echo "<div class='panel-header'>Address &amp; Map
				<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
				<div class='panel-switch'>
					<label>Show Google Map " .$CMSBuilder->tooltip('Show Google Map', 'Display your location on an interactive map on the contact page. You can set your location by using the locate function or by clicking and dragging the map pin.'). "</label>
					<div class='onoffswitch'>
						<input type='checkbox' name='google_map' id='google_map' value='1'" .($global['google_map'] ? " checked" : ""). " />
						<label for='google_map'>
							<span class='inner'></span>
							<span class='switch'></span>
						</label>
					</div>
				</div>
			</div>";
			echo "<div class='panel-content clearfix'>";
			
				echo "<fieldset id='gllpLatlonPicker' class='gllpLatlonPicker'>
					<div class='gllpMap'>Google Maps</div>
					<div class='gllpSearch'>
						<button type='button' class='gllpSearchButton button-sm f_right nomargin center' id='gllpSearchButton'><i class='fa fa-location-arrow nomargin'></i></button>
						<input type='text' name='' class='gllpSearchField input f_right nomargin' value='' />
					</div>
					<input type='hidden' name='gpslat' class='gllpLatitude' value='".(isset($global['gpslat']) && $global['gpslat'] != ""  ? $global['gpslat'] : "53.563967")."'/>
					<input type='hidden' name='gpslong' class='gllpLongitude' value='".(isset($global['gpslong']) && $global['gpslong'] != "" ? $global['gpslong'] : "-113.490357")."'/>
					<input type='hidden' name='zoom' class='gllpZoom' value='".(isset($global['zoom']) && $global['zoom'] != 0 ? $global['zoom'] : "12")."'/>
				</fieldset>";
				echo "<div class='f_left'>";
					echo "<div class='form-field'>
						<label>Street Address</label>
						<input type='text' name='contact_address' value='" .(isset($global['contact_address']) ? $global['contact_address'] : ''). "' class='input' />
						<label>Unit No.</label>
						<input type='text' name='contact_address2' value='" .(isset($global['contact_address2']) ? $global['contact_address2'] : ''). "' class='input' />
						<label>City/Town</label>
						<input type='text' name='contact_city' value='" .(isset($global['contact_city']) ? $global['contact_city'] : ''). "' class='input' />
					</div>";
					echo "<div class='form-field'>
						<label>Province/State</label>
						<select name='contact_province' class='select'>";
							echo "<option value=''>- Select -</option>";
							echo "<optgroup label='Canada'>";
							for($p=1; $p<=count($provinces); $p++){
								echo "<option value='" .$provinces[$p][1]. "'" .((isset($global['contact_province']) && $global['contact_province'] == $provinces[$p][1]) ? " selected" : ""). ">" .$provinces[$p][0]. "</option>";	
							}
							echo "</optgroup>";
							echo "<optgroup label='United States'>";
							for($p=1; $p<=count($states); $p++){
								echo "<option value='" .$states[$p][1]. "'" .((isset($global['contact_province']) && $global['contact_province'] == $states[$p][1]) ? " selected" : ""). ">" .$states[$p][0]. "</option>";	
							}
							echo "</optgroup>";
						echo "</select>
						<label>Country</label>
						<select name='contact_country' class='select'>
							<option value=''>- Select -</option>
							<option value='Canada'" .(isset($global['contact_country']) && $global['contact_country'] == 'Canada' ? " selected" : ""). ">Canada</option>
							<option value='United States'" .(isset($global['contact_country']) && $global['contact_country'] == 'United States' ? " selected" : ""). ">United States</option>
						</select>
						<label>Postal/Zip Code</label>
						<input type='text' name='contact_postal_code' value='" .(isset($global['contact_postal_code']) ? $global['contact_postal_code'] : ''). "' class='input' />
					</div>";
				echo "</div>";
			echo "</div>";
		echo "</div>"; //Location
		
		//Business hours
		echo "<div class='panel'>";
			echo "<div class='panel-header'>Business Hours
				<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
				<div class='panel-switch'>
					<label>Show Hours " .$CMSBuilder->tooltip('Show Hours', 'Display your business hours on the contact page.'). "</label>
					<div class='onoffswitch'>
						<input type='checkbox' name='show_hours' id='show_hours' value='1'" .($global['show_hours'] ? " checked" : ""). " />
						<label for='show_hours'>
							<span class='inner'></span>
							<span class='switch'></span>
						</label>
					</div>
				</div>
			</div>";
			echo "<div class='panel-content clearfix nopadding'>";
				
				if(is_array($global['global_hours'])){
					echo "<table cellpadding='0' cellspacing='0' border='0' id='hours'>";
				
					for($h=1; $h<=count($global['global_hours']); $h++){
						$hours = $global['global_hours'][$h-1];
						$start_time = date('G:i', strtotime($hours['start_time']));
						$end_time = date('G:i', strtotime($hours['end_time']));
	
						echo "<tr>
							<td width='100px'>" .$hours['day']. "</td>
							<td width='120px'><select name='start_time" .$h. "' class='select select_sm nomargin'".($hours['closed'] ? " disabled" : "").">";
								for($s=0; $s<24; $s++) {
									echo "<option value='" .$s. ":00' ".($start_time == $s.":00" ? "selected" : "").">" .date("g:i a", strtotime($s.":00")). "</option>";
									echo "<option value='" .$s. ":30' ".($start_time == $s.":30" ? "selected" : "").">" .date("g:i a", strtotime($s.":30")). "</option>";
								}
								echo "</select>
							</td>
							<td width='10px' class='nopadding'>to</td>
							<td width='120px'><select name='end_time" .$h. "' class='select select_sm nomargin'".($hours['closed'] ? " disabled" : "").">";
								for($e=0; $e<24; $e++) {
									echo "<option value='" .$e. ":00' ".($end_time == $e.":00" ? "selected" : "").">" .date("g:i a", strtotime($e.":00")). "</option>";
									echo "<option value='" .$e. ":30' ".($end_time == $e.":30" ? "selected" : "").">" .date("g:i a", strtotime($e.":30")). "</option>";
								}
								echo "</select>
							</td>
							<td><input type='checkbox' class='checkbox' name='closed" .$h. "' id='closed" .$h. "' value='1' ".($hours['closed'] ? "checked" : "")." /><label for='closed" .$h. "'>Closed</label>
							<input type='hidden' name='hours_id" .$h. "' value='" .$hours['hours_id']. "' /></td>
						</tr>";
					}
					echo "</table>";
				}
				
			echo "</div>";
		echo "</div>"; //Business hours
		
		//Social networking
		echo "<div class='panel'>";
			echo "<div class='panel-header'>Social Networking
				<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			</div>";
			echo "<div class='panel-content clearfix'>";
				echo "<label class='clear'>Please Enter the Full URL:</label>";
				if(is_array($global['global_social'])){
					foreach($global['global_social'] as $social){
						echo "<div class='form-field social'>
							<i class='fa fa-" .$social['service']. "'></i>
							<input type='text' name='social_" .$social['id']. "' class='input social' value='" .$social['url']. "' />
						</div>";
					}
				}
			echo "</div>";
		echo "</div>"; //Social networking
		
		//SEO
		echo "<div class='panel'>";
			echo "<div class='panel-header'>Search Engine Optimization
				<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
			</div>";
			echo "<div class='panel-content clearfix'>";
					echo "<div class='form-field'>
						<label>Default Title " .$CMSBuilder->tooltip('Default Title', 'The title of the website. Usually just the name of the company, however can also include a keyword-rich slogan. This will aid in search engine optimization. (e.g. Pixel Army - Edmonton Web Design)'). "</label>
						<input type='text' name='meta_title' class='input' value='" .(isset($global['meta_title']) ? $global['meta_title'] : ''). "' />
						<label>Default Description " .$CMSBuilder->tooltip('Default Description', 'A keyword-rich description of the website that will aid in search engine optimization.'). "</label>
						<textarea name='meta_description' class='textarea'>" .(isset($global['meta_description']) ? $global['meta_description'] : ''). "</textarea>
					</div>";
					echo "<div class='google-preview'>
						<p>This Page in Google Search Results:</p>
						<div>
							" .(trim($global['meta_title']) != "" ? "<h2>" .$global['meta_title']. "</h2>" : ""). "
							" .(trim($global['meta_title']) != "" || trim($global['meta_description']) != "" ? "<h6>" .$_SERVER['HTTP_HOST']. "</h6>" : ""). "
							" .str_limit_characters($global['meta_description'], 160). "
						</div>
					</div>";
			echo "</div>";
		echo "</div>"; //SEO
		
		//Sticky footer
		echo "<footer id='cms-footer' class='resize'>";
		echo "<button type='submit' class='button f_right' name='save' value='save'><i class='fa fa-check'></i>Save Changes</button>";
		echo "</footer>";
	
		echo "<input type='hidden' name='xssid' value='" .$_COOKIE['xssid']. "'/>";	
	echo "</form>";

}

?>