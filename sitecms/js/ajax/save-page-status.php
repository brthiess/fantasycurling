<?php

//System files
include("../../includes/config.php");
include("../../../includes/database.php");
include("../../includes/functions.php");
include("../../includes/utils.php");

if(isset($_POST) && USER_LOGGED_IN){
			
	//update page status
	$params = array($_POST['showhide'], $_POST['id']);
	$update = $db->query("UPDATE pages SET showhide = ? WHERE page_id = ?",$params);
	
	if($update && !$db->error()){
		echo $CMSBuilder->mini_alert("<p>Page status successfully saved!</p>",true);
		sitemapXML();
	} else {
		echo $CMSBuilder->mini_alert("<p>There was an error updating this page status: ".$db->error()."</p>",false);
	}
	
} else {
	echo 'error';
}
	
?>