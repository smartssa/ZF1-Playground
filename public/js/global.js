$.beautyOfCode.init({
	brushes : [ 'Xml', 'JScript', 'Php' ],
	ready : function() {
		$.beautyOfCode.beautifyAll();
	}
});

$(function() {
	// handle image thingy.
	$('.ajaxloader').click(function(event) {
		var target = $(this).attr('href');
		window.location.hash = target;
		$('#content').fadeOut('fast', function() {
			// complete fadeout, load new content while it's hiding!
			$.ajax({
				url : target,
				success : function(data) {
					$('#content').html(data);
					$('#content').fadeIn('slow', function() {
						$.beautyOfCode.beautifyAll();
					});
				}
			});
		});
		return false;
	});

	$('button[id^="button_"]').click(function(e) {
		// ajax request to trigger an event for the button.
		$.ajax({
			url : '/index/event-trigger/id/1/extra/' + $(this).attr('id'),
			success : function(data) {
				$("#append_me").append(data);
			}
		});
	});
});

function formatText(index, panel) {
	return index + "";
}

function startSlider() {
	$('.anythingSlider').anythingSlider({
		easing : "swing", // Anything other than "linear" or
		// "swing" requires the easing plugin
		autoPlay : true, // This turns off the entire FUNCTIONALY,
		// not
		// just if it starts running or not.
		delay : 5000, // How long between slide transitions in
		// AutoPlay
		// mode
		startStopped : false, // If autoPlay is on, this can force it
		// to
		// start stopped
		animationTime : 1000, // How long the slide transition takes
		hashTags : true, // Should links change the hashtag in the
		// URL?
		buildNavigation : false, // If true, builds and list of anchor
		// links
		// to link to each slide
		pauseOnHover : true, // If true, and autoPlay is enabled, the
		// show will pause on hover
		startText : "Go", // Start text
		stopText : "Stop", // Stop text
		navigationFormatter : formatText
	// Details at the top of the file on this use (advanced use)
	});

	$("#slide-jump").click(function() {
		$('.anythingSlider').anythingSlider(6);
	});
}

// $(function() {
// $('.ajaxnavloader').click(function(event) {
// var target = $(this).attr('href');
// window.location.hash = target;
// $('#subnav').fadeOut('slow', function() {
// // complete fadeout, load new content while it's hiding!
// $.ajax( {
// url : '/ajax/get-nav?path=' + escape(target),
// success : function(data) {
// $('#subnav').html(data);
// $('#subnav').fadeIn();
// }
// });
// });
// return false;
// })
// });
