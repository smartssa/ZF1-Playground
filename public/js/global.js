$(function() {
	$('.ajaxloader').click(function(event) {
		var target = $(this).attr('href');
		window.location.hash = target;
		$('#testresults').fadeOut('slow', function() {
			// Animation complete
			$.ajax( {
				url : target,
				success : function(data) {
					$('#testresults').html(data);
					$('#testresults').fadeIn();
				}
			});
		});
		return false;
	})
});
