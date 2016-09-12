<?php

//Sanitize data
sanitize_form_data();

//Instatiate SiteBuilder
include($_SERVER['DOCUMENT_ROOT'].$root."modules/classes/SiteBuilder.class.php");
$SiteBuilder = new SiteBuilder($path);
$pathbits = $SiteBuilder->pathbits;
$page = $SiteBuilder->curr_page_content();
$global = $SiteBuilder->global_settings();
$navigation = $SiteBuilder->get_navigation();
$error404 = $page['error404'];
$sitemap = $SiteBuilder->get_sitemap();

//Session hijacking prevention
if(!isset($_COOKIE['xid']) || $_COOKIE['xid'] == ""){
	$randomstr = gen_random_string();
	$_COOKIE['xid'] = $randomstr;
	setcookie("xid", $randomstr, 0, "/", $_SERVER['HTTP_HOST']);
}

?>