<?php $breadcrumbs = $SiteBuilder->get_breadcrumb(); ?>

<nav id="breadcrumbs">
	<ol itemscope itemtype="http://schema.org/BreadcrumbList">
		<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop='item' href='<?php echo $siteurl.$path; ?>'><span itemprop="name">Home</span></a><meta itemprop='position' content='1' /> › </li>
	    <?php
	    
	    foreach($breadcrumbs as $key=>$crumb){
			echo "<li itemprop='itemListElement' itemscope itemtype='http://schema.org/ListItem'><a itemprop='item' href='".$siteurl.$crumb['url']."' ><span itemprop='name'>".$crumb['name']."</span></a><meta itemprop='position' content='".($key+2)."' /> ".(count($breadcrumbs) - 1 == $key ? '' : ' <span class="rsaquo">›</span> ' )." </li> ";
		}
		
		?>
	</ol>
</nav>