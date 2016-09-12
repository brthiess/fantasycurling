<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $page['meta_title']; ?></title>

<?php if(trim($page['meta_description']) != ''){ ?><meta name="description" content="<?php echo $page['meta_description']; ?>" /><?php } ?>
<link rel="canonical" content="<?php echo $siteurl.$page['meta_canonical']; ?>" />

<meta name="viewport" content="minimum-scale=1.0, width=device-width">
<link rel="apple-touch-icon" href="<?php echo $path; ?>images/HomeScreenIcon.png" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $path; ?>images/favicon.ico" />

<!-- font awesome -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

<!--stylesheets-->
<link rel="stylesheet" href="<?php echo $path; ?>css/layout.css" />


<!--jquery libs-->        
<script type="text/javascript" src="<?php echo $path; ?>js/jquery-1.11.3.min.js"></script>


        
<!--[if lte IE 8]>
<script type="text/javascript" src="<?php echo $path; ?>js/html5shiv.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>js/selectivizr.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>js/respond.min.js"></script>
<![endif]-->

</head>

<body class='<?php if (!$user_loggedin) {echo 'login-body';}?>'>

<?php if($user_loggedin) { ?>
<!--open wrapper-->
<div id="wrapper" >
	<header class='header'>    
        <div id="navigation">
			
			<nav>
				<a class='nav-link' href='<?php echo $path;?>'><img id="nav-logo" src="<?php echo $path;?>images/logo-small.png" alt="<?php echo $global['company_name']; ?>"><span id='nav-title'>Brier<span class='highlighted-text'>King</span></span></a>
				<ul id='nav-list-desktop'>
					<?php include("includes/navigation.php");?>
				</ul>
				<div class='profile-container'>
					<a class='account-link' href='<?php echo $sitemap[6]['page_url'];?>'><img class='account-logo' src='<?php echo $path . "images/" . ($Account->photo != "" ? "users/" . $Account->photo : "default.png");?>'/> <?php echo ($Account->team_name != "" ? $Account->team_name : $Account->username);?> <span class='fa fa-sort-desc'></span></a>
					<div class='dropdown-link'>
						<ul class='dropdown-list'>
							<li>
								<a class='dropdown-item' href='<?php echo $sitemap[6]['page_url'];?>'>Edit Profile</a>								
							</li>
							<li>
								<a class='dropdown-item' href='<?php echo $sitemap[7]['page_url'];?>'>Logout</a>
							</li>
						</ul>
					</div>
				</div>
				<a id="mbl-toggle">
					<span></span>
					<span></span>
					<span></span>
				</a>
			</nav>
			
		</div>  
		<ul id='nav-list'>
			<?php include("includes/mobilenavigation.php");?>										
		</ul>		
	</header>
<?php } ?>