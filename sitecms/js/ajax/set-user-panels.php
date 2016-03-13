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
			$pageurl = str_replace($pathbits[$key], '', $pageurl);
			unset($pathbits[$key]);
		}
	}
	
	$pageurl = "/".implode("/",$pathbits);
	$pageurl = rtrim($pageurl, '/') . '/';
	
	$section = $CMSBuilder->get_section_id($pageurl);
	
	$section_panels = array();	
		
	$params = array(USER_LOGGED_IN,$section);
	$query = $db->query("SELECT * FROM cms_user_panels WHERE account_id = ? AND section_id = ? ORDER BY panel_index",$params);
	if($query && !$db->error()){
		$user_panels = $db->fetch_array();
		foreach($user_panels as $panel){
			$section_panels[] = $panel['panel_index'];
		}
	}
	
	echo json_encode($section_panels);

}
	
?>