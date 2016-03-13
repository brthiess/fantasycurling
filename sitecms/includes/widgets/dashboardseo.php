<?php if($cms_settings['enhanced_seo'] && (array_key_exists(3, $Account->roles) || array_key_exists(4, $Account->roles))){ //SEO and master permissions 
	//get all pages that are in the red
	$problem_pages = $Analyzer->get_problem_pages();
	
	//clean up array
	foreach($problem_pages as $key => $seo_page){
		if($seo_page['type'] == 1 || $seo_page['page_id'] < 3 || $seo_page['system_page'] == 1){
			unset($problem_pages[$key]);
		}
	}
	
	if(!empty($problem_pages)){
?>

<div class="cms-overview panel f_left">
	<div class="panel-header">SEO Pages <?php echo $CMSBuilder->tooltip('SEO Pages', 'The pages below have scored low for SEO and require your attention.'); ?></div>
	<div class="panel-content clearfix nopadding">
        <table cellpadding="0" cellspacing="0" border="0">
        <?php
	    $page_count = 0;
        foreach($problem_pages as $seo_page){
	        if($seo_page['type'] == 0 && $seo_page['page_id'] > 2 && $page_count < 10 && $seo_page['system_page'] == 0){
		        echo '<tr>
					<td height="30px">' .$seo_page['page_title']. '</td>
					<td><span class="seo-fail">' .$seo_page['seo_score']. '</span></td>
					<td><a href="'.$path.'pages/content/?action=edit&item_id=' .$seo_page['page_id']. '"><i class="fa fa-pencil"></i></a></td>
				</tr>';
				$page_count++;
	        }
        }
        ?>
        </table>
    </div>
</div>

<?php }
}//seo permissions ?>