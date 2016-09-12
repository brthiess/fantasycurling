<?php

try{
	$Account->logout();
	echo "<script>window.location.href='/';</script>";
}catch(Exception $e){
	header('Location: ' .$path. 'login/');	
}

?>