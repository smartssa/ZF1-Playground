$(function() {
	$('.ajaxloader').click(function(event) {
		var target = $(this).attr('href');
		window.location.hash = target;
		$.ajax({
			  url: target,
			  success: function(data) {
			    $('#testresults').html(data);
			  }
			});
		return false;
	})
});
