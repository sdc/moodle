require(['jquery'], function($) {	
	var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
	$('#region-main').find('.course-content').find('li.activitytask').find('a.activitytask-button').click(function(event) {
		event.preventDefault();
		var parent = $(this).parent();
		var url = $(this).remove().attr('href');
		$.get(url, function(data) {
			var now = new Date();
			parent.append('<span class="activitytask-done">('+months[now.getMonth()]+' '+now.getDate()+')</span>');
			var img = parent.closest('.activitytask').find('.autocompletion').children('img.smallicon');
			if(img.length) {
				var src = img.attr('src').replace(/\/[a-z0-9-_]+$/i, '/completion-auto-y');
				img.attr('src', src);
			}			
		}).fail(function() { 
			window.location.href = url; 
		});		
	});	
});