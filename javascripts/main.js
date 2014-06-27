$(document).ready(function(){
	$('#b2').on('click', function(){
		if($('#content h2') != undefined && $('#content h2').text() === 'Coding'){return;}
		oboe('files/coding.json')
		.on('done', function(parsedjson){
			$('#content').empty().removeClass('fadeInRight animated');
			setTimeout(function(){$('#content').html(parsedjson.content).addClass('fadeInRight animated')}, 150);
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
