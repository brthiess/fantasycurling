<div id='statistics-wrapper' class='clearfix'>
    <div class='panel'>
		<div class='panel-header'>Inquiries in Last 30 Days
			<span class='f_right'><a class='panel-toggle fa fa-chevron-up'></a></span>
		</div>
		<div class='panel-content clearfix'>				
			<div class='chart-container full'>
	            <!-- Chart 1 - Overall Views and Visits -->
	            <div id='chart-basic-line' class='nopadding nomargin' style='height:200px;'></div>
	            <ul id='legend' class='legend-container'></ul>
	        </div>
		</div>
	</div>
</div>


<script src='<?php echo $path; ?>js/statistics/Chart.min.js'></script>
<script src='<?php echo $path; ?>js/statistics/chart_helpers.js'></script>
<script src='<?php echo $path; ?>js/statistics/moment.min.js'></script>
<script type='text/javascript'>
<?php
//get last 30 days
$d = array();
for($i = 0; $i < 30; $i++) 
    $d[] = date("Y-m-d", strtotime('-'. $i .' days'));

$d = array_reverse($d);

//get inquiries
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

$params = array(date("Y-m-d 00:00:00",strtotime("-30 days")),date("Y-m-d 23:59:59"));
$query = $db->query("SELECT * FROM inquiries WHERE timestamp BETWEEN ? AND ?",$params);
$result = $db->fetch_array();
foreach($result as $stat){
	@$data[$stat['inquiry']][date("Y-m-d",strtotime($stat['timestamp']))] += 1;
}
?>
 
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