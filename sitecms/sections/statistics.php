<script>
(function(w,d,s,g,js,fs){
  g=w.gapi||(w.gapi={});g.analytics={q:[],ready:function(f){this.q.push(f);}};
  js=d.createElement(s);fs=d.getElementsByTagName(s)[0];
  js.src='https://apis.google.com/js/platform.js';
  fs.parentNode.insertBefore(js,fs);js.onload=function(){g.load('analytics');};
}(window,document,'script'));
</script>

<div id="statistics-wrapper" class="clearfix">
    <div id="statistics-header" class="clearfix">
        <h2 id="view-name" class="nomargin"></h2>
        <div id="embed-api-auth-container" class="clearfix"></div>
        <div id="view-selector-container" class="clearfix input-wrapper"></div>

        <div id="daterange-selector" class="clearfix input-wrapper" style="display:none;"></div>
    </div>

    <div id="allcharts-wrapper" style="display:none;">

        <table id="statistics-overview" width="100%" cellspacing="0" cellpadding="15" border="0">
            <tbody>
                <tr>
                    <td id="active-users-count"></td>
                    <td><div>Visits <sup title='<span>Visits</span><p>The total number of visits over the selected dimension. A visit consists of a single-user session.</p>'>?</sup> <b id="visits-count"></b></div></td>
                    <td><div>Pages/Visit <sup title='<span>Pages/Visit</span><p>The average number of pages viewed during a visit to your site. Repeated views of a single page are counted.</p>'>?</sup> <b id="page-visits-count"></b></div></td>
                    <td><div>Avg. Time on Site <sup title='<span>Avg. Time On Site</span><p>The average amount of time visitors spent viewing this page or a set of pages.</p>'>?</sup> <b id="avg-time-count"></b></div></td>
                    <td><div>% New Visits <sup title='<span>% New Visits</span><p>The percentage of visits by people who had never visited your site before.</p>'>?</sup> <b id="new-visits-count"></b></div></td>
                    <td><div>Bounce Rate <sup title='<span>Bounce Rate</span><p>The percentage of single-page visits (i.e., visits in which the person left your site from the first page).</p>'>?</sup> <b id="bounce-rate-count"></b></div></td>
                </tr>
            </tbody>
        </table>

        <div class="chart-container full">
            <!-- Chart 1 - Overall Views and Visits -->
            <div class="chart-header" style="text-align:center;">
                <h3 class="chart-title">Website Statistics</h3>
                <h4 class="chart-subtitle">Source: Google Analytics</h4>
            </div>
            <div id="chart-visits" class="full" style="height:300px;"></div>
            <ul id="legend-visits" class="legend-container"></ul>
        </div>
        
        <div class="clearfix full">
            <div class="chart-container" style="width:49%; float:left; margin-right:2%;">
                <!-- Chart 2 - Traffic Sources -->
                <div class="chart-header clearfix">
                    <h3 class="chart-title f_left">Traffic Sources</h3>
                    <select id="select-traffic-sources" class="limit-selector f_right select" data-id="traffic-sources" data-dimension="source" style="width:auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div id="chart-traffic-sources" class="full" style="height:200px;"></div>
                <ul id="legend-traffic-sources" class="legend-container"></ul>
                <table id="table-traffic-sources" class="table-overview" width="100%" cellpadding="2" cellspacing="0" border="0">
                    <thead>
                        <tr>
                            <th><p><strong></strong></p></th>
                            <th><p><strong>Referrer</strong></p></th>
                            <th><p><strong>Visits</strong></p></th>
                            <th><p><strong>Pages/Visits</strong></p></th>
                            <th width="80px"><p><strong>Avg. Time</strong></p></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            
            <div class="chart-container" style="width:49%; float:left;">
                <!-- Chart 3 - Keyword Sources -->
                <div class="chart-header clearfix">
                    <h3 class="chart-title f_left">Keyword Sources</h3>
                    <select id="select-keyword-sources" class="limit-selector f_right select" data-id="keyword-sources" data-dimension="keyword" style="width:auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div id="chart-keyword-sources" class="full" style="height:200px;"></div>
                <ul id="legend-keyword-sources" class="legend-container"></ul>
                <table id="table-keyword-sources" class="table-overview" width="100%" cellpadding="2" cellspacing="0" border="0">
                    <thead>
                        <tr>
                            <th><p><strong></strong></p></th>
                            <th><p><strong>Keyword</strong></p></th>
                            <th><p><strong>Visits</strong></p></th>
                            <th><p><strong>Pages/Visits</strong></p></th>
                            <th width="80px"><p><strong>Avg. Time</strong></p></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="clearfix full">
            <div class="chart-container" style="width:49%; float:left; margin-right:2%;">
                <!-- Chart 4 - Popular Pages -->
                <div class="chart-header clearfix">
                    <h3 class="chart-title f_left">Popular Pages</h3>
                    <select id="select-popular-pages" class="limit-selector f_right select" data-id="popular-pages" data-dimension="pagePath" style="width:auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div id="chart-popular-pages" class="full" style="height:200px;"></div>
                <ul id="legend-popular-pages" class="legend-container"></ul>
                <table id="table-popular-pages" class="table-overview" width="100%" cellpadding="2" cellspacing="0" border="0">
                    <thead>
                        <tr>
                            <th><p><strong></strong></p></th>
                            <th><p><strong>Page</strong></p></th>
                            <th><p><strong>Visits</strong></p></th>
                            <th><p><strong>Pages/Visits</strong></p></th>
                            <th width="80px"><p><strong>Avg. Time</strong></p></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="chart-container" style="width:49%; float:left;">
                <!-- Chart 5 - Length of Visit -->
                <div class="chart-header">
                    <h3 class="chart-title">Length of Visit <small class="chart-subtitle">(in seconds)</small></h3>
                </div>
                <div id="chart-visit-duration" class="full" style="height:200px;"></div>
                <ul id="legend-visit-duration" class="legend-container"></ul>
                <table id="table-visit-duration" class="table-overview" width="100%" cellpadding="2" cellspacing="0" border="0">
                    <thead>
                        <tr>
                            <th><p><strong></strong></p></th>
                            <th><p><strong>Duration of Visit</strong></p></th>
                            <th><p><strong>Visits</strong></p></th>
                            <th><p><strong>Pages/Visits</strong></p></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="clearfix full">
            <div class="chart-container" style="width:49%; float:left; margin-right:2%;">
                <!-- Chart 6 - Cities -->
                <div class="chart-header clearfix">
                    <h3 class="chart-title f_left">Cities</h3>
                    <select id="select-cities" class="limit-selector f_right select" data-id="cities" data-dimension="city" style="width:auto;">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div id="chart-cities" class="full" style="height:275px;"></div>
                <ul id="legend-cities" class="legend-container"></ul>
                <table id="table-cities" class="table-overview" width="100%" cellpadding="2" cellspacing="0" border="0">
                    <thead>
                        <tr>
                            <th><p><strong></strong></p></th>
                            <th><p><strong>City</strong></p></th>
                            <th><p><strong>Visits</strong></p></th>
                            <th><p><strong>Pages/Visits</strong></p></th>
                            <th width="80px"><p><strong>Avg. Time</strong></p></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="chart-container" style="width:49%; float:left;">
                <!-- Chart 7 - Browsers -->
                <div class="chart-header">
                    <h3 class="chart-title">Browsers</h3>
                </div>
                <div id="chart-browsers" class="full" style="height:200px;"></div>
                <ul id="legend-browsers" class="legend-container" style="min-height:74px;"></ul>
                <table id="table-browsers" class="table-overview" width="100%" cellpadding="2" cellspacing="0" border="0">
                    <thead>
                        <tr>
                            <th><p><strong></strong></p></th>
                            <th><p><strong>Browser</strong></p></th>
                            <th><p><strong>Visits</strong></p></th>
                            <th><p><strong>Pages/Visits</strong></p></th>
                            <th width="80px"><p><strong>Avg. Time</strong></p></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div><!-- /#allcharts-wrapper -->
</div><!-- /#statistics-wrapper -->

<script src="<?php echo $path; ?>js/statistics/Chart.min.js"></script>
<script src="<?php echo $path; ?>js/statistics/chart_helpers.js"></script>
<script src="<?php echo $path; ?>js/statistics/moment.min.js"></script>

<script src="<?php echo $path; ?>js/statistics/embed-api/view-selector2.js"></script>
<script src="<?php echo $path; ?>js/statistics/embed-api/date-range-selector.js"></script>
<script src="<?php echo $path; ?>js/statistics/embed-api/active-users.js"></script>

<script type='text/javascript' src='https://www.google.com/jsapi'></script>

<script src="<?php echo $path; ?>js/statistics/statistics.js"></script>