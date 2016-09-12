var path = "/fantasycurling/";
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

function savePicks(e, formSelector, buttonSelector){
	e.preventDefault();
	var games = new Array();
	$(".input").each(function(){
		if($(this).attr("data-type") == 'game'){
			if($(this).is(":checked")){				
				var x = {team_id: $(this).val(), game_id: $(this).attr("name")};
				games.push(x);
			}
		}
	});
	hotshots = new Array();
	$(".input").each(function(){
		if($(this).attr("data-type") == 'hotshot'){					
			var x = {hotshot_id: $(this).val()};
			hotshots.push(x);		
		}
	});

	$(buttonSelector).addClass("spinner");
	$.ajax({
		url: path + 'js/ajax/save-picks.php',
		data: {hotshotss: hotshots, gamess: games},
		method: 'post',
		dataType: 'json',
		success:function(data){			
			if(data.success == 'true'){
				$(buttonSelector).addClass("success");
				setTimeout(function (){$(buttonSelector).removeClass("success");
				$(buttonSelector).removeClass("spinner");}, 10000);
			}
			else {
				$(buttonSelector).addClass("error");
				setTimeout(function (){$(buttonSelector).removeClass("error");
				$(buttonSelector).removeClass("spinner");}, 10000);
				console.log(data);
				console.log("error");
			}
		},
		error: function(data){
			console.log(data);
			$(buttonSelector).removeClass("spinner");
		}
	});
}
function startSpinner(el){
	$(el).addClass("spinner");
}
function stopSpinner(el){
	$(el).removeClass("spinner");
}
$(document).ready(function(){
	if ($(".save-picks-wrapper").length > 0){
		$(window).on("scroll", function(){
			if(($(window).scrollTop() + $(window).height()) >= ($(".save-picks-wrapper").offset().top + $(".save-picks-container").height() + 20)){
				$(".save-picks-container").addClass("relative");
			}
			else {
				$(".save-picks-container").removeClass("relative");
			}
		});
	}
});

$(document).ready(function(){
	$(".input-container input").on("change", function(){		
		if($(this).val() != ""){
			$(this).addClass("filled");
			$(this).removeClass("error");
		}	
		else {
			$(this).removeClass("filled");
		}
	});
})

function toggleLogin() {
	$(".login-container").toggleClass("hidden");
	$(".login-footer").toggleClass("hidden");
	$(".register-footer").toggleClass("hidden");
	$(".register-container").toggleClass("hidden");
}

function submitAndValidateForm(e, formElement){
	errors = false;
	e.preventDefault();
	$(formElement + " input").each(function(){
		if($(this).val() == "" && $(this).hasClass("required")){
			errors = true;
			$(this).addClass("error");
		}
		else {
			$(this).removeClass("error");
		}
	})
	if(!errors){
		$(formElement).submit();
	}
}