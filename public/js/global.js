$(function() {
	$('.ajaxloader').click(function(event) {
		var target = $(this).attr('href');
		window.location.hash = target;
		$('#content').fadeOut('slow', function() {
			// complete fadeout, load new content while it's hiding!
			$.ajax( {
				url : target,
				success : function(data) {
					$('#content').html(data);
					$('#content').fadeIn();
				}
			});
		});
		return false;
	})
});

//$(function() {
//	$('.ajaxnavloader').click(function(event) {
//		var target = $(this).attr('href');
//		window.location.hash = target;
//		$('#subnav').fadeOut('slow', function() {
//			// complete fadeout, load new content while it's hiding!
//			$.ajax( {
//				url : '/ajax/get-nav?path=' + escape(target),
//				success : function(data) {
//					$('#subnav').html(data);
//					$('#subnav').fadeIn();
//				}
//			});
//		});
//		return false;
//	})
//});
