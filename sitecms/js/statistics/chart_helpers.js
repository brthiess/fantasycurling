var fillColors = ['60,108,70','51,51,51','#5D8467','93,132,103','181,162,122','102,102,102','109,145,116','153,153,153','134,163,141','200,185,155'];

/**
* Create a new canvas inside the specified element. Set it to be the width
* and height of its container.
* @param {string} id The id attribute of the element to host the canvas.
* @return {RenderingContext} The 2D canvas context.
*/
function makeCanvas(id) {
	var container = document.getElementById(id);
	var canvas = document.createElement('canvas');
	var ctx = canvas.getContext('2d');

	container.innerHTML = '';
	canvas.width = container.offsetWidth;
	canvas.height = container.offsetHeight;
	container.appendChild(canvas);
	
	return ctx;
}


/**
* Create a visual legend inside the specified element based off of a
* Chart.js dataset.
* @param {string} id The id attribute of the element to host the legend.
* @param {Array.<Object>} items A list of labels and colors for the legend.
*/
function generateLegend(id, items) {
	var legend = document.getElementById(id);
	legend.innerHTML = items.map(function(item) {
	  var color = item.color || item.fillColor;
	  var label = item.label;
	  return '<li><i style="background:' + color + '"></i>' + label + '</li>';
	}).join('');
}

String.prototype.toHHMMSS = function () {
    var sec_num = parseInt(this, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    var time    = hours+':'+minutes+':'+seconds;
    return time;
}

// Set some global Chart.js defaults.
Chart.defaults.global.animationSteps = 60;
Chart.defaults.global.animationEasing = 'easeInOutQuart';
Chart.defaults.global.responsive = true;
Chart.defaults.global.maintainAspectRatio = false;
Chart.defaults.global.multiTooltipTemplate = "<%if (datasetLabel){%><%=datasetLabel%>: <%}%><%= value %>";