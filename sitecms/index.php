<?php 

//System files
include("includes/config.php");
include("../includes/database.php");
include("includes/functions.php");
include("includes/utils.php");

//Include plugins
include("../modules/classes/Emogrifier.class.php");
include("modules/classes/ImageUpload.class.php");

//Include modules
include("modules/Login.php");
include("modules/Reset.php");
include("modules/Settings.php");
include("modules/Search.php");
include("modules/Users.php");
include("modules/Pages.php");
include("modules/PromoBoxes.php");
include("modules/CallToAction.php");
include("modules/Slideshow.php");
include("modules/Draws.php");
include("modules/Games.php");
include("modules/Teams.php");


//Header
include("includes/header.php");

//Not found
if($error404){
	include("sections/notfound.php");
	
//Sections
}else{
	include("sections/".$section['filelocation']);
}

//Footer
include("includes/footer.php");

?>