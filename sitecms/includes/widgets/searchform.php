<form id="search-form" action="" method="get">
	<input type="text" name="search" class="input" value="<?php echo $searchterm; ?>" placeholder="Search" />
	<?php if($searchterm != ""){ ?>
		<a id="clear-search"><i class="fa fa-times-circle"></i></a>
	<?php } ?>
	<button type="button" class="button" onclick="this.form.submit();"><i class="fa fa-search"></i></button>
</form>
<form id="clear-search-form" name="clear-search-form" class="hidden" action="<?php echo PAGE_URL; ?>" method="post">
	<input type="hidden" name="clear-search" value="Clear" />
	<input type="hidden" name="search" value="" />
	<input type="hidden" name="xssid" value="<?php echo $_COOKIE['xssid']; ?>" />
</form>