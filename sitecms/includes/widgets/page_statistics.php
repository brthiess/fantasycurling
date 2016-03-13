<script>
(function(w,d,s,g,js,fs){
  g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
  js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
  js.src='https://apis.google.com/js/platform.js';
  fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
}(window,document,'script'));
</script>

<div id='statistics-wrapper' class='clearfix'>
	<div id='statistics-header' class='clearfix'>
		<h2 id='view-name'></h2>
		<div id='embed-api-auth-container' class='clearfix'></div>
		<div id='view-selector-container' class='clearfix input-wrapper'></div>
		<div id='daterange-selector' class='clearfix input-wrapper' style='display:none;'></div>
	</div>

	<div id='allcharts-wrapper' style='display:none;'>
		
		<div class='panel'>
			<div class='panel-header'>Page Statistics &nbsp; <small>Source: Google Analytics</small>
				<span class='f_right'><a class='panel-toggle fa fa-chevron-down'></a></span>
			</div>
			<div id='main-stats-holder' class='panel-content page-stats closed clearfix'>
		        <table id='statistics-overview' width='100%' cellspacing='0' cellpadding='15' border='0' data-pageurl='<?php echo $p ?>'>
		            <tbody>
		                <tr>
			                <td><div>Avg. Page Speed <?php echo $CMSBuilder->tooltip('Avg. Page Speed', 'The average amount of time (in seconds) it takes this page to load, from initiation of the pageview (e.g., click on a page link) to load completion in the browser.'); ?> <b id='page-speed'></b><br/><small>Site Avg: <span id='total-page-speed'></span></small></div></td>
		                    <td><div>Page Views <?php echo $CMSBuilder->tooltip('Page Views', 'The total number of visits over the selected dimension. A visit consists of a single-user session.'); ?> <b id='visits-count'></b><br/><small>Site Avg: <span id='total-visits-count'></span></small></div></td>
		                    <td><div>Bounce Rate <?php echo $CMSBuilder->tooltip('Bounce Rate', 'The percentage of single-page visits (i.e., visits in which the person left your site from the first page).'); ?> <b id='bounce-rate-count'></b><br/><small>Site Avg: <span id='total-bounce-rate-count'></span></small></div></td>
		                    <td><div>Avg. Time on Page <?php echo $CMSBuilder->tooltip('Avg. Time on Page', 'The average amount of time visitors spent viewing this page.'); ?> <b id='avg-time-count'></b><br/><small>Site Avg: <span id='total-avg-time-count'></span></small></div></td>
		                    <td><div>Exit % <?php echo $CMSBuilder->tooltip('Exit %', 'For all pageviews to the page, Exit Rate is the percentage that were the last in the session.'); ?> <b id='exit-rate-count'></b><br/><small>Site Avg: <span id='total-exit-rate-count'></span></small></div></td>
		                </tr>
		            </tbody>
		        </table>
				<div class='chart-container full'>
		            <!-- Chart 1 - Overall Views and Visits -->
		            <div id='chart-visits' class='full' style='height:300px;'></div>
		            <ul id='legend-visits' class='legend-container'></ul>
		        </div>
			</div>
		</div>		
	</div><!-- /#allcharts-wrapper -->
</div><!-- /#statistics-wrapper -->

<script src='<?php echo $path; ?>js/statistics/Chart.min.js'></script>
<script src='<?php echo $path; ?>js/statistics/chart_helpers.js'></script>
<script src='<?php echo $path; ?>js/statistics/moment.min.js'></script>

<script src='<?php echo $path; ?>js/statistics/embed-api/view-selector2.js'></script>
<script type='text/javascript' src='https://www.google.com/jsapi'></script>
<script src='<?php echo $path; ?>js/statistics/statistics_page.js'></script>