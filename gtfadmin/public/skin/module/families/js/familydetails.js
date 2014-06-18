$(document).ready(function(){
  	VanillaZF.minister('body');
    $('#help-tooltip').tooltip();
    $('a.new-member').click(function(e) {
    	e.preventDefault();
    	$('.add-new-member').show();
    	return false;
    });

    $('.cancel-member').click(function() {
    	$('.edit-member input:visible').val("");
    	$('.add-new-member').hide();
    });

	$('a.member_destory').click(function() {
		var htmlString = '<div id="floatingCirclesG" class="loading-delete-families-single"><div id="frotateG_01" class="f_circleG"></div><div id="frotateG_02" class="f_circleG"></div><div id="frotateG_03" class="f_circleG"></div><div id="frotateG_04" class="f_circleG"></div><div id="frotateG_05" class="f_circleG"></div><div id="frotateG_06" class="f_circleG"></div><div id="frotateG_07" class="f_circleG"></div><div id="frotateG_08" class="f_circleG"></div></div>';
		if (confirm('Are you sure you want to delete this family member?')) {
			$(this).parent().find( $(".loader-here")).html( htmlString );
			$(this).hide();
			var thisDD = $(this).parents("dd");
			$.post(deleteUrl.replace('replacement', $(this).attr('data-id')), {file_name: $(this).attr('data-file'), user_id: $(this).attr('data-user-id')}, function(data){
				VanillaZF.acknowledge(data);
				if (data.result.success == true)
					thisDD.fadeOut();
			}, 'json');
		}
    	return false;
	});

	$('li.member-name, .family-member-avatar').on( "click", function() {
	  $(this).closest('.family-member').find('.edit-trigger').click();
	});

	$('.edit-trigger').on( "click", function() {
		$(this).toggleClass("open");
		$(this).next('.edit-member-form').slideToggle();
		return false;
	});

	$('input.deletefamily').on( "click", function(e) {
		var my_lastname = $('input[name="lastName"]').val();
		var htmlString = '<div id="floatingCirclesG" class="loading-delete-families-single"><div id="frotateG_01" class="f_circleG"></div><div id="frotateG_02" class="f_circleG"></div><div id="frotateG_03" class="f_circleG"></div><div id="frotateG_04" class="f_circleG"></div><div id="frotateG_05" class="f_circleG"></div><div id="frotateG_06" class="f_circleG"></div><div id="frotateG_07" class="f_circleG"></div><div id="frotateG_08" class="f_circleG"></div></div>';
		if (confirm( "Are you sure you want to delete the " +  my_lastname +" family?" ))
		{	
			$(this).hide();
			$(".loader-here").html( htmlString );
			var url = $(this).attr("data-href");
			$.post(url, {file_name: $(this).attr("data-file")}, function (data){
				VanillaZF.acknowledge(data);
				if (data.result.success == true)
				{				
					$("a#default_page").click();
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
