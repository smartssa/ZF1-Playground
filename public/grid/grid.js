var grid_width = 11;
var grid_height = 6;
var grid_aspect = grid_width / grid_height;
var next_page = 2;
var curr_page = 1;
var prev_page = 5;

var pageNavigation = {
	current : curr_page,
	previous : prev_page,
	next : next_page,
	goNext : function() {
		window.location.hash = this.next;
		this.goPage(this.next);
	},
	goPrev : function() {
		window.location.hash = this.previous;
		this.goPage(this.previous);
	},
	goPage : function(page) {
		this.commonIn();
		// window.location.hash = page;
		$('#bucket').load('?noshell=1&page=' + page,
				function(responseText, textStatus, XMLHttpRequest) {
					pageNavigation.current = curr_page;
					pageNavigation.next = next_page;
					pageNavigation.previous = prev_page;
					pageNavigation.commonOut();
				});
	},
	commonIn : function() {
		resizeWindow();
	},
	commonOut : function() {
		resizeWindow();
	}
};

$(function() {
	
	// size this.
	$(window).resize(resizeWindow);
	resizeWindow();
	$('#prevPageLink').click(function(e) {
		e.preventDefault();
		pageNavigation.goPrev();
	});
	$('#nextPageLink').click(function(e) {
		e.preventDefault();
		pageNavigation.goNext();
	});

	$('a.pageNav').click(function(e) {
		e.preventDefault();
		var page = String($(this).attr('id')).substring(5);
		pageNavigation.goPage(page);
	});

	$(document).keydown(function(e) {

		if (e.keyCode == 37) {
			e.preventDefault();
			pageNavigation.goPrev();
		}
		if (e.keyCode == 39) {
			e.preventDefault();
			pageNavigation.goNext();
		}
	});


});

function resizeWindow() {
	var width = $(window).width();
	var height = $(window).height();

	var grid_padding = 11; // total padding both sides

	var grid_square_min_width = 130;
	var grid_square_min_height = 110;

	var min_width = grid_width * (grid_square_min_width + grid_padding);
	var min_height = grid_height * (grid_square_min_height + grid_padding);

	grid_square_height = grid_square_min_height;
	grid_square_width = grid_square_min_width;

	if (height < min_height) {
		height = min_height;
	}

	if (width < min_width) {
		width = min_width;
	}

	$('body').css('height', height);
	$('body').css('width', width);
	var body_margin_top = (height - ((grid_square_height + grid_padding) * grid_height)) / 2;

	$('body').css('margin-top', body_margin_top);
	$('div.grid_square').css('width', grid_square_width);
	$('div.grid_square').css('height', grid_square_height);
	$('div.grid_square_absolute').each(function(idx, val) {
		$(this).css('z-index', idx + 500);
		$(this).css('top', 0);
		$(this).css('left', 0);
	});
	$('div.grid_box_1_1').css('width', (grid_square_width * 1) + 0).css(
			'height', (grid_square_height * 1) + 0);
	$('div.grid_box_2_2').css('width', (grid_square_width * 2) + 10).css(
			'height', (grid_square_height * 2) + 10);
	$('div.grid_box_2_3').css('width', (grid_square_width * 2) + 10).css(
			'height', (grid_square_height * 3) + 20);
	$('div.grid_box_3_2').css('width', (grid_square_width * 3) + 20).css(
			'height', (grid_square_height * 2) + 10);
	$('div.grid_box_3_3').css('width', (grid_square_width * 3) + 20).css(
			'height', (grid_square_height * 3) + 20);
	$('div.grid_box_1_2').css('width', (grid_square_width * 1) + 0).css(
			'height', (grid_square_height * 2) + 10);
	$('div.grid_box_1_3').css('width', (grid_square_width * 1) + 0).css(
			'height', (grid_square_height * 3) + 20);
	$('div.grid_box_3_1').css('width', (grid_square_width * 3) + 20).css(
			'height', (grid_square_height * 1) + 0);
	$('div.grid_box_2_1').css('width', (grid_square_width * 2) + 10).css(
			'height', (grid_square_height * 1) + 0);
	$('div.grid_box_6_4').css('width', (grid_square_width * 6) + 50).css(
			'height', (grid_square_height * 4) + 30);
	$('div.grid_box_7_4').css('width', (grid_square_width * 7) + 60).css(
			'height', (grid_square_height * 4) + 30);
	// console.log(grid_square_width, grid_square_height);
	$('.grid_content').css('width', min_width);
	$('div.grid_square').show();
	$('div.grid_square_absolute').fadeIn();
}
