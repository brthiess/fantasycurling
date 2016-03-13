<?php

if(!empty($_GET["search"])){
	//set search for this section
	$searchterm = $_GET["search"];
	$CMSBuilder->set_system_search($searchterm, SECTION_ID);
} else {
	$searchterm = $CMSBuilder->system_search(SECTION_ID);
}

if(isset($_POST['clear-search'])){
	$CMSBuilder->set_system_search(NULL, SECTION_ID);
	$searchterm = NULL;
}	
	
?>