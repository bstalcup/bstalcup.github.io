window.onload = function(){
	$('#b2').click(function(){
		$.get('coding.json', function(data){
			$('#content').empty().removeClass('fadeInRight animated');
			setTimeout(function(){$('#content').html(data.content).addClass('fadeInRight animated')}, 250);
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