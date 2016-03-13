<?php
if(count($navigation) > 0){
	foreach($navigation as $nav){
		if($nav['type'] == "1"){
			echo '<li><a href="' .$nav['url']. '" target="' .(($nav['urltarget'] == "1") ? "_blank" : ""). '">' .$nav['name']. '</a>';
		}else{
			echo '<li><a class="nav-item" href="' .$nav['page_url']. '" class="' .(($nav['page_id'] == $page['page_id'] || ($nav['page_id'] == $page['parent_id'])) ? "active" : ""). '">' .$nav['name']. '</a>';
		}
		
		if(is_array($nav['sub_pages']) && count($nav['sub_pages']) > 0){
			echo '<ul>';
			foreach($nav['sub_pages'] as $nav2){
				if($nav2['type'] == "1"){
					echo '<li><a href="' .$nav2['url']. '" target="' .(($nav2['urltarget'] == "1") ? "_blank" : ""). '">' .$nav2['name']. '</a>';
				}else{
					echo '<li><a href="' .$nav2['page_url']. '">' .$nav2['name']. '</a>';
				}
				
				if(is_array($nav2['sub_pages']) && count($nav2['sub_pages']) > 0){
					echo '<ul>';
					foreach($nav2['sub_pages'] as $nav3){
						if($nav3['type'] == "1"){
							echo '<li><a href="' .$nav3['url']. '" target="' .(($nav3['urltarget'] == "1") ? "_blank" : ""). '">' .$nav3['name']. '</a>';
						}else{
							echo '<li><a href="' .$nav3['page_url']. '">' .$nav3['name']. '</a>';
						}
						
						if(is_array($nav3['sub_pages']) && count($nav3['sub_pages']) > 0){
							echo '<ul>';
							foreach($nav3['sub_pages'] as $nav4){
								if($nav4['type'] == "1"){
									echo '<li><a href="' .$nav4['url']. '" target="' .(($nav4['urltarget'] == "1") ? "_blank" : ""). '">' .$nav4['name']. '</a>';
								}else{
									echo '<li><a href="'.$nav4['page_url']. '">' .$nav4['name']. '</a>';
								}
								
								if(is_array($nav4['sub_pages']) && count($nav4['sub_pages']) > 0){
									echo '<ul>';
									foreach($nav4['sub_pages'] as $nav5){
										if($nav5['type'] == "1"){
											echo '<li><a href="' .$nav5['url']. '" target="' .(($nav5['urltarget'] == "1") ? "_blank" : ""). '">' .$nav5['name']. '</a></li>';
										}else{
											echo '<li><a href="' .$nav5['page_url']. '">' .$nav5['name']. '</a></li>';
										}
									}
									echo '</ul>';
								}
								echo '</li>';
							}
							echo '</ul>';
						}
						echo '</li>';
					}
					echo '</ul>';
				}
				echo '</li>';
			}
			echo '</ul>';
		}
		echo '</li>';
	}
}
?>