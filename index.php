<?php 

//System files
include("includes/config.php");
include("includes/database.php");
include("includes/functions.php");
include("includes/utils.php");

//Include plugins

//Include modules
//initialize account
include("modules/classes/Emogrifier.class.php");
include("modules/classes/Account.class.php");
$Account = new Account();
$user_loggedin = $Account->login_status();
include("modules/Account.php");
include("modules/Login.php");


//Send headers
if($page['type'] == 1 && !$error404){ 
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: " .$page['url']);
	exit();
}
if(isset($page['redirect_to_slug']) && $page['redirect_to_slug'] == true){ 
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: " .$page['page_url']);
	exit();
}
if($error404 == true){
	header("HTTP/1.0 404 Not Found");
}

//Header
include("includes/header.php");

//404 error
if($error404){
	echo $page['content'];
	
//Pages
}
else if (!$user_loggedin) {
	include("pages/account/login.php");
}
else{
	
	switch(($page['page_id'] != '' ? $page['page_id'] : $page['parent_id'])){
		
		//sitemap
		case 2:
			echo '<ul id="sitemap">';
			include("includes/navigation.php");
			echo '</ul>';
		break;
		
		case 3:						
			include("pages/home.php");
		break;
		
		//contact
		case 5:
			include("pages/mypicks.php");
		break;
		
		//standard
		default:
			echo $page['content'];
		break;
	}
}

//Footer
include("includes/footer.php");

?>
