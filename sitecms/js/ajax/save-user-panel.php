<?php

//System files
include("../../includes/config.php");
include("../../../includes/database.php");
include("../../includes/functions.php");
include("../../includes/utils.php");

if(isset($_POST) && USER_LOGGED_IN){
	
	//get section id
	$pageurl = $_SERVER['HTTP_REFERER'];
	$query_string = parse_url($pageurl, PHP_URL_QUERY);
	if($pageurl == $path || $pageurl == $path.'index.php'){
		$pageurl = $path.'home/';
	}
	if(empty($pageurl)){
		$pathbits = array('');
	}else{
		$pathbits = explode("/",  str_replace("http://","",$pageurl));
	}
		
	$shifts = explode("/", $path);
	array_shift($pathbits);
		
	foreach($pathbits as $key => $bit){
		if($pathbits[$key] == "?".$query_string){
			//$pageurl = str_replace($pathbits[$key], '', $pageurl);
			unset($pathbits[$key]);
		}
	}
	
	$pageurl = "/".implode("/",$pathbits);
	$pageurl = rtrim($pageurl, '/') . '/';
				
	$section = $CMSBuilder->get_section_id($pageurl);
	
	//check if panel status is changing
	$params = array(USER_LOGGED_IN,$section,$_POST['panel']);
	$query = $db->query("SELECT * FROM cms_user_panels WHERE account_id = ? AND section_id = ? AND panel_index = ?",$params);
	if($query && !$db->error() && $db->num_rows() > 0){
		if($_POST['status'] == "false"){
			//panel is set to show, remove from db
			$delete = $db->query("DELETE FROM cms_user_panels WHERE account_id = ? AND section_id = ? AND panel_index = ?",$params);
		}
	} else {
		echo $db->error();
		if($_POST['status'] == "true"){
			//panel is set to hide, insert into db
			$insert = $db->query("INSERT INTO cms_user_panels (account_id, section_id, panel_index) VALUES (?,?,?)",$params);
		}
	}
		
}
	
?>