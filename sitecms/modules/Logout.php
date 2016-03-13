<?php

//System files
include("../includes/config.php");
include("../../includes/database.php");
include("../includes/functions.php");
include("../../modules/classes/Account.class.php");
$Account = new Account('Admin');

//Logout
try{
	$Account->logout();
	unset($_SESSION['system_search']);
	header('Location: '.$path);	
}catch(Exception $e){
	header('Location: '.$path);	
}

?>