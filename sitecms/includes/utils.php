<?php

//Sanitize data
sanitize_form_data();

//Instatiate Account
require_once($_SERVER['DOCUMENT_ROOT'].$root."modules/classes/Account.class.php");
$Account = new Account('Admin');

//Instatiate CMSBuilder
require_once($_SERVER['DOCUMENT_ROOT'].$path."modules/classes/CMSBuilder.class.php");
$CMSBuilder = new CMSBuilder($path);
$pathbits = $CMSBuilder->pathbits;
$section = $CMSBuilder->curr_section();
$global = $CMSBuilder->global_settings();
$error404 = $section['error404'];

$cms_settings = $CMSBuilder->cms_settings();
if($cms_settings['enhanced_seo']){ //include SEOAnalyzer
	require_once($_SERVER['DOCUMENT_ROOT'].$path."modules/classes/SEOAnalyzer.class.php");
	$Analyzer = new SEOAnalyzer();
}

//XSS prevention cookie
if(!isset($_COOKIE['xssid']) || $_COOKIE['xssid'] == ""){
	$randomstr = gen_random_string();
	$_COOKIE['xssid'] = $randomstr;
	$domain = ($_SERVER['HTTP_HOST'] != 'localhost:8888') ? $_SERVER['HTTP_HOST'] : false;
	setcookie("xssid", $randomstr, 0, "/");
}
if(!empty($_POST) && $_POST['xssid'] != $_COOKIE['xssid']){
	$Account->logout();
	$CMSBuilder->set_system_alert('Invalid session. Please ensure cookies are enabled and try again.', false);
	header("Location:" .$path. "login/");
	exit();
}

//Definitions
DEFINE('USER_LOGGED_IN', $Account->login_status());
DEFINE('MASTER_USER', $Account->account_has_role('Master'));
DEFINE('SECTION_ID', $section['section_id']);
DEFINE('PARENT_ID', $section['parent_id']);
DEFINE('PAGE_URL', $section['page_url']);
DEFINE('ITEM_ID', (isset($_GET['item_id']) ? $_GET['item_id'] : ''));
DEFINE('ACTION', (isset($_GET['action']) ? $_GET['action'] : ''));

?>