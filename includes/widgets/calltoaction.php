<?php

$cta = array();
if(!empty($page['cta_id'])){
	$query = $db->query("SELECT * FROM `pages_cta` WHERE `showhide` = 0 && `cta_id` = ?", array($page['cta_id']));
	if($query && !$db->error() && $db->num_rows() > 0){
		$result = $db->fetch_array();
		$cta = $result[0];
	}
}
if(!empty($cta)){ 
	echo '<section id="call-to-action">
		<div class="fluid">
			<a href="' .$cta['url']. '" target="' .($cta['url_target'] == 0 ? '_self' : '_blank'). '">
				<h3>' .$cta['title']. '</h3>
				' .(!empty($cta['subtitle']) ? '<p>' .$cta['subtitle']. '</p>' : ''). '
				' .(!empty($cta['image']) && file_exists('images/cta/'.$cta['image']) ? '<img src="' .$cta['image']. '" alt="' .$cta['title']. '" />' : ''). '
			</a>
		</div>
	</section>';
}

?>