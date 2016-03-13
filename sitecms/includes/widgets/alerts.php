<?php 
$system_alert = $CMSBuilder->system_alert();
if(!is_null($system_alert)){
	foreach($system_alert as $alert){
		echo '<div class="system-alert ' .($alert['status'] ? 'success' : 'error'). '">
			<div class="title">' .($alert['status'] ? '<i class="fa fa-check"></i>Success' : '<i class="fa fa-close"></i>Error'). '!</div>
			<div class="message">' .$alert['message']. '</div>
		</div>';
	}
}
?>