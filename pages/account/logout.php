<?php

include("../includes/database.php");
include("../includes/functions.php");
include("../includes/config.php");
include("../modules/classes/Account.class.php");
$Account = new Account();
try{
	$Account->logout();
	header('Location: ' .$path. 'login/');
}catch(Exception $e){
	header('Location: ' .$path. 'login/');	
}

?>