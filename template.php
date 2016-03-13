<!DOCTYPE html>
<html>
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $page['meta_title']; ?></title>

<?php if(trim($page['meta_description']) != ''){ ?><meta name="description" content="<?php echo $page['meta_description']; ?>" /><?php } ?>
<link rel="canonical" content="<?php echo $siteurl.$page['meta_canonical']; ?>" />

<meta name="viewport" content="width=1100, user-scalable=yes" />
<link rel="apple-touch-icon" href="<?php echo $path; ?>images/HomeScreenIcon.png" />
<link rel="shortcut icon" type="image/x-icon" href="<?php echo $path; ?>favicon.ico" />

<meta property="og:title" content="<?php echo $page['meta_title']; ?>" />
<meta property="og:type" content="website" />
<meta property="og:url" content="<?php echo $siteurl.$page['meta_canonical']; ?>" />
<meta property="og:image" content="<?php echo $siteurl; ?>/images/logo.jpg" />
<meta property="og:site_name" content="<?php echo $page['meta_title']; ?>" />
<meta property="og:description" content="<?php echo $page['meta_description']; ?>" />

<!-- font awesome -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">

<!--stylesheets-->
<link rel="stylesheet" href="<?php echo $path; ?>css/typography.css" />
<link rel="stylesheet" href="<?php echo $path; ?>css/layout.css" />
<link rel="stylesheet" href="<?php echo $path; ?>css/print.css" media="print" />
<link rel="stylesheet" href="<?php echo $path; ?>includes/plugins/prettyPhoto/css/prettyPhoto.css" type="text/css" media="screen" charset="utf-8" />
<link rel="stylesheet" href="<?php echo $path; ?>includes/plugins/photoswipe/photoswipe.css" />

<!--jquery libs-->        
<script type="text/javascript" src="<?php echo $path; ?>js/jquery-1.11.3.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>js/jquery-ui-1.10.4.min.js"></script>
        
<!--[if lte IE 8]>
<script type="text/javascript" src="<?php echo $path; ?>js/html5shiv.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>js/selectivizr.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>js/respond.min.js"></script>
<![endif]-->

</head>

<body>

<!--open wrapper-->
<div id="wrapper">

    <!--open main-navigation-->
    <nav id="main-navigation">
    	<ul><?php include('includes/navigation.php'); ?></ul>
    </nav><!--close main-navigation-->
	
	<?php include("includes/breadcrumbs.php"); //optional ?>
	
	<!-- content here! -->
	
<div class="push"></div>

</div><!--close wrapper-->

<!--open footer-->
<div id="footer">

	<!--contact_info-->
    <footer>
    
		<div itemscope itemtype="http://schema.org/Organization"> 
			<h5 itemprop="name"><?php echo $global['company_name']; ?></h5>
			<p>
			<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
				<span itemprop="streetAddress"><?php echo (trim($global['contact_address2']) != '' ? $global['contact_address2']. ', ' .$global['contact_address'] : $global['contact_address'] ); ?></span>,
				<?php
				echo (trim($global['contact_city']) != '' ? '<span itemprop="addressLocality">' .$global['contact_city']. '</span>, ' : '');
				echo (trim($global['contact_province']) != '' ? '<span itemprop="addressRegion">' .$global['contact_province']. '</span> ' : '');
				echo (trim($global['contact_postal_code']) != '' ? '<span itemprop="postalCode">' .$global['contact_postal_code']. '</span> ' : '');
				?>
			</span>
				<?php
                echo (trim($global['contact_phone']) != '' ? '/ Phone: <span itemprop="telephone">' .$global['contact_phone']. '</span> ' : '');
                echo (trim($global['contact_fax']) != '' ? '/ Fax: <span itemprop="faxNumber">' .$global['contact_fax'] . '</span>' : '');	
                echo (trim($global['contact_toll_free']) != '' ? '/ Toll Free: ' .$global['contact_toll_free'] : '');	
                echo (trim($global['contact_email']) != '' ? '/ <a href="mailto:' .$global['contact_email']. '">' .$global['contact_email']. '</a>' : '');	
                ?>
			</p>
            <p class="hosting">Website design &amp; hosting by <a href="http://www.pixelarmy.ca" target="_blank">Pixel Army</a></p>
		</div><!--//contact_info-->
	
    </footer>

	<!-- SHOW/HIDE FOOTER MAP -->
    <?php if($page['google_map'] == 1 || ($page['google_map'] == -1 && $global['google_map'] == 1)) { ?>
    	<!-- insert map here -->
    <?php } ?>

	<!--open social-icons-->
    <ul id="social-icons" itemscope itemtype="http://schema.org/Organization">
		<link itemprop="url" href="<?php echo $siteurl; ?>"> 
	    <?php
		    foreach($global['social'] as $service => $url){
			    echo (trim($url) != '' ? "<li><a itemprop='sameAs' href='$url' target='_blank'>$service<span class='fa fa-$service'></span><span class='fa fa-$service'></span></a></li>" : "");
		    }
		?>
    </ul><!--close social-icons-->

</div><!--close footer-->

<!--scripts-->
<script type="text/javascript" src="<?php echo $path; ?>includes/plugins/prettyPhoto/jquery.prettyPhoto.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>includes/plugins/photoswipe/simple-inheritance.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>includes/plugins/photoswipe/code-photoswipe-1.0.11.min.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>js/modernizr.custom.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>js/ddsmoothmenu.js"></script>
<script type="text/javascript" src="<?php echo $path; ?>js/script.js"></script>

</body>
</html>