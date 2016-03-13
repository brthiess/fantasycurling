//Set External Link Target
var external = RegExp('^((f|ht)tps?:)?//(?!' + location.host + ')');
$("a:not([target])").each(function(){
	//check to see if external link
	if (external.test($(this).attr('href'))){
		$(this).attr('target','_blank');
	}
});

$(document).ready(function(){
	$('#mbl-toggle').click(function(){
		$(this).toggleClass('open');
		$("#nav-list").toggleClass("expanded");
	});
});