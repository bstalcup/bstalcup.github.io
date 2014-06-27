$(document).ready(function(){
	$('#b2').on('click', function(){
		if($('#content h2').length != 0 && $('#content h2').text() === 'Coding'){return;}
		oboe('files/coding.json')
		.on('done', function(data){
			$('button').removeClass('pure-button-active');
			$('#b2').addClass('pure-button-active');
			$('#content').empty().removeClass('fadeIn').removeClass('animated');
			setTimeout(function(){$('#content').html(data.content).addClass('fadeIn').addClass('animated')}, 150);
		});
	});
});

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
	// $('#b2').click(function(){
	// 	$.get('.json', function(data){
	// 		$('#content').clear().html(data.content).addClass('fadeInRight animated');
	// 	});
	// })
