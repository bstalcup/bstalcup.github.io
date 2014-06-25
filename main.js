window.onload = function(){
	$('button').first().click(function(){
		$.get('philosophy.json', function(data){
			$('#content').html(data.content);
		});
	});
}