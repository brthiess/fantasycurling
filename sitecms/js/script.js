//Define Elements
var path = '/fantasycurling/sitecms/';
var menu = $('#cms-menu');
var nav = $('#cms-menu nav');
var content = $('#cms-content');

//Window Size
var minwindow = false;
if($('#section-title').css('position') == 'relative'){
	minwindow = true;
}

//Navigation Menu
$(function(){
			
	//Accordions
	$(menu).find('.accordion').each(function(){
		var activated = false;
		if($(this).hasClass('expanded')){
			activated = 0;
		}
		$(this).accordion({
			collapsible: true, 
			active: activated, 
			animate: 200
		});
	});
	
	//Menu is set to closed
	var menucookie = getCookie('cmsmenu');
	if(menucookie == 'hidden' || minwindow){
		$(menu).removeClass('open').css({left: -$(menu).width()});
		$('.resize').css({marginLeft: 0});
	}
	
	//Search clear
	$(document).on("click","#clear-search",function(){
		$('#search-form input').val(''); 
		document.getElementById("clear-search-form").submit();
	});
	
	//Custom scrollbar
	$(window).load(function(){
		$(nav).mCustomScrollbar({
			theme: 'minimal',
			contentTouchScroll: true,
			scrollInertia: 0
		});
	});
	
});

//Toggle Side Menu
function toggleMenu(){
	if($(menu).hasClass('open')){
		$(menu).removeClass('open').stop().animate({left: -$(menu).width()}, {duration:300, easing: 'easeOutQuad'});
		$('.resize').stop().animate({marginLeft: 0}, {duration:300, easing: 'easeOutQuad'});
		$(content).removeClass('fixed');
		setCookie('cmsmenu', 'hidden', 0);
	}else{
		$(menu).addClass('open').show().stop().animate({left: 0}, {duration:300, easing: 'easeOutQuad'});
		$('.resize').stop().animate({marginLeft: $(menu).width()}, {duration:300, easing: 'easeOutQuad'});
		if(minwindow){
			$(content).addClass('fixed');
			setCookie('cmsmenu', 'hidden', 0);
		}else{
			setCookie('cmsmenu', 'visible', 0);
		}
	}	
}

//System Alerts
$(function(){
	$('.system-alert').each(function(){
		$(this).animate({opacity: 1}, 600);
	});
});

//Toggle Page Content/URL
$(function(){
	$(document).on("change",".toggle-pagecontent",function(){
		$(".page-content, .page-url").stop().slideToggle(300, function(){
			$(".page-content, .page-url").switchClass("hidden","");
		});
	});
});
	
//Toggle Panel
$(function(){
	$('.panel-toggle').click(function(){
		var panel_box = $(this).parents('.panel');
		var panel = $(panel_box).find('.panel-content');
		if($(this).hasClass('fa-chevron-up')){
			$(this).removeClass('fa-chevron-up').addClass('fa-chevron-down');
		}else{
			$(this).removeClass('fa-chevron-down').addClass('fa-chevron-up');
		}
		$(panel).stop().slideToggle(300, function(){
			$(panel).toggleClass("closed");
			if($(panel).find(".gllpLatlonPicker").length > 0){
				$(".gllpLatlonPicker").each(function() {
					(new GMapsLatLonPicker()).init( $(this) );
				});	
			}
			var panelStatus = (panel.is(":hidden") || panel.hasClass("closed")) ? true : false;
			if($('.panel').length > 1){
				$.ajax({
					method: "POST",
					url: path+"js/ajax/save-user-panel.php",
					data: { panel: panel_box.index('.panel'), status: panelStatus, xssid: getCookie("xssid") }
				});
			}
		});
	});
	
	//set default state per user/section
	if($('.panel').length > 1){
		$.ajax({
			method: "POST",
			url: path+"js/ajax/set-user-panels.php",
			data: {xssid: getCookie("xssid")},
			dataType: 'json',
			success: function(data){
				$.each(data,function(key,val){
					$('.panel').eq(val).find('.panel-toggle').removeClass('fa-chevron-up').addClass('fa-chevron-down');
					$('.panel').eq(val).find('.panel-content').stop().slideUp("fast",function(){
						$(this).addClass("closed");
					});
				});
			}
		});	
	}
	
});

//Ajax showhide for table list items
$(function(){
	$(".ajax-showhide").on("click",function(e){
		$this = $(this);
		$.ajax({
			method: "POST",
			url: path+"js/ajax/showhide-item.php",
			data: { table: $this.attr("data-table"), table_id: $this.attr("data-tableid"), item_id: $this.attr("data-itemid"), item_col: $this.attr("data-itemcol"), item_status: $this.is(':checked'), xssid: getCookie("xssid") },
			success: function(data){
				var tblcell = $this.parents('td');
				if($this.is(':checked')){
					$(tblcell).find('.switch-sorter').text('Visible');	
				}else{
					$(tblcell).find('.switch-sorter').text('Hidden');	
				}
				$this.parents('table').trigger("update").trigger("appendCache");
				setMiniAlert(data);
			}
		});
	});
});

//Set Page Status
$(function(){
	$(document).on("click",".page-status button",function(){
		var selected = $(this);
		if(selected.parent().hasClass("no-ajax")){ //new page, set status ready for save
			$("input[name='showhide']").val(selected.val());
			selected.siblings().removeClass("active");
			selected.addClass("active");
		} else {
			if(!selected.hasClass("active")){ //don't make request if already selected
				$.ajax({
					method: "POST",
					url: path+"js/ajax/save-page-status.php",
					data: { id: selected.parent().attr("data-id"), showhide: selected.val(), xssid: getCookie("xssid") },
					success: function(data){
						setMiniAlert(data);
						selected.siblings().removeClass("active");
						selected.addClass("active");
					}
				});
			}
		}
	});
});
	
//Table Sorter
$(function(){
	$.tablesorter.addParser({ 
	    id: 'monthDayYear', 
	    is: function(s) { 
	        return false; 
	    }, 
	    format: function(s) { 
	        var date = new Date(Date.parse(s));
	        return new Date(date);
	    }, 
	    type: 'numeric' 
	}); 
	$('table.tablesorter').each(function(){
		var pager = $(this).next('.pager');
		var pagesize = $(pager).data('pagesize');
		var pagerOptions = {
			container: pager, 
			size: pagesize, 
			savePages : true,
			storageKey:'tablesorter-pager',
			output: 'Displaying {startRow} - {endRow} ({totalRows} Total)', 
			updateArrows: true,
			fixedHeight: false,
			removeRows: true
		};
		$(this).tablesorter({
			emptyTo: '9999'
		}).tablesorterPager(pagerOptions);

		// If no rows are being shown but there are results, fix pager by resetting it
		if($(this).find('tbody tr').length == 0 && $(this)[0].config.pager.totalPages > 0) {
			pagerOptions.savePages = false;
			$(this).tablesorterPager(pagerOptions);
		}
	});
	$("table.stickyheader").tablesorter({
		widgets: ['stickyHeaders'],
		widgetOptions: {
			stickyHeaders_offset: 85 //offset for sticky page header
		}
	});
});
	
//Tooltips
$(function(){
	$('.tooltip').tooltip({ 
		track: true,
		content: function(){
			return $(this).prop('title');
		}
	});
});

//Tabs
$(function(){
	$(".tabs").tabs({ 
		show: { effect:"fade", duration:300 }, 
		hide: { effect:"fade", duration:300 }
	});
});
	
//Autofills
$(function(){
	$('input[placeholder], textarea[placeholder]').placeholder();
});

//Character Counts
$(function(){
	$(document).on("keyup","textarea[class*='char-count-'],input[class*='char-count-']",function(){
		var maxLength = 0;
		var textLength = $(this).val().length;
		var target = $("#count-"+$(this).attr("id"));
		var classList = $(this).attr('class').split(/\s+/);
		$.each(classList, function(index, item) {
		    if(item.substring(0, 11) === 'char-count-'){
			    var split = item.split('-');
		        maxLength = split[2];
		    }
		});
		target.text(textLength);
		if(textLength > maxLength)
			target.addClass("error");
		else 
			target.removeClass("error");
	});
});

//Date Picker
$(function(){
	$( ".datepicker" ).datepicker({
		prevText: '<i class="fa fa-chevron-circle-left"></i>',
        nextText: '<i class="fa fa-chevron-circle-right"></i>',
		dateFormat: 'yy-mm-dd',
		dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
		numberOfMonths: 1
	});
	$( ".datepicker.multi" ).datepicker({
		prevText: '<i class="fa fa-chevron-circle-left"></i>',
        nextText: '<i class="fa fa-chevron-circle-right"></i>',
		dateFormat: 'yy-mm-dd',
		dayNamesMin: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
		numberOfMonths: 3
	});
});

//Clear Form Errors
$(function(){
	$('input').blur(function(){
		if($(this).hasClass('required') && $(this).val() != ''){
			$(this).removeClass('required');
		}
	});
});
	
//Delete Confirm
$(function(){
	$('.delete').bind('click', function(){
		var deletebtn = $(this);
		if($(this).attr('type') == 'button'){
			$('<div id="dialog-box"></div>').appendTo('body')
			.html('Are you sure you want to delete this entry?')
			.dialog({
				modal: true, 
				title: 'Confirm',
				autoOpen: true,
				width: 300,
				resizable: false,
				closeOnEscape: true,
				closeText: "x",
				buttons: {
					"Confirm": function(){
						$(deletebtn).attr('type', 'submit').trigger('click');
					},
					Cancel: function(){
						$(this).dialog("close");
					}
				 },
				show:{effect:"drop", direction:"up", duration:200},
				hide:{effect:"drop", direction:"up", duration:200},
				open: function(){
					$('.ui-dialog-buttonpane').
	                    find('button:contains("Cancel")').button({
	                    icons: {
	                        primary: 'fa fa-ban'
	                    }
	                });
	                $('.ui-dialog-buttonpane').
	                    find('button:contains("Confirm")').button({
	                    icons: {
	                        primary: 'fa fa-check'
	                    }
	                });
				}
			});	
		}
	});
});

//Business Hours
$(function(){
	$('#hours').find('input[name^="closed"]').change(function(){
		if(this.checked){
			$(this).parents('tr').find('select').attr('disabled', true);
		}else{
			$(this).parents('tr').find('select').removeAttr('disabled');
		}
	});
});

//Page Tabs
$(function(){
	$('#addpanel button').click(function(){
		var html = $('#tabs_panel_template').html();
		$('#addpanel').before('<div class="tabspanel newpanel" style="display:none;">'+html.replace('tinymceDynamic', 'tinymceEditor')+'</div>');
		tinymceInitDefault(".newpanel textarea.tinymceEditor");
		$('.newpanel').removeClass('newpanel').fadeIn(300);	
	});
	
	$(document).on('click','.delete-tab',function(){
		var container = $(this).parents('.tabspanel');
		$('<div id="dialog-box"></div>').appendTo('body')
		.html('Are you sure you want to permanently delete this tab?')
		.dialog({
			modal: true, 
			title: 'Confirm',
			autoOpen: true,
			width: 300,
			resizable: false,
			closeOnEscape: true,
			closeText: "x",
			buttons: {
				"Confirm": function() {
					$(this).dialog("close");
					var tab_id = $(container).find('.tab_id').val();
					$.ajax({
						url: path+'js/ajax/deletetab.php',
						data: 'tab_id='+tab_id+"&xssid="+getCookie("xssid"),
						type: 'post',
						success: function(data){
							if(data == 'success'){
								$(container).fadeOut(300, function(){
									$(container).remove();	
								});
							}else{
								alert('Error! Unable to delete tab. Please try again.');	
							}
						},
						error: function(data){
							alert('Error! Unable to delete tab. Please try again.');	
						}
						
					});
				},
				Cancel: function() {
					$(this).dialog("close");
				}
			 },
			show:{effect:"drop", direction:"up", duration:200},
			hide:{effect:"drop", direction:"up", duration:200},
			open: function(){
				$('.ui-dialog-buttonpane').
                    find('button:contains("Cancel")').button({
                    icons: {
                        primary: 'fa fa-ban'
                    }
                });
                $('.ui-dialog-buttonpane').
                    find('button:contains("Confirm")').button({
                    icons: {
                        primary: 'fa fa-check'
                    }
                });
			}
		});
	});
});

//Logout
function logout(){
	$('<div id="dialog-box"></div>').appendTo('body')
	.html('Are you sure you want to logout?')
	.dialog({
		modal: true, 
		title: 'Confirm',
		autoOpen: true,
		width: 300,
		resizable: false,
		closeOnEscape: true,
		closeText: "x",
		buttons: {
			"Confirm": function() {
				window.location = path+'modules/Logout.php';
			},
			Cancel: function() {
				$(this).dialog("close");
			}
		 },
		show:{effect:"drop", direction:"up", duration:200},
		hide:{effect:"drop", direction:"up", duration:200},
		open: function(){
			$('.ui-dialog-buttonpane').
                find('button:contains("Cancel")').button({
                icons: {
                    primary: 'fa fa-ban'
                }
            });
            $('.ui-dialog-buttonpane').
                find('button:contains("Confirm")').button({
                icons: {
                    primary: 'fa fa-check'
                }
            });
		}
	});	
}

//PrettyPhoto & Photoswipe
$(document).ready(function(){
	if(!minwindow){
		$("a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal', slideshow:6000, autoplay_slideshow: false, overlay_gallery: false, deeplinking: false, theme: 'light_square', social_tools: false});	
	}else{
		$("a[rel^='prettyPhoto']:not(.iframe)").photoSwipe();	
		$("a.iframe[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal', slideshow:6000, autoplay_slideshow: false, overlay_gallery: false, deeplinking: false, theme: 'light_square', social_tools: false});	
	}
});

//Page SEO Google Preview 
$(function(){
	if($(".seo-preview").length > 0){
		if($("#seo-slug").val() == '' || $("#seo-slug").val() == undefined){
			var current_slug = $("#button-text").val().replace(/[^a-zA-Z0-9\/_|+ -]/gi,'').replace(/[\/_|+ -]+/gi, '-').replace(/(^-|-$)/g,'').toLowerCase();
		} else {
			var current_slug = $("#seo-slug").val().replace(/[^a-zA-Z0-9\/_|+ -]/gi,'').replace(/[\/_|+ -]+/gi, '-').replace(/(^-|-$)/g,'').toLowerCase();
		}
		var full_url = $("#default-url").val();
		$(document).on("change","#button-text, #seo-slug, #seo-title, #seo-description",function(){
			$(".google-preview").stop().fadeOut("fast",function(){
				if($("#seo-title").val() != ""){
					$(".seo-title").text($("#seo-title").val());
				} else {
					$(".seo-title").text($("#default-meta-title").val());
				}
				$(".seo-description").text($("#seo-description").val());
				if($("#seo-slug").val() != ""){
					var new_slug = $("#seo-slug").val().replace(/[^a-zA-Z0-9\/_|+ -]/gi,'').replace(/[\/_|+ -]+/gi, '-').replace(/(^-|-$)/g,'').toLowerCase();
				} else {
					var new_slug = $("#button-text").val().replace(/[^a-zA-Z0-9\/_|+ -]/gi,'').replace(/[\/_|+ -]+/gi, '-').replace(/(^-|-$)/g,'').toLowerCase();
				}
				if(current_slug == ""){
					full_url = full_url+new_slug;
				} else {
					full_url = full_url.replace(current_slug, new_slug);
				}
				$(".seo-slug").text(full_url);
				current_slug = new_slug;
				$(this).fadeIn("slow");
			});
		});
	}
});

//Mini Ajax Alert 
function setMiniAlert(message){
	var this_alert = $(message);
	this_alert.addClass("come-in-top");
	$("#system-mini-alerts").prepend(this_alert);
	this_alert.delay(4000).queue(function(){$(this).removeClass('come-in-top').addClass('bounce-out-top')});
	setTimeout(function(){
		this_alert.remove();
	}, 5000);
}

//Cookie Functions
function setCookie(cname, cvalue, exdays){
	if(exdays == 0){
		var expires = "expires=0";
	}else{
    	var d = new Date();
    	d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+d.toUTCString();
	}
    document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
}
function getCookie(cname){
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}