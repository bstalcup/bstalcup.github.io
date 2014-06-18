$(document).ready(function(){
	
	$('.tool-tip').tooltip();
	
	$('#myCarousel').carousel({ interval: false });

	$(function() {
		$( "#edit-directory" ).tabs({ active: 0 });
	});
	
	$(function () {
		$("#deleteParishModal").modal({show:false});
	});
	
	$("#delete_parish_trigger").click(function() {
        if (allFamiliesCount == 0)
        {
        	alert("Your parish directory is empty!");
        	return false;
        }
		$('#deleteParishModal').modal('show');
	});  
	
	$('.delete-directory-form .button-red').hide();
	
	var MatchMe = 'DELETE';
    $('#delete-directory').on('input', function(){
        if($(this).val() == MatchMe){
            $('.delete-directory-form .button-red').show();
        } else {
			$('.delete-directory-form .button-red').hide();
		}
    });

    $('.clearClose').on("click", function(){
    	$("#delete-directory").val('');
    	$(".DeleteAllFamilies").hide();
    	$('#delete-directory').show();
    	$('.progress').hide();
    });

    $('.DeleteAllFamilies').on("click", function(){
    	$('#delete-directory, .DeleteAllFamilies').hide();
    	$('.progress').show();
    	//$('.clearClose').attr("disabled","disabled");
    	/* use this to enable the close modal button again: 
    	$('.clearClose').removeAttr('disabled');
		
		var counter = 0;
     	setInterval(function() {
      		counter = (counter + 5) % 100;
      		$(".progress .bar").css("width", counter + "%");
      		$(".progress .bar .sr-only").html( counter + "%" );
     	}, 1000);
		*/
		deleteDirectory();
		return false;
    });

   
	$('.duplicate-family-removal').on("click", function(){
		var checkedHere = $( "input:checked.delete_selection" ).length;
		if (checkedHere == 0)
		{
			alert( "You must select the family you want to delete." );
			return false;
		}
		if (confirm("Are you sure you want to delete this family/s ?"))
		{
			var len = $("input:checked.delete_selection").length;
			$("input:checked.delete_selection").each(function (index){
				var familyName = $(this).closest("td").next().text() + " " + $(this).closest("td").next().next().text(), url = $(this).attr("data-href");
				$.post(url, {file_name: $(this).attr("data-file")}, function (data){
					VanillaZF.acknowledge(data);
					if (data.result.success == true)
					{				
						if (index == len - 1)
						{
							//update the number in the header
							var oldNumber = $("#duplicate_number").text().match(/\d+/g);
							$("#duplicate_number").html("Duplicate Families (" + (parseInt(oldNumber[0])-1) + ")");
							// take care of the carousel
							$('#myCarousel').carousel('next');
							setTimeout(function() {
								$('.active').prev(".item").remove();
								$('#myCarousel').carousel({ interval: false });
							}, 1000);
							
						}
						showAlert('alert-success', 'We successfully removed the '+ familyName +' family.');
					}
					else
						showAlert('alert-danger', 'An error has occured while deleting the '+ familyName +' family. Please contact your site administrator.');	
				}, 'json')
			});
		}
		return false;
	});

	$('.merge_bottom_top, .merge_top_bottom').on("click", function(){
		var myPrev = $(this).closest('tr').prev('tr'),
			myNext = $(this).closest('tr').next('tr'),
			params = {};
		if ($(this).hasClass("merge_top_bottom"))
		{
			var familyName = myNext.find("td:first").text() + " " + $(this).closest("tr").find("td:first").next().text(),
				master = myNext.attr("data-id"),
				slave = myPrev.attr("data-id");
		}
		else
		{
			var familyName = myPrev.find("td:first").text() + " " + $(this).closest("tr").find("td:first").next().text(),
				master = myPrev.attr("data-id"),
				slave = myNext.attr("data-id");
		}

		if (!confirm( "Are you sure that " + familyName +  " is the Master family?" ))
			return false;

		params.slave = [];
		params.master = [];
		for (var i in families) {
			for (var j in families[i]) {
				if (families[i][j]['objectId'] == master) {
					for (var k in families[i][j])
						if (k == 'familyPhoto')
							params.master.push(k +'|||'+ families[i][j][k]['name']);
						else if (k != 'parish')
							params.master.push(k +'|||'+ families[i][j][k]);
				}
				if (families[i][j]['objectId'] == slave)	{
					for (var k in families[i][j])
						if (k == 'familyPhoto')
							params.slave.push(k +'|||'+ families[i][j][k]['name']);
						else if (k != 'parish')
							params.slave.push(k +'|||'+ families[i][j][k]);
				}
			}
		}

		$.post(mergeUrl, params, function (data){
			VanillaZF.acknowledge(data);
			if (data.result.success == true)
			{
				//update the number in the header
				var oldNumber = $("#duplicate_number").text().match(/\d+/g);
				$("#duplicate_number").html("Duplicate Families (" + (parseInt(oldNumber[0])-1) + ")");
				// show the alert
				showAlert('alert-success', 'We successfully merge the families.');
				// take care of the carousel
				$('#myCarousel').carousel('next');
				setTimeout(function() {
					$('.active').prev(".item").remove();
					$('#myCarousel').carousel({ interval: false });
				}, 1000);
			}
			else
				showAlert('alert-danger', 'An error has occured while merging the families. Please contact your site administrator.');	
		}, 'json')
	});

	$('.delete_duplicate').on("click", function(){
		if (confirm("Are you sure you want to delete this family ?")) {
			var familyName = $(this).closest("tr").find("td:first").text() + " " + $(this).closest("tr").find("td:first").next().text(), 
				url = $(this).attr("data-href");
			$.post(url, {file_name: $(this).attr("data-file")}, function (data){
				VanillaZF.acknowledge(data);
				if (data.result.success == true)
				{				
					//update the number in the header
					var oldNumber = $("#duplicate_number").text().match(/\d+/g);
					$("#duplicate_number").html("Duplicate Families (" + (parseInt(oldNumber[0])-1) + ")");
					// take care of the carousel
					$('#myCarousel').carousel('next');
					setTimeout(function() {
						$('.active').prev(".item").remove();
						$('#myCarousel').carousel({ interval: false });
					}, 1000);
					showAlert('alert-success', 'We successfully removed the '+ familyName +' family.');
				}
				else
					showAlert('alert-danger', 'An error has occured while deleting the '+ familyName +' family. Please contact your site administrator.');	
			}, 'json')
		} else {
			return false;
		}
	});
});

function deleteDirectory()
{
	$.post(deleteDirectoryURL, function (data){
		VanillaZF.acknowledge(data);
  		counter = Math.ceil(100 / Math.ceil(allFamiliesCount / 20));
  		$(".progress .bar").css("width", counter + "%");
  		$(".progress .bar .sr-only").html( counter + "%" );
		if (data.result == true)
		{				
			deleteDirectory();
			return false;
		}
		if (data.result == "Done")
		{				
			$('.clearClose').removeAttr('disabled');
			//$('#deleteParishModal').modal('hide');
			showAlert('alert-success', 'Your parish directory has been deleted!');
			return false;
		}
	}, 'json')
}