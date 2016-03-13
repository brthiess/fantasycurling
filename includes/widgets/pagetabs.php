<?php

$page_tabs = array();
$query = $db->query("SELECT * FROM pages_tabs WHERE page_id = ? && showhide = 0 ORDER BY ordering", array($page['page_id']));
if($query && !$db->error()){
	$page_tabs = $db->fetch_array();
}

?>

<?php if(count($page_tabs) > 0){ ?>

<!--open content-tabs-->
<div id="content-tabs" class="clear">
    <ul>
	<?php 
	foreach($page_tabs as $tab){ 
		echo '<li><a href="#' .$tab['page']. '">' .$tab['title']. '</a></li>'; 
	} 
	?>
    </ul>
	<?php 
	foreach($page_tabs as $tab){ 
		echo '<div id="' .$tab['page']. '">' .$tab['content']. '</div>'; 
	} 
	?>
</div><!--close content-tabs-->

<?php } ?>