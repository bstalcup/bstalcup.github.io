var ajaxData = {};
$(document).ready(function(){
	
	$("a.toggle-menu-anchor").click(function(e) {
		e.preventDefault();
		var elem = $(this).next('.submenu');
		var par = $(this).closest('li.toggle-menu');
		var func = $(this).hasClass('selected') ? 'removeClass' : 'addClass';
        elem.slideToggle('slow');
		$(this)[func]("selected");
	});

	//$(".toggle-menu a.selected").parents("li.active").click();
	$("li.active a.toggle-menu-anchor").click();

	// start - make ajax requests for page content
	$("ul.sidebar-nav li a").not(".toggle-menu-anchor").click(function(e){
		var url = $(this).attr("href");
		//sidebar actions
			var par = $(this).closest('li.toggle-menu');
			var elem = $('.sidebar-nav li.active').not(par);
			var headli = $(this).parents('li');
			headli.addClass('active'); //add class active to parent li
			elem.removeClass('active'); //remove class active
			$('.submenu a').removeClass('selected');
			$(this).addClass('selected');
			par.addClass("active");
			var test = $('.sidebar-nav li.active').find('.submenu'); //store the current active submenu
			$('a.toggle-menu-anchor').not($(this).closest('.sidebar-nav li.toggle-menu').find('a.toggle-menu-anchor')).removeClass('selected'); //remove the selected class to the parent a.toggle-menu-anchor for arrow
			$('.submenu').not(test).slideUp(); //hide all submenus that are not active
		//end sidebar actions
		makeTheCall(url, {});
	})
	// end - make ajax requests for page content

	$(document).on("click", "a", function(e){
		// use this to avoid doing the same thing again
		if (!$(this).hasClass('no_follow'))
		{
			var url = $(this).attr("href");
			history.pushState(null, null, url);
			if ($(this).attr('href') != '/logout')
				e.preventDefault();
		}
	});
	
	$(window).on('popstate', function () {
    	$.ajax({
        	url: location.pathname + '?type=ajax' + location.search.replace("?", "&"), success: function (data) {
            	$("div.view").html(data.content);
        	}
    	});
	});

	// start - make ajax requests for tabelar information
	$(document).on("click", "#data-table tr a", function(){

		// use this to avoid doing the same thing again
		if (!$(this).hasClass('no_follow') && !$(this).hasClass('order'))
		{
			var params = {};
			var url = $(this).parents("tr").attr("url");
			if ($(this).parents("table").hasClass("families-table"))
			{
				familyUrlArr = url.split("-");
				for (var i in ajaxData.families)
					if (ajaxData.families[i].objectId == familyUrlArr[familyUrlArr.length-1])
						params = {family: ajaxData.families[i]};
			}
			if ($(this).parents("table").hasClass("groups-table"))
			{
				groupUrlArr = url.split("-");
				for (var i in ajaxData.groups)
					if (ajaxData.groups[i].objectId == groupUrlArr[groupUrlArr.length-1])
						params = {group: ajaxData.groups[i]};
			}
			makeTheCall(url, params);
		}	
	})
	// end - make ajax requests for tabelar information

	// start - make ajax requests for pagination
	$(document).on("click", "div.pagination ul li a, a.order", function(){
		// use this to avoid doing the same thing again
		if (!$(this).hasClass('no_follow'))
		{
			var url = $("#data-table").attr("url") + $(this).attr("href");
			makeTheCall(url, {});
		}
	})
	// end - make ajax requests for pagination

	$(function () {
		$("#supportModal").modal({show:false});
	});
	
	$("#contact_form_trigger").click(function() {
		$('#supportModal').modal('show')
	});  	
	
	/*upload family scripts*/
	
	// Watch for the value of the "Choose Image" field to change...
	
	$('#parish_directory').change(function() {
				
		// If the upload field is empty, we'll hide te upload button,
		// Otherwise, let's show it.
		if( '' === $('#parish_directory').val() ) {
			$('.upload_directory_button').hide();
		} else {
			$('.upload_directory_button').show();
		} // end if/else
		
	});
	
	$(function() {
		$( "#sortable" ).sortable();
	});
	//end sortable
	$("a.directory_cancel_upload").click(function(e) {
		e.preventDefault();
		$(".edit-directory-upload").slideUp();	
	});
	//hide CSV edit section and go back to uploading a new CSV file
	
	/*end upload family scripts*/
	
});//end ready event

/* start ajax call */
	var makeTheCall = function( url, params ) {
		$("div.view").html($("#waitModal").html());
		$("div.view .loader-container").show();
		$.post(url, params, function (data){
			$("div.view").html(data.content);
			VanillaZF.acknowledge(data);
			VanillaZF.minister('body');
			ajaxData = data;
		}, 'json')
	};
/* end ajax call */

/*show alert*/
	//$(document).on("click", ".family-details .button-green", function(){
		//showAlert('alert-danger', 'We successfully removed these families.'); //bad
		//showAlert('alert-success', 'We successfully added these families.'); //good
	//});
	var showAlert = function( val_class, val_message ) {
		$('div.alert').attr('class', 'alert'); 			//clear all clases exept alert
		$('div.alert').empty(); 			   			//empty all the content inside
		$('div.alert').addClass( val_class ).show();	//add passed class value
		$('div.alert').text( val_message );				//add passed text/message value
		$('div.alert').delay(2000).fadeOut('slow');		//fade out after a delay of 1.2 sec
	};
/*end show alert*/
