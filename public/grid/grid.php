<?php

	$width  = 11; // build a 12x6 grid
	$height = 6;
	$aspect = $width / $height;
	$total  = $width * $height;

	$grid_allocation = array();
	$data_source     = array();
	if ($_GET['page'] == 1 || $_GET['page'] == '') {
		// which grid segment  ... which boxes are owned by this
		// this should come from some sort of dynamic, magic, thing.
		$grid_allocation[1] = array('start' => 14, 'width' => 2, 'height' => 2);
		$grid_allocation[2] = array('start' => 18, 'width' => 2, 'height' => 1);
		$grid_allocation[3] = array('start' => 27, 'width' => 3, 'height' => 2);
		$grid_allocation[4] = array('start' => 37, 'width' => 1, 'height' => 1);
		$grid_allocation[5] = array('start' => 46, 'width' => 2, 'height' => 1);
		$grid_allocation[6] = array('start' => 49, 'width' => 1, 'height' => 1);
		$grid_allocation[7] = array('start' => 30, 'width' => 2, 'height' => 2);
		$grid_allocation[8] = array('start' => 51, 'width' => 3, 'height' => 1);
		$grid_allocation[9] = array('start' => 32, 'width' => 1, 'height' => 1);
		$grid_allocation[10] = array('start' => 2, 'width' => 2, 'height' => 2);
		$data_source[1]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[2]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[3]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[4]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[5]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[6]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[7]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[8]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[9]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[10]   = array('type' => 'text', 'src' => "<h1>Look at me</h1><p>Hey, I'm a text box!</p>");
		$prev_page = 5;
		$curr_page = 1;
		$next_page = 2;
	} else if ($_GET['page'] == 2) {
		$grid_allocation[1] = array('start' => 46, 'width' => 1, 'height' => 1);
		$grid_allocation[2] = array('start' => 14, 'width' => 3, 'height' => 3); // 14, 15, 16, 25, 26, 27, 36, 37, 38);
		$grid_allocation[3] = array('start' => 39, 'width' => 2, 'height' => 2); // (39, 40, 50, 51);
		$grid_allocation[4] = array('start' => 18, 'width' => 2, 'height' => 1); // (18, 19);
		$grid_allocation[5] = array('start' => 30, 'width' => 2, 'height' => 3); // (30, 31, 41, 42, 52, 53);
		$grid_allocation[6] = array('start' => 32, 'width' => 1, 'height' => 2); // (32, 43);
		$data_source[1]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[2]    = array('type' => 'video', 'src' => '<iframe src="http://www.youtube.com/embed/03kZSHR2U-A" frameborder="0" allowfullscreen></iframe>');
		$data_source[3]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[4]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[5]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[6]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$prev_page = 1;
		$curr_page = 2;
		$next_page = 3;
	} else if ($_GET['page'] == 3) {
		$grid_allocation[1] = array('start' => 14, 'width' => 7, 'height' => 4);
		$data_source[1]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$prev_page = 2;
		$curr_page = 3;
		$next_page = 4;
	} else if ($_GET['page'] == 4) {
		$grid_allocation[1] = array('start' => 13, 'width' => 2, 'height' => 2);
		$grid_allocation[2] = array('start' => 26, 'width' => 1, 'height' => 1);
		$grid_allocation[3] = array('start' => 16, 'width' => 2, 'height' => 3);
		$grid_allocation[4] = array('start' => 29, 'width' => 1, 'height' => 2);
		$grid_allocation[5] = array('start' => 31, 'width' => 2, 'height' => 1);
		$grid_allocation[6] = array('start' => 47, 'width' => 2, 'height' => 1);
		$grid_allocation[7] = array('start' => 50, 'width' => 2, 'height' => 1);
		$grid_allocation[8] = array('start' => 41, 'width' => 2, 'height' => 2);
		$data_source[1]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[2]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[3]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[4]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6081/6116454954_a2e521a086_b.jpg');
		$data_source[5]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[6]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[7]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6081/6116454954_a2e521a086_b.jpg');
		$data_source[8]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$prev_page = 3;
		$curr_page = 4;
		$next_page = 5;
	} else if ($_GET['page'] == 5) {
		$grid_allocation[1] = array('start' => 13, 'width' => 3, 'height' => 2);
		$grid_allocation[2] = array('start' => 18, 'width' => 2, 'height' => 2);
		$grid_allocation[3] = array('start' => 21, 'width' => 1, 'height' => 1);
		$grid_allocation[4] = array('start' => 38, 'width' => 2, 'height' => 2);
		$grid_allocation[5] = array('start' => 41, 'width' => 3, 'height' => 2);
		$grid_allocation[6] = array('start' => 46, 'width' => 2, 'height' => 1);
		$data_source[1]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[2]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6081/6116454954_a2e521a086_b.jpg');
		$data_source[3]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[4]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6081/6116454954_a2e521a086_b.jpg');
		$data_source[5]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$data_source[6]    = array('type' => 'image', 'src' => 'http://farm7.static.flickr.com/6182/6116451888_c2121a917e_b.jpg');
		$prev_page = 4;
		$curr_page = 5;
		$next_page = 1;
	}



	// reversal of 'em
	$grid_reversal = array();
	$grid_box_size = array();
	foreach ($grid_allocation as $key => $boxes) {
		foreach ($boxes as $bkey => $box) {
			if ($bkey == 'start') {
				$grid_reversal[$box] = $key;
			} elseif ($bkey == 'width') {
				$bwidth = $box;
			} elseif ($bkey == 'height') {
				$bheight = $box;
			}
		}
		$grid_box_size[$key] = 'grid_box_' . $bwidth . '_' . $bheight;
	}
	?>
<?php if ($_GET['noshell'] != 1) { ?>
<!DOCTYPE html>
<html class="no-js">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=0.65">
<title>Page Title</title>
<script src="http://code.jquery.com/jquery-1.6.3.min.js"></script>
<script src="modernizr.js"></script>
<script src="grid.js"></script>
<style type="text/css">
body {
	margin: 0;
	padding: 0;
	background: white;
	overflow: hidden;
}

h1 {
	margin: 5px;
}

p {
	margin: 5px;
}

div {
	margin: 0;
	padding: 0;
	border: 0;
}

div.grid_content {
	margin-left: auto;
	margin-right: auto;
	overflow: hidden;
}

div.grid_square {
	z-index: -100;
	padding: 0;
	margin: 5px;
	display: none;
	float: left;
	background: white;
	border: 0px solid black;
}

div.grid_square img {
	max-height: 100%;
	max-width: 100%;
}

div.grid_square iframe {
	height: 100%;
	width: 100%;
}
div.grid_allocated {
	
}

div.grid_square_absolute {
	display: none;
	overflow: hidden;
	position: relative;
	background: #eeffee;
	border: 1px solid black;
	position: relative;
	overflow: hidden;
}
</style>
</head>


<body>
	<div id="bucket">
<?php } ?>
	<div class="grid_content">
		<!--  render all the content boxes here, then align then in JS before 'showing' the page. -->
<?php 
	for ($i = 1; $i <= $total; $i++) {
		if ($grid_reversal[$i]) {
			echo '<div id="grid_'.$i.'" class="grid_square grid_allocated">';
			echo '<div class="grid_square_absolute '.$grid_box_size[$grid_reversal[$i]].'">';
			if ($data_source[$grid_reversal[$i]]['type'] == 'image') {
				echo '<img src="' . $data_source[$grid_reversal[$i]]['src'] . '">';
			} elseif ($data_source[$grid_reversal[$i]]['type'] == 'text') {
				echo $data_source[$grid_reversal[$i]]['src'];
			} elseif ($data_source[$grid_reversal[$i]]['type'] == 'video') {
				echo $data_source[$grid_reversal[$i]]['src'];
			}
			echo '</div>';
			echo '</div>';
		} else {
			echo '<div id="grid_'.$i.'" class="grid_square"></div>';
		}

		if ($i % $width == 0) {
			echo '<div style="clear: both;"></div>';
		}
	}

	?>
<script>
grid_width  = <?php echo $width; ?>;
grid_height = <?php echo $height; ?>;
grid_aspect = <?php echo $aspect; ?>;
next_page   = <?php echo $next_page; ?>;
curr_page   = <?php echo $curr_page; ?>;
prev_page   = <?php echo $prev_page; ?>;
</script>
</div>
	<?php if ($_GET['noshell'] != 1) { ?>
	
	</div>
	<div>
	
		<a href="" id="prevPageLink">Previous Page </a> || <a href="" id="nextPageLink">Next Page</a>
		<br/>
			<a href="?page=1" class="pageNav" id="page_1">page 1</a> | <a href="?page=2" class="pageNav" id="page_2">page 2</a> | <a
				href="?page=3" class="pageNav" id="page_3">page 3</a> | <a href="?page=4" class="pageNav" id="page_4">page 4</a> | <a
				href="?page=5" class="pageNav" id="page_5">page 5</a>
		</div>
</body>
</html>
<?php } ?>
