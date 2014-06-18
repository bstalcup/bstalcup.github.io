function showResetMsg(clasa, txt) {
	$(".flash_message p").addClass(clasa).html(txt);
	$(".flash_message").fadeIn( "fast" );
	setTimeout(function(){
		$(".flash_message").fadeOut( "fast" ); 
	}, 2500);
	$(this).blur();
}

$(window).on('resize',function() {
    var h = $(window).height();
		$("#home-login").css('height', h);
	}).trigger('resize');
$(document).ready(function(){

	if (typeof google != "undefined")
		var geocoder = new google.maps.Geocoder();
	var parishes = {};
	var h = $(window).height();

	$("#home-login").css('height', h);
    // flip stuff here
    $('a#new-account-link').click(function () {
		$("#flip-login-form").hide("slide", { direction: "left" }, 800);
        $("#sign-up-form").delay(1000).show("slide", { direction: "left" }, 800);
        return false;
    });
	
	$('a#sign-up-cancel').click(function () {
		$("#sign-up-form").hide("slide", { direction: "left" }, 800);
		$("#flip-login-form").delay(1000).show("slide", { direction: "left" }, 800);
		return false;
	});
	
	 $('a#forgot-passwd-link').click(function () {
		$("#flip-login-form").hide("slide", { direction: "left" }, 800);
		$("#forgot-passwd").delay(1000).show("slide", { direction: "left" }, 800);
		return false;
	});
	
	$('a#cancel-passwd').click(function () {
		$("#forgot-passwd").hide("slide", { direction: "left" }, 800);
		$("#flip-login-form").delay(1000).show("slide", { direction: "left" }, 800);
		return false;
	});
	
	//functions used on fish-parish page
	
	setTimeout(function(){
		$('#welcomeModal').modal('show');
	}, 400);
	
	$('#submit-locate-parish').click(function (e) {
		e.preventDefault();
		$('.find-parish-search').hide("slide", { direction: "left" }, 800);
		$('.find-parish-results').delay(900).show("slide", { direction: "left" }, 800);
	 	var address = $("#cityState").val() != "" ? $("#cityState").val() : $("#zipCode").val();
	 	geocoder.geocode( { 'address': address }, function(results, status) {
			var html = "";
			if (status == google.maps.GeocoderStatus.OK)
			{
				lat = results[0].geometry.location.k;
				lng = results[0].geometry.location.A;
				$.get("http://apiv4.updateparishdata.org/Churchs/", {lat: lat, long: lng, pg: 1}, function(data) {
					if (data.length > 0)
					{
						parishes = data;
						$.each(data, function (i, el) {
							html += "<li class='parish' data-id='"+ i +"'>"+ el.name +" ("+ el.church_address_city_name +", "+ el.church_address_providence_name +", "+ el.church_address_postal_code +")</li>";
						});
						$(".loader-container").hide();
						$(".find-parish-results-list").append(html);
					}
					else {
						$(".loader-container").hide();
						$(".find-parish-results-list").append("<li class='no-results'>This is a bit embarassing.<br>We weren't able to find your location.<br> Try searching again.");
					}
				});
			}
			else{
				$(".loader-container").hide();
				$(".find-parish-results-list").append("<li class='no-results'>This is a bit embarassing.<br>We weren't able to find your location.<br> Try searching again.");
			}
	    });
		return false;
	});

	$('#back-to-locate-parish').click(function (e) {
		e.preventDefault();
		$('.find-parish-results').hide("slide", { direction: "left" }, 800);
		$('.find-parish-search').delay(900).show("slide", { direction: "left" }, 800);
		$('.select-parish .find-parish-results-list li').remove();
		$(".loader-container").show();
		$("#submit-parish-results").prop('disabled', true);
		return false;
	});
	
	$(document).on('click', '.select-parish .find-parish-results-list li', function (e) {
		var $activeli = $('.select-parish .find-parish-results-list li.selected');
		$activeli.removeClass('selected');
		$(this).addClass('selected');
		$("#submit-parish-results").prop('disabled', false);
		return false;
	});

	$('#submit-parish-results').click(function (e) {

		e.preventDefault();
		var htmlString = '<div id="floatingCirclesG" class="loading-delete-families"><div id="frotateG_01" class="f_circleG"></div><div id="frotateG_02" class="f_circleG"></div><div id="frotateG_03" class="f_circleG"></div><div id="frotateG_04" class="f_circleG"></div><div id="frotateG_05" class="f_circleG"></div><div id="frotateG_06" class="f_circleG"></div><div id="frotateG_07" class="f_circleG"></div><div id="frotateG_08" class="f_circleG"></div></div>';
		$( this ).parent().html( htmlString );
		var selectedParish = parishes[$(".find-parish-results-list li.selected").attr("data-id")];
		$.post("", selectedParish, function(result){
			VanillaZF.acknowledge(result);
		});

		return false;
	});
		
});

$(function() {
	$('.userLogin').submit(function() {
		var aemail = $('#email');
		var apass = $('#password');

		// validate required fields
		var stumble = false;
		var elements = '';
		if (!aemail.val()) {
			elements += 'email';
			aemail.parent().parent().addClass('error');
			stumble = true;
		} else {
			aemail.parent().parent().removeClass('error');
		}
		if (!apass.val()) {
			if (elements.length) elements += ' and ';
			elements += 'password';
			apass.parent().parent().addClass('error');
			stumble = true;
		} else {
			apass.parent().parent().removeClass('error');
		}

		if (!stumble) {
			//$('#waitModal').modal({backdrop: 'static', 'show': true});
			// we did not stumble, send request to server
			$.post(document.location.href, {action: 'login', email: aemail.val(), password: apass.val()}, function(result) {
				// unlock form
				// manage server feedback
				//$('#waitModal').modal('hide');
				VanillaZF.acknowledge(result);
			}, 'json');
		} else {
			// show warning message of missing input
			VanillaZF.addWarning('Please enter your ' + elements + '.');
		}

		return false;
	});
});