<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $global["company_name"]; ?> | Content Management System</title>
<meta name="robots" content="noindex, nofollow" />
<meta name="viewport" content="minimum-scale=1.0, width=device-width, maximum-scale=1.0, user-scalable=no">

<!--stylesheets-->
<link rel="stylesheet" href="<?php echo $path; ?>css/base.css" />
<link rel="stylesheet" href="<?php echo $path; ?>includes/plugins/mCustomScrollbar/jquery.mCustomScrollbar.min.css">

<!--font-awesome-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

<!--jquery libs-->        
<script type="text/javascript" src="<?php echo $root; ?>js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="<?php echo $root; ?>js/jquery-ui-1.10.4.min.js"></script>


<!--[if lte IE 8]>
<link rel="stylesheet" href="<?php echo $path; ?>css/ie8.css" />
<script type="text/javascript" src="<?php echo $root; ?>js/html5shiv.js"></script>
<script type="text/javascript" src="<?php echo $root; ?>js/selectivizr.js"></script>
<script type="text/javascript" src="<?php echo $root; ?>js/respond.min.js"></script>
<![endif]-->

</head>

<body>

<?php if(USER_LOGGED_IN){ ?>

<!--open cms-title-->
<header id="cms-title">
    <h1>CMS <small>Content Management System</small></h1>
    <a id="menu-toggle" class="fa fa-bars" onclick="toggleMenu();"></a>
</header>

<!--open cms-wrapper-->
<div id="cms-wrapper" class="clearfix">

	<!--open cms-menu-->
    <div id="cms-menu" class="open">
        <?php include("includes/navigation.php"); ?>
    </div><!--close cms-menu-->

	<!--open content-->
    <section id="cms-content" class="clearfix resize">
	    
	    <div id="system-mini-alerts"></div>
	    
    	<header id="section-title">
            <h1><?php echo (!empty($section['icon']) ? '<i class="fa fa-' .$section['icon']. '"></i>' : ''); ?><?php echo $section['name']; ?></h1>
        </header>
                
		<?php include("includes/widgets/alerts.php"); ?>
	
<?php } ?>