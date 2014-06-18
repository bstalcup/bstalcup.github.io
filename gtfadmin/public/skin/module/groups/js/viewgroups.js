$(document).ready(function(){

	$(".group_destroy").click(function(e){
		var url = $(this).attr("data-href");
		var parentTR = $(this).closest("tr");
		var my_name = parentTR.find("td:first").text();
		var htmlString = '<div id="floatingCirclesG" class="loading-delete-families-small"><div id="frotateG_01" class="f_circleG"></div><div id="frotateG_02" class="f_circleG"></div><div id="frotateG_03" class="f_circleG"></div><div id="frotateG_04" class="f_circleG"></div><div id="frotateG_05" class="f_circleG"></div><div id="frotateG_06" class="f_circleG"></div><div id="frotateG_07" class="f_circleG"></div><div id="frotateG_08" class="f_circleG"></div></div>';
		if (confirm( "Are you sure you want to delete the '" + my_name +"' group?" ))
		{	
			$( this ).parent().html( htmlString );
			$.post(url, function (data){
				VanillaZF.acknowledge(data);
				if (data.result.success == true)
				{				
					parentTR.fadeOut();
					showAlert('alert-success', 'We successfully removed the group.');
				}
				else
					showAlert('alert-danger', 'An error has occured. Please contact your site administrator.');	
				return false;
			}, 'json')
		}
		return false;
	});
});
