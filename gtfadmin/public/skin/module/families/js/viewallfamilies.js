$(document).ready(function(){
	var a = $('#directory_search').autocomplete({
		serviceUrl:'/family/autocomplete',
		minChars:3,
		delimiter: /(,|;)\s*/, // regex or character
		maxHeight:400,
		width:240,
		zIndex: 9999,
		appendTo: '#suggestions-container',
		onSearchStart: function (query) { $('#floatingCirclesG').show(); $('#suggestions-container').show(); },
		onSearchComplete: function (query, suggestions) { $('#floatingCirclesG').hide(); },
		deferRequestBy: 0, //miliseconds
		noCache: false, //default is false, set to true to disable caching
	});

	$("a.search-trigger").on("click", function(){
		$("form.family-search-form").submit();
	});

	$("form.family-search-form").submit(function(e) {
		$('#suggestions-container').hide();
	});

	$( "#directory_search" ).focusin(function() {
		$('#suggestions-container').show();
	});

	$( "#directory_search" ).focusout(function() {
		$('#suggestions-container').hide();
	});

	$("a.add-new-family").on("click", function(){
		var url = $(this).attr("href");
		makeTheCall(url, {});
	});

	$(".deleteFamily").click(function(e){
		var url = $(this).attr("data-href");
		var parentTR = $(this).closest("tr");
		var my_lastname = parentTR.find("td:first").text();
		if (confirm( "Are you sure you want to delete the '" + my_lastname +"'family?" ))
		{	
			$( this ).parent().html( htmlString );
			$.post(url, {file_name: $(this).attr("data-file")}, function (data){
				VanillaZF.acknowledge(data);
				if (data.result.success == true)
				{				
					parentTR.fadeOut();
					showAlert('alert-success', 'We successfully removed the family.');
				}
				else
					showAlert('alert-danger', 'An error has occured. Please contact your site administrator.');	

				return false;
			}, 'json')
		}
		return false;
	});
});
