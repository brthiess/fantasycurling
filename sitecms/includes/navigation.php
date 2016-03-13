<?php

$navigation = $CMSBuilder->get_navigation();
if(count($navigation) > 0){
	echo '<nav><ul>';
	
		//Account widget
		$profile = $CMSBuilder->get_section(3);
		echo '<li id="account-widget" class="accordion">
			<h4>' .$Account->first_name. ' ' .$Account->last_name. '
				<small>' .$Account->email. '</small>
				<img src="' .$root. 'images/users/' .($Account->photo != "" && file_exists('../images/users/'.$Account->photo) ? $Account->photo : "default.jpg"). '" alt="" />
			</h4>
			<ul>
				<li><a href="' .$path. $profile['page']. '/?action=edit&item_id=' .USER_LOGGED_IN. '"><i class="fa fa-lg fa-user"></i>Account Details</a></li>
				<li><a href="' .$path. $profile['page']. '/?action=edit&item_id=' .USER_LOGGED_IN. '"><i class="fa fa-lg fa-lock"></i>Change Password</a></li>
				<li><a onclick="logout();"><i class="fa fa-lg fa-sign-out"></i>Logout</a></li>
			</ul>
		</li>';
		
		//Navigation sections
		foreach($navigation as $nav){
			if($CMSBuilder->check_permissions($nav['section_id']) || MASTER_USER){
				echo '<li class="' .(is_array($nav['sub_sections']) && count($nav['sub_sections']) > 0 ? 'accordion' : '').($nav['section_id'] == $section['parent_id'] ? ' expanded' : ''). '">
					<a href="' .$nav['page_url']. '" class="' .($nav['section_id'] == $section['section_id'] ? "active" : ""). '">
						<i class="fa fa-lg fa-' .$nav['icon']. '"></i>' .$nav['name']. '
					</a>';
					
					if(is_array($nav['sub_sections']) && count($nav['sub_sections']) > 0){
						echo '<ul class="ui-accordion-content-active">';
						foreach($nav['sub_sections'] as $sub){
							echo '<li><a href="' .$sub['page_url']. '" class="' .($sub['section_id'] == $section['section_id'] ? "active" : ""). '">
								<i class="fa fa-lg fa-' .$sub['icon']. '"></i>' .$sub['name']. '
							</a></li>';
						}
						echo '</ul>';
					}
					
				echo '</li>';
			}
		}
		
	echo '</ul></nav>';
}

?>