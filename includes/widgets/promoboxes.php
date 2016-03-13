<?php  

$promoboxes = array();
$query = $db->query("SELECT `promo_boxes`.* FROM `pages_promo` LEFT JOIN `promo_boxes` ON `promo_boxes`.`promo_id` = `pages_promo`.`promo_id` WHERE `promo_boxes`.`showhide` = 0 AND `pages_promo`.`page_id` = ? ORDER BY `promo_boxes`.`ordering` ASC", array($page['page_id']));
if($query && !$db->error()) {
	$promoboxes = $db->fetch_array();
}

if(count($promoboxes) > 0) { 
	echo '<section id="promo-boxes" class="full">';
	foreach($promoboxes as $key => $promo) {
		$promo['image'] = ($promo['image'] != '' && file_exists('images/promos/'.$promo['image']) ? $promo['image'] : 'default.jpg');
		echo '<div class="promo-box">
			<h3>' .$promo['title']. '</h3>
			<a href="' .$promo['url']. '" '.($promo['url_target'] > 0 ? 'target="_blank"' : ''). '>
				<img src="' .$promo['image']. '" alt="' .(trim($promo['image_alt']) != '' ? $promo['image_alt'] : $promo['title']). '" />
			</a>
		</div>';
	}
	echo '</section>';
} 
?>