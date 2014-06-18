$( window ).load(function() {
	$('#calendar').fullCalendar({
		events: [],
		header: {
			left: 'prev,next today',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		/*
		eventClick: function(cal_event, jsEvent, view){
			var $event_edit = $('#event-edit');
			$event_edit.find('#eventTitle').val(cal_event.title);
			$event_edit.find('#eventStartTime').val(cal_event.start.toTimeString().split(' ')[0]);
			$event_edit.find('#eventEndTime').val(cal_event.end.toTimeString().split(' ')[0]);
			$event_edit.find('#message').val(cal_event.description);
			var year = cal_event.start.getFullYear();
			var month = cal_event.start.getMonth() + 1 < 10 ? "0" + (cal_event.start.getMonth() + 1).toString() : cal_event.start.getMonth() + 1;
			var day = cal_event.start.getDate() < 10 ? "0" + cal_event.start.getDate() : cal_event.start.getDate();
			$event_edit.find('#eventDate').val(year + '-' + month + '-' + day);
			$event_edit.find('#event-id').val(cal_event.id);
			$event_edit.modal('show');
		},*/
		dayClick: function(date, allDay, jsEvent, view) {
			var year = date.getFullYear();
			var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1).toString() : date.getMonth() + 1;
			var day = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();
			$('#eventDate').val(year + '-' + month + '-' + day);
			$('#myModal').modal('show');
			$('#myModal').on('shown.bs.modal', function() {
				//$('#eventStartTime').delay(1000).focus();
				alert('hi');
			}); 
		}
	});
	
	$('#myModal').on('shown.bs.modal', function() {
		//$('#eventStartTime').delay(1000).focus();
		alert('hi');
	}); 
	
	/*
	$modals.on('shown', function(){
		$(this).find('#eventStartTime').focus();
	}); 
	*/
});