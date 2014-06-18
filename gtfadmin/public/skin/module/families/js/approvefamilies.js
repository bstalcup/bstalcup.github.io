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

	$('.families-table .button.approve').on( 'click', function() {
		var thisParent = $(this).closest('tr');
		var htmlString = '<div id="floatingCirclesG" class="loading-delete-families"><div id="frotateG_01" class="f_circleG"></div><div id="frotateG_02" class="f_circleG"></div><div id="frotateG_03" class="f_circleG"></div><div id="frotateG_04" class="f_circleG"></div><div id="frotateG_05" class="f_circleG"></div><div id="frotateG_06" class="f_circleG"></div><div id="frotateG_07" class="f_circleG"></div><div id="frotateG_08" class="f_circleG"></div></div>';
		$( this ).parent().html( htmlString );
		$.post(approved_url, {thisFamilyId: $(this).attr("data-id")}, function(data){
			VanillaZF.acknowledge(data);
			if (data.success == true)
			{
				showAlert('alert-success', 'Family successfully approved!');
				thisParent.fadeOut();
				var app = parseInt($("#approvedFamilies").text());
				$("#approvedFamilies").html(app - 1);
			}
			else
				showAlert('alert-danger', 'An error has occured. Please contact the site administrator.');
		}, 'json');
	});

	$('.families-table .button.no_approve').on( 'click', function() {
		if (confirm("Are you sure you want to delete this family ?")) {
			var thisParent = $(this).closest('tr');
			var htmlString = '<div id="floatingCirclesG" class="loading-delete-families"><div id="frotateG_01" class="f_circleG"></div><div id="frotateG_02" class="f_circleG"></div><div id="frotateG_03" class="f_circleG"></div><div id="frotateG_04" class="f_circleG"></div><div id="frotateG_05" class="f_circleG"></div><div id="frotateG_06" class="f_circleG"></div><div id="frotateG_07" class="f_circleG"></div><div id="frotateG_08" class="f_circleG"></div></div>';
			$( this ).parent().html( htmlString );
			$.post(familydeleteURL.replace('0', $(this).attr("data-id")), {file_name: $(this).attr("data-file")}, function(data){
				VanillaZF.acknowledge(data);
				if (data.result.success == true)
				{
					showAlert('alert-success', 'Family successfully deleted!');
					thisParent.fadeOut();
					var app = parseInt($("#approvedFamilies").text());
					$("#approvedFamilies").html(app - 1);
				}
				else
					showAlert('alert-danger', 'An error has occured. Please contact the site administrator.');
			}, 'json');
		} else {
			return false;
		}
	});
});
