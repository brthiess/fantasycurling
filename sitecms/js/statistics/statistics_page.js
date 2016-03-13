/* 
	STEPS TO CONFIGURE 
	1. Create a Client ID
	2. Get View ID of Website (Admin > View > View Settings > View ID)
*/

var client_id = '828433560865-9tgvp3tn9020hckff3hpa9nq21ibp9o5.apps.googleusercontent.com';
var view_id = '100853964';

var mainData = new Object();
var stats_shown = false;

// == NOTE ==
// This code uses ES6 promises. If you want to use this code in a browser
// that doesn't supporting promises natively, you'll have to include a polyfill.

gapi.analytics.ready(function() {
	
	var page_url = $("#statistics-overview").attr("data-pageurl");
	
	/**
	* Authorize the user immediately if the user has already granted access.
	* If no access has been created, render an authorize button inside the
	* element with the ID "embed-api-auth-container".
	*/
	gapi.analytics.auth.authorize({
		container: 'embed-api-auth-container',
		userInfoLabel: 'Logged in as: ',
		clientid: client_id,
	});

	// Add Logout button
	gapi.analytics.auth.on('success', function(response) {
		$('#embed-api-auth-container').append('<small id="logout-wrapper">(<a href="https://accounts.google.com/logout" target="Logout">Logout</a>)</small>');
	});

	
	// Create a new ViewSelector2 instance to be rendered inside of an element with the id "view-selector-container".
	var viewSelector = new gapi.analytics.ext.ViewSelector2({
		container: 'view-selector-container'
	})
	.execute();


	// If view_id is available, auto-select View and remove View Selector and re-style daterange-selector
	if(view_id != '') {
		viewSelector.set({
			ids: 'ga:' + view_id
		});

		$( '#' + viewSelector.get().container ).hide();
	}
	
	// Update the Chartjs charts, and the dashboard title whenever the user changes the view.
	viewSelector.on('viewChange', function(data) {

		// Display Date Range Selector and Charts Wrapper
		if(!stats_shown) {
			var allcharts = document.getElementById('allcharts-wrapper');

			allcharts.style.display = 'block';

			stats_shown = true;
		}

		mainData = data;

		var title = document.getElementById('view-name');
		title.innerHTML = data.property.name + ' (' + data.view.name + ') Page: '+$("#statistics-overview").attr("data-pageurl");

		// Render all the of charts for this view.
		renderTotalOverview();
		renderPageStatistics(page_url);
		renderChartVisits(page_url);
	});

	// If viewSelector has any errors, alert user (eg: if view_id is set and they don't have permissions)
	viewSelector.on('error', function(data) {
		alert(data.message);
	});

	function renderTotalOverview() {
		var thisQuery = query({
		  'ids': mainData.ids,
		  'metrics': 'ga:avgPageLoadTime,ga:pageviews,ga:bounceRate,ga:avgTimeOnPage,ga:exitRate',
		});

		Promise.all([thisQuery]).then(function(results) {
			var total_results = results[0].totalsForAllResults;
			$('#total-page-speed').html( total_results['ga:avgPageLoadTime'].toHHMMSS() );
			$('#total-visits-count').html( total_results['ga:pageviews'] );
			$('#total-avg-time-count').html( total_results['ga:avgTimeOnPage'].toHHMMSS() );
			$('#total-bounce-rate-count').html( parseFloat(total_results['ga:bounceRate']).toFixed(2) + '%' );
			$('#total-exit-rate-count').html( parseFloat(total_results['ga:exitRate']).toFixed(2) + '%' );
		});
	}
		
	function renderPageStatistics(page_url){
		var thisPageQuery = query({
		  'ids': mainData.ids,
		  'metrics': 'ga:avgPageLoadTime,ga:pageviews,ga:bounceRate,ga:avgTimeOnPage,ga:exitRate',
		  'filters': 'ga:pagePath=='+page_url,
		  'dimensions': 'ga:pagePath'
		});

		Promise.all([thisPageQuery]).then(function(results) {
			var page_results = results[0].totalsForAllResults;
			$('#page-speed').html( page_results['ga:avgPageLoadTime'].toHHMMSS() );
			$('#visits-count').html( page_results['ga:pageviews'] );
			$('#avg-time-count').html( page_results['ga:avgTimeOnPage'].toHHMMSS() );
			$('#bounce-rate-count').html( parseFloat(page_results['ga:bounceRate']).toFixed(2) + '%' );
			$('#exit-rate-count').html( parseFloat(page_results['ga:exitRate']).toFixed(2) + '%' );
		});
	}
	
	// Render chart 1
	function renderChartVisits(page_url) {

		var thisVisits = query({
		  'ids': mainData.ids,
		  'metrics': 'ga:visits',
		  'filters': 'ga:pagePath=='+page_url,
		  'dimensions': 'ga:date,ga:nthDay',
		  'start-date': moment().subtract(30, 'days').format('YYYY-MM-DD'),
		  'end-date': moment().format('YYYY-MM-DD')
		});

		var thisPageViews = query({
		  'ids': mainData.ids,
		  'metrics': 'ga:pageviews',
		  'filters': 'ga:pagePath=='+page_url,
		  'dimensions': 'ga:date,ga:nthDay',
		  'start-date': moment().subtract(30, 'days').format('YYYY-MM-DD'),
		  'end-date': moment().format('YYYY-MM-DD')
		});

		Promise.all([thisVisits, thisPageViews]).then(function(results) {
			
		  var data1 = results[0].rows.map(function(row) { return +row[2]; });
		  var data2 = results[1].rows.map(function(row) { return +row[2]; });
		  var labels = results[1].rows.map(function(row) { return +row[0]; });
		  		  
		  labels = labels.map(function(label) {
		    return moment(label, 'YYYYMMDD').format('MM/DD');
		  });

		  var data = {
		    labels : labels,
		    datasets : [
		    {
		      label: 'Visits',
		      label_name: "Sample 1",
		      fillColor : "rgba(60,108,70,0.5)",
		      strokeColor : "rgba(60,108,70,1)",
		      pointColor : "rgba(60,108,70,1)",
		      pointStrokeColor : "#fff",
		      type: "line",
		      data : data1
		    },
		    {
		      label: 'Pageviews',
		      label_name: "Sample 2",
		      fillColor : "rgba(181,162,122,0.5)",
		      strokeColor : "rgba(181,162,122,1)",
		      pointColor : "rgba(181,162,122,1)",
		      pointStrokeColor : "#fff",
		      type: "bar",
		      data : data2
		    }
		    ]
		  };

		  new Chart(makeCanvas('chart-visits')).Line(data);
		  generateLegend('legend-visits', data.datasets);
		});
	}
	
	$('.panel-toggle').click(function(){
		var panel_box = $(this).parents('.panel');
		var panel = $(panel_box).find('.panel-content');		
		var panelStatus = (panel.is(":hidden") || panel.hasClass("closed")) ? true : false;
		if(panelStatus){
			renderTotalOverview();
			renderPageStatistics(page_url);
		}
	});

	/**
	* Extend the Embed APIs `gapi.analytics.report.Data` component to
	* return a promise the is fulfilled with the value returned by the API.
	* @param {Object} params The request parameters.
	* @return {Promise} A promise.
	*/
	function query(params) {
		return new Promise(function(resolve, reject) {
		  var data = new gapi.analytics.report.Data({query: params});
		  data.once('success', function(response) { resolve(response); })
		      .once('error', function(response) { reject(response); })
		      .execute();
		});
	}

});