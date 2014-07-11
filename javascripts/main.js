$(document).ready(function(){
	$('#b2').on('click', function(){
		if($('#content h2').length != 0 && $('#content h2').text() === 'Coding'){return;}
		pressButton($('#b2'));
		oboe('files/coding.json')
		.on('done', function(data){
			$('#content').empty().removeClass('fadeIn').removeClass('animated');
			setTimeout(function(){$('#content').html(data.content).addClass('fadeIn').addClass('animated')}, 150);
		});
	});
	$('#b3').on('click', function(){
		pressButton($('#b3'))
		var today = new Date();
		var mm = today.getDate() + 1;
		var dd = today.getDate();
		oboe('http://www3.nd.edu/Departments/Maritain/etext/gkcday'+mm+'.htm')
		.on('done', function(data){
			console.log(data);
			// var s = data.split('<hr>');
			// $('#content').empty().removeClass('fadeIn').removeClass('animated');
			// setTimeout(function(){$('#content').html(s[dd]).addClass('fadeIn').addClass('animated')}, 150);
		}
	});
});

function pressButton(b) {
	$('button').removeClass('pure-button-active');
	b.addClass('pure-button-active');
}

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
