<?php
//get last 30 days
$d = array();
for($i = 0; $i < 30; $i++) 
    $d[] = date("Y-m-d", strtotime('-'. $i .' days'));

$d = array_reverse($d);

$data = array();

//set default data array
$params = array("inquiry");
$get_enumopts = $db->query("SHOW COLUMNS FROM `inquiries` WHERE Field = ?",$params);
if($get_enumopts) {
	$opts = $db->fetch_array();
	$opts = explode("','",preg_replace("/(enum|set)\('(.+?)'\)/","\\2", $opts[0]['Type']));
	foreach($opts as $inquiry_type){
		$data[$inquiry_type] = array();
	}
}

//get inquiries
$params = array(date("Y-m-d",strtotime("-30 days")).' 00:00:00',date("Y-m-d").' 23:59:59');
$query = $db->query("SELECT * FROM inquiries WHERE timestamp BETWEEN ? AND ?",$params);
$result = $db->fetch_array();
foreach($result as $stat){
	@$data[$stat['inquiry']][date("Y-m-d",strtotime($stat['timestamp']))] += 1;
}
?>
<div id='statistics-wrapper' class='clearfix'>
    <div class='panel'>
		<div class='panel-header'>Conversions in Last 30 Days
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>
		<div id="main-stats-holder" class='panel-content clearfix'>
			<table id="statistics-overview" width="100%" cellspacing="0" cellpadding="15" border="0">
	            <tbody>
	                <tr>
		                <?php
		                foreach($data as $label => $array){
							echo "<td class='center'><div>Total<br/> ".$label." <b>".array_sum($array)."</b></div></td>";
						}
		                ?>
	                </tr>
	            </tbody>
	        </table>	
			<div class='chart-container full'>
	            <!-- Chart 1 - Overall Views and Visits -->
	            <div id='chart-basic-line' class='nopadding nomargin' style='height:150px;'></div>
	            <ul id='legend' class='legend-container'></ul>
	        </div>
		</div>
	</div>
</div>

<script src='<?php echo $path; ?>js/statistics/Chart.min.js'></script>
<script src='<?php echo $path; ?>js/statistics/chart_helpers.js'></script>
<script src='<?php echo $path; ?>js/statistics/moment.min.js'></script>
<script type='text/javascript'> 
var labels = <?php echo json_encode($d); ?>;
labels = labels.map(function(label) {
	return moment(label, 'YYYYMMDD').format('MM/DD');
});
var data = {
	labels : labels,
	datasets : [
		<?php 
		$set_count = 0;
		foreach($data as $type => $dataset){
			//set data
			foreach($d as $date){
				if(!isset($dataset[$date])){
					$dataset[$date] = 0;
				}
			}
			ksort($dataset);
			
			echo "{
		      label: '$type',
		      fillColor : 'rgba('+fillColors[$set_count]+',0.5)',
		      strokeColor : 'rgba('+fillColors[$set_count]+',1)',
		      pointColor : 'rgba('+fillColors[$set_count]+',1)',
		      pointStrokeColor : '#fff',
		      type: 'line',
		      data : ".json_encode(array_values($dataset))."
		    }".($set_count == (count($data)-1) ? "" : ",");
		    $set_count++;
		}
		?>
	]
};

new Chart(makeCanvas('chart-basic-line')).Line(data);
generateLegend('legend', data.datasets);  
</script>

<?php

//get SEO average
if($cms_settings['enhanced_seo'] && (array_key_exists(3, $Account->roles) || array_key_exists(4, $Account->roles))){ //SEO and master permissions
	$query = $db->query("SELECT AVG(`seo_score`) as `site_avg` FROM `pages` WHERE `type` = 0 AND `page_id` > 2");
	if($query && !$db->error()){
		$result = $db->fetch_array();
		$site_avg = number_format($result[0]['site_avg'],0);
		if($site_avg > 80){
			$avg_class = "seo-pass";
		} else if($site_avg >= 50 && $site_avg <= 80){
			$avg_class = "seo-average";
		} else {
			$avg_class = "seo-fail";
		}
?>

<div class='dashboard-box graph f_left'>
	<div class='radial-progress p<?php echo $site_avg; ?> <?php echo $avg_class; ?>'>
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
					<span><?php echo $site_avg; ?></span>
				</div>
			</div>
		</div>
	</div>
	<p class='center uppercase'>Site SEO Avg</p>
</div>

<?php }
}//seo 

?>

<br class='clear'/>