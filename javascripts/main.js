$(document).ready(function(){
	$('#b2').on('click', function(){
		if($('#content h2').length != 0 && $('#content h2').text() === 'Coding'){return;}
		oboe('files/coding.json')
		.on('done', function(data){
			$('#content').empty().removeClass('fadeInRight animated');
			setTimeout(function(){$('#content').html(data.content)}, 500);//.addClass('fadeInRight animated')}, 150);
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
