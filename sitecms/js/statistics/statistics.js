/* 
	STEPS TO CONFIGURE 
	1. Create a Client ID
	2. Get View ID of Website (Admin > View > View Settings > View ID)
*/

var client_id = '821128475243-bfugc161b7o2sdbbsod1f9k86ob5vdmq.apps.googleusercontent.com';
var view_id = '18897992';

var mainData = new Object();
var stats_shown = false;

// == NOTE ==
// This code uses ES6 promises. If you want to use this code in a browser
// that doesn't supporting promises natively, you'll have to include a polyfill.

gapi.analytics.ready(function() {

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

	// Create a new ActiveUsers instance to be rendered inside of an element with the id "active-users-count" and poll for changes every xx seconds.
	var activeUsers = new gapi.analytics.ext.ActiveUsers({
		container: 'active-users-count',
		pollingInterval: 30
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
		$('#daterange-selector').css({ 'padding-left' : 0, 'border' : 0 });
	}


	// Query params representing the first chart's date range.
	var dateRange1 = {
		'start-date': moment().subtract(1, 'month').day(0).format('YYYY-MM-DD'),
		'end-date':  moment().day(0).format('YYYY-MM-DD')
	};


	// Create a new DateRangeSelector instance to be rendered inside of an element with the id "date-range-selector-1-container", set its date range and then render it to the page.
	var dateRangeSelector1 = new gapi.analytics.ext.DateRangeSelector({
		container: 'daterange-selector'
	})
	.set(dateRange1)
	.execute();


	// Initialize jquery UI datepicker
	$( 'input[type="date"]' ).datepicker({
		prevText: '<i class="fa fa-chevron-circle-left"></i>',
        nextText: '<i class="fa fa-chevron-circle-right"></i>',
		dateFormat: 'yy-mm-dd',
		dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
		numberOfMonths: 1
	});


	// Update the activeUsers component, the Chartjs charts, and the dashboard title whenever the user changes the view.
	viewSelector.on('viewChange', function(data) {

		// Display Date Range Selector and Charts Wrapper
		if(!stats_shown) {
			var daterange = document.getElementById('daterange-selector');
			var allcharts = document.getElementById('allcharts-wrapper');

			daterange.style.display = 'block';
			allcharts.style.display = 'block';

			stats_shown = true;
		}

		mainData = data;

		var title = document.getElementById('view-name');
		title.innerHTML = data.property.name + ' (' + data.view.name + ')';

		// Start tracking active users for this view.
		activeUsers.set(data).execute();

		// Render all the of charts for this view.
		renderTotalOverview();
		renderChartVisits();
		renderCustomChart('traffic-sources', 'source', 10);
		renderCustomChart('keyword-sources', 'keyword', 10);
		renderCustomChart('popular-pages', 'pagePath', 10);
		renderVisitDuration();
		renderTopCities(10);
		renderTopBrowsers(10);
	});

	// If viewSelector has any errors, alert user (eg: if view_id is set and they don't have permissions)
	viewSelector.on('error', function(data) {
		alert(data.message);
	});

	/**
	* Register a handler to run whenever the user changes the date range from
	* the first datepicker. The handler will update the first dataChart
	* instance as well as change the dashboard subtitle to reflect the range.
	*/
	dateRangeSelector1.on('change', function(data) {
		dateRange1['start-date'] = data['start-date'];
		dateRange1['end-date'] = data['end-date'];

		// Re-render all charts with changed date range
		renderTotalOverview();
		renderChartVisits();
		renderCustomChart('traffic-sources', 'source', 10);
		renderCustomChart('keyword-sources', 'keyword', 10);
		renderCustomChart('popular-pages', 'pagePath', 10);
		renderVisitDuration();
		renderTopCities(10);
		renderTopBrowsers(10);
	});

	// Register a handler to run whenever limit-selector (dropdown menu for max. limit of results to return)
	$('.limit-selector').on('change', function() {
		if($(this).data('id') != 'cities') {
			renderCustomChart($(this).data('id'), $(this).data('dimension'), $(this).val());
		} else {
			renderTopCities($(this).val());
		}
	});


	function renderTotalOverview() {
		var thisQuery = query({
		  'ids': mainData.ids,
		  'metrics': 'ga:visits,ga:pageviews,ga:pageviewspervisit,ga:avgtimeonsite,ga:percentnewvisits,ga:visitbouncerate',
		  'start-date': dateRange1['start-date'],
		  'end-date': dateRange1['end-date']
		});

		Promise.all([thisQuery]).then(function(results) {
			var total_results = results[0].totalsForAllResults;

			$('#visits-count').html( total_results['ga:visits'] );
			$('#page-visits-count').html( parseFloat(total_results['ga:pageviewspervisit']).toFixed(2) );
			$('#avg-time-count').html( total_results['ga:avgtimeonsite'].toHHMMSS() );
			$('#new-visits-count').html( parseFloat(total_results['ga:percentnewvisits']).toFixed(2) + '%' );
			$('#bounce-rate-count').html( parseFloat(total_results['ga:visitbouncerate']).toFixed(2) + '%' );
		});
	}


	// Render chart 1
	function renderChartVisits() {

		var thisVisits = query({
		  'ids': mainData.ids,
		  'dimensions': 'ga:date,ga:nthDay',
		  'metrics': 'ga:visits',
		  'start-date': dateRange1['start-date'],
		  'end-date': dateRange1['end-date']
		});

		var thisPageViews = query({
		  'ids': mainData.ids,
		  'dimensions': 'ga:date,ga:nthDay',
		  'metrics': 'ga:pageviews',
		  'start-date': dateRange1['start-date'],
		  'end-date': dateRange1['end-date']
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

	/**
	*	Render custom line chart according to dimension
	*	(renders a chart with pageviews per visit and visits)
	*	(renders table overview under chart)

	*	@ name - name used in the id (prepended by table/chart/label)
	*	@ dimension - source/keyword (without the ga:)
	*	@ max_results - Limit number of results to get
	*/
	function renderCustomChart(id_name, dimension, max_results) {

		var thisQuery = query({
		  'ids': mainData.ids,
		  'dimensions': 'ga:' + dimension,
		  'metrics': 'ga:visits,ga:pageviews,ga:pageviewspervisit,ga:avgtimeonsite',
		  'sort': '-ga:visits',
		  'start-date': dateRange1['start-date'],
		  'end-date': dateRange1['end-date'],
		  'max-results': max_results
		});
		Promise.all([thisQuery]).then(function(results) {

			if(results[0].rows.length < max_results) {
				max_results = results[0].rows.length;
			}

			// store results in object/array
			var alldata = [];
			for(i = 0; i < results[0].rows.length; i++) {
				var newdata = {
					'0' : results[0].rows[i][0], // dimension
					'1' : results[0].rows[i][1], // visits
					'2' :  results[0].rows[i][2], // pageviews
					'3' : results[0].rows[i][3], // pageviewspervisit
					'4' : results[0].rows[i][4] // avgtimeonsite
				}

				alldata.push(newdata);
			}

			var data1 = results[0].rows.map(function(row) { return +row[1]; }); // visits
			var data2 = results[0].rows.map(function(row) { return +parseFloat(row[3]).toFixed(2) ; }); // pageviews per visit

			var labels = [];
			for(var i = 1; i <= max_results; i++) {
				labels.push(i);
			}

			// Ensure the data arrays are at least as long as the labels array.
			// Chart.js bar charts don't (yet) accept sparse datasets.
			for (var i = 0, len = labels.length; i < len; i++) {
			if (data1[i] === undefined) data1[i] = null;
			if (data2[i] === undefined) data2[i] = null;
			}

			var data = {
			labels : labels,
			datasets : [
			  {
			    label: 'Visits',
			    fillColor : "rgba(60,108,70,0.5)",
			    strokeColor : "rgba(60,108,70,1)",
			    pointColor : "rgba(60,108,70,1)",
			    pointStrokeColor : "#fff",
			    data : data1
			  },
			    {
			      label: 'Pages/Visit',
			      fillColor : "rgba(181,162,122,0.5)",
			      strokeColor : "rgba(181,162,122,1)",
			      pointColor : "rgba(181,162,122,1)",
			      pointStrokeColor : "#fff",
			      data : data2
			    }
			]
			};

			new Chart(makeCanvas('chart-' + id_name)).Line(data);
			generateLegend('legend-' + id_name, data.datasets);

			// create table overview
			var table_el = document.getElementById('table-' + id_name);
			var tbody_el = table_el.getElementsByTagName("TBODY")[0];

			var table_content = '';
			for(var i = 0; i < alldata.length; i++) {
				table_content += '<tr class="row'+ (!(i%2) ? '1' : '2') +'">';
					table_content += '<td><p><strong>'+ (i + 1) +'</strong></p></td>';
					table_content += '<td><p>'+ alldata[i][0] +'</p></td>';
					table_content += '<td><p>'+ alldata[i][1] +'</p></td>';
					table_content += '<td><p>'+ parseFloat(alldata[i][3]).toFixed(2) +'</p></td>';
					table_content += '<td><p>'+ parseFloat(alldata[i][4]).toFixed(2) +'</p></td>';
				table_content += '</tr>';
			}
			tbody_el.innerHTML = table_content;

		})
		.catch(function(err) {
		  console.error(err.stack);
		})
	}

	// Render chart of Length of Visits
	function renderVisitDuration() {

		var group_count = 7;
		var group = [];

		for(i = 0; i < group_count; i++) {
			group[i] = {
				'visits' : 0,
				'pageviews' : 0
			};
		}

		var thisQuery = query({
		  'ids': mainData.ids,
		  'dimensions': 'ga:visitLength',
		  'metrics': 'ga:visits,ga:pageviews,ga:pageviewspervisit,ga:avgtimeonsite',
		  'sort': '-ga:visits',
		  'start-date': dateRange1['start-date'],
		  'end-date': dateRange1['end-date']
		});
		Promise.all([thisQuery]).then(function(results) {

			results[0].rows.forEach(function(row, i) {
				var this_duration = parseFloat(row[0]);
				var group_no;

				if (this_duration >= 0 && this_duration <= 10) {
					group_no = 0;
				} else if (this_duration > 10 && this_duration <= 30) {
					group_no = 1;
				} else if (this_duration > 30 && this_duration <= 60) {
					group_no = 2;
				} else if (this_duration > 60 && this_duration <= 180) {
					group_no = 3;
				} else if (this_duration > 180 && this_duration <= 600) {
					group_no = 4;
				} else if (this_duration > 600 && this_duration <= 1800) {
					group_no = 5;
				} else {
					group_no = 6;
				}

				group[group_no].visits += parseFloat(row[1]);
				group[group_no].pageviews += parseFloat(row[2]);

			});

			var data1 = group.map(function(property) { return +property['visits']; });
			var data2 = group.map(function(property) { return +property['pageviews']; });

			var labels = ['0-10', '11-30', '31-60', '61-180', '181-600', '601-1800', '1801+'];

			var data = {
			labels : labels,
			datasets : [
			  {
			    label: 'Visits',
			    fillColor : "rgba(60,108,70,0.5)",
			    strokeColor : "rgba(60,108,70,1)",
			    pointColor : "rgba(60,108,70,1)",
			    pointStrokeColor : "#fff",
			    data : data1
			  },
			    {
			      label: 'Pages/Visit',
			      fillColor : "rgba(181,162,122,0.5)",
			      strokeColor : "rgba(181,162,122,1)",
			      pointColor : "rgba(181,162,122,1)",
			      pointStrokeColor : "#fff",
			      data : data2
			    }
			]
			};

			new Chart(makeCanvas('chart-visit-duration')).Line(data);
			generateLegend('legend-visit-duration', data.datasets);

			// create table overview
			var table_el = document.getElementById('table-visit-duration');
			var tbody_el = table_el.getElementsByTagName("TBODY")[0];

			var table_content = '';
			group.forEach(function(row, i) {
				table_content += '<tr class="row'+ (!(i%2) ? '1' : '2') +'">';
					table_content += '<td><p><strong>'+ (i + 1) +'</strong></p></td>';
					table_content += '<td><p>'+ labels[i] +' seconds</p></td>';
					table_content += '<td><p>'+ row['visits'] +'</p></td>';
					table_content += '<td><p>'+ parseFloat(row['pageviews']).toFixed(0) +'</p></td>';
				table_content += '</tr>';
			});
			tbody_el.innerHTML = table_content;

		})
		.catch(function(err) {
			console.error(err.stack);
		})
	}


	function renderTopCities(max_results) {
		var thisQuery = query({
		  'ids': mainData.ids,
		  'dimensions': 'ga:city',
		  'metrics': 'ga:visits,ga:pageviews,ga:pageviewspervisit,ga:avgtimeonsite',
		  'sort': '-ga:visits',
		  'start-date': dateRange1['start-date'],
		  'end-date': dateRange1['end-date'],
		  'max-results': max_results
		});
		Promise.all([thisQuery]).then(function(results) {

			if(results[0].rows.length < max_results) {
				max_results = results[0].rows.length;
			}

			// store results in object/array
			var alldata = [];
			for(i = 0; i < results[0].rows.length; i++) {
				alldata.push({
					'name' : results[0].rows[i][0],
					'count' : results[0].rows[i][1],
					'0' : results[0].rows[i][0], // dimension
					'1' : results[0].rows[i][1], // visits
					'2' :  results[0].rows[i][2], // pageviews
					'3' : results[0].rows[i][3], // pageviewspervisit
					'4' : results[0].rows[i][4] // avgtimeonsite
				});
			}

			// create table overview
			var table_el = document.getElementById('table-cities');
			var tbody_el = table_el.getElementsByTagName("TBODY")[0];

			var table_content = '';
			for(var i = 0; i < alldata.length; i++) {
				table_content += '<tr class="row'+ (!(i%2) ? '1' : '2') +'">';
					table_content += '<td><p><strong>'+ (i + 1) +'</strong></p></td>';
					table_content += '<td><p>'+ alldata[i][0] +'</p></td>';
					table_content += '<td><p>'+ alldata[i][1] +'</p></td>';
					table_content += '<td><p>'+ parseFloat(alldata[i][3]).toFixed(2) +'</p></td>';
					table_content += '<td><p>'+ parseFloat(alldata[i][4]).toFixed(2) +'</p></td>';
				table_content += '</tr>';
			}
			tbody_el.innerHTML = table_content;

			// load map
			google.load('visualization', '1', {
				packages: ['geomap'], 
				callback: function() {
					var mapdata = new google.visualization.DataTable();

					mapdata.addRows(alldata.length);
					mapdata.addColumn('string', 'City');
					mapdata.addColumn('number', 'Popularity');

					$(alldata).each(function(key, loc) {
						mapdata.setValue(key, 0, loc.name);
						mapdata.setValue(key, 1, loc.count);
					});

					var options = {};
					options['dataMode'] = 'markers';
					options['width'] = '100%';
					options['height'] = '100%';
					options['colors'] = [0x888888, 0x666666, 0x444444, 0x222222]; 

					var container = document.getElementById('chart-cities');
					var geomap = new google.visualization.GeoMap(container);
					geomap.draw(mapdata, options);
				}
			});

		})
		.catch(function(err) {
			console.error(err.stack);
		})
	}

	// Render Doughnut Chart for Browsers Stats
	function renderTopBrowsers(max_results) {
		query({
		  'ids': mainData.ids,
		  'dimensions': 'ga:browser',
		  'metrics': 'ga:visits,ga:pageviews,ga:pageviewspervisit,ga:avgtimeonsite',
		  'sort': '-ga:visits',
		  'max-results': 10,
		  'start-date': dateRange1['start-date'],
		  'end-date': dateRange1['end-date']
		})
		.then(function(response) {
			var alldata = [];
			var data = [];
			var colors = ['#3C6C46','#333333','#5D8467','#CCCCCC','#B5A27A','#666666','#6d9174','#999999','#86a38d','#c8b99b'];

			response.rows.forEach(function(row, i) {
				alldata.push({
					'0' : row[0], // dimension
					'1' : row[1], // visits
					'2' : row[2], // pageviews
					'3' : row[3], // pageviewspervisit
					'4' : row[4] // avgtimeonsite
				});

				data.push({
					label: row[0],
					value: +row[1],
					color: colors[i]
				});
			});

			new Chart(makeCanvas('chart-browsers')).Doughnut(data);
			generateLegend('legend-browsers', data);

			// create table overview
			var table_el = document.getElementById('table-browsers');
			var tbody_el = table_el.getElementsByTagName("TBODY")[0];

			var table_content = '';
			for(var i = 0; i < alldata.length; i++) {
				table_content += '<tr class="row'+ (!(i%2) ? '1' : '2') +'">';
					table_content += '<td><p><strong>'+ (i + 1) +'</strong></p></td>';
					table_content += '<td><p>'+ alldata[i][0] +'</p></td>';
					table_content += '<td><p>'+ alldata[i][1] +'</p></td>';
					table_content += '<td><p>'+ parseFloat(alldata[i][3]).toFixed(2) +'</p></td>';
					table_content += '<td><p>'+ parseFloat(alldata[i][4]).toFixed(2) +'</p></td>';
				table_content += '</tr>';
			}
			tbody_el.innerHTML = table_content;
		});
	}


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