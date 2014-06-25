window.onload = function(){
	$('#b2').click(function(){
		$.get('coding.json', function(data){
			$('#content').empty().removeClass('fadeInRight animated');
			console.log($('#content'));
			$('#content').html(data.content).addClass('fadeInRight animated');
		});
	})
	// $('#b2').click(function(){
	// 	$.get('.json', function(data){
	// 		$('#content').clear().html(data.content).addClass('fadeInRight animated');
	// 	});
	// })
	// $('#b2').click(function(){
	// 	$.get('.json', function(data){
	// 		$('#content').clear().html(data.content).addClass('fadeInRight animated');
	// 	});
	// })
	// $('#b2').click(function(){
	// 	$.get('.json', function(data){
	// 		$('#content').clear().html(data.content).addClass('fadeInRight animated');
	// 	});
	// })
}