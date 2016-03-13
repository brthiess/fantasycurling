<?php

//System files
include("../../includes/config.php");
include("../../../includes/database.php");
include("../../includes/functions.php");
include("../../includes/utils.php");

if(isset($_POST) && USER_LOGGED_IN){
		
	extract($_POST); //get plain variables
	
	if(!isset($item_col)){
		$item_col = "showhide";
	}
	
	$item_status = ($item_status == "true" ? 0 : 1);
	
	//update item showhide status
	$params = array($item_status, $item_id);
	$update = $db->query("UPDATE $table SET $item_col = ? WHERE $table_id = ?",$params);
	
	if($update && !$db->error()){
		echo $CMSBuilder->mini_alert("<p>Item status successfully saved!</p>",true);
	} else {
		echo $CMSBuilder->mini_alert("<p>There was an error updating this item status: ".$db->error()."</p>",false);
	}
	
} else {
	echo "error";
}
	
?>