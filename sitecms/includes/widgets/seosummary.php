<?php
	
try{
	//set page/item details
	$Analyzer->set_page($row['focus_keyword'],$page_url,($row['slug'] != NULL ? $row['slug'] : $row['page']),$row['page_title'],$row['page_id'],$item_table,$item_key);
	//analyze page
	$Analyzer->analyze_page();
}catch(Exception $e){
	echo "<p class='error'><i class='fa fa-exclamation-triangle'></i> &nbsp;<strong>Warning:</strong> ".$e->getMessage()."</p><hr/>";
}

//get page score
$overall_score = $Analyzer->get_score();
if($overall_score > 80){
	$class = "seo-pass";
} else if($overall_score >= 50 && $overall_score <= 80){
	$class = "seo-average";
} else {
	$class = "seo-fail";
}

//get site average for pages/items
$site_avg = 0;
foreach($records_arr as $record){
	$site_avg += $record['seo_score'];
}
$site_avg = number_format(($site_avg/count($records_arr)),0);
if($site_avg > 80){
	$avg_class = "seo-pass";
} else if($site_avg >= 50 && $site_avg <= 80){
	$avg_class = "seo-average";
} else {
	$avg_class = "seo-fail";
}

echo "<div class='graph f_left'>
<div class='radial-progress p".number_format($overall_score,0)." $class'>
	<div class='circle animate'>
		<div class='mask full'>
			<div class='fill'></div>
		</div>
		<div class='mask half'>
			<div class='fill'></div>
			<div class='fill fix'></div>
		</div>
		<div class='shadow'></div>
	</div>
	<div class='inset'>
		<div class='percentage'>
			<div class='numbers'>
				<span>$overall_score</span>
			</div>
		</div>
	</div>
	<div class='radial-progress site-avg p$site_avg $avg_class'>
		<div class='circle animate'>
			<div class='mask full'>
				<div class='fill'></div>
			</div>
			<div class='mask half'>
				<div class='fill'></div>
				<div class='fill fix'></div>
			</div>
			<div class='shadow'></div>
		</div>
	</div>
</div>
<p class='center uppercase'>Overall Score<br/><small>Site Avg: $site_avg</small></p>
</div>";

$summarize = $Analyzer->get_summary();
echo "<ul class='seo-summary f_left'>";
//sort summary items by most concerning
usort($summarize, function($a, $b) {
    return $a['grade'] - $b['grade'];
});
foreach($summarize as $summary){
	echo "<li class='seo-".$summary['grade']."'>".$summary['message']."</li>";
}
echo "</ul>";
	
?>