<?php
//System files
include("../../includes/config.php");
include("../../../includes/database.php");
include("../../includes/functions.php");
include("../../includes/utils.php");

if(isset($_POST) && USER_LOGGED_IN){
	$tab_id = $_POST['tab_id'];
	if($tab_id != ''){
		$delete = $db->query("DELETE FROM pages_tabs WHERE tab_id = $tab_id");
		if($delete && !$db->error()){
			echo 'success';	
		}else{
			echo 'error';	
		}
	} else {
		echo 'success';	
	}
} else {
	echo 'error';
}
?>