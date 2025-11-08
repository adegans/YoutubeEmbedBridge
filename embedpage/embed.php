<?php
$video_id = (isset($_GET['vid'])) ? $_GET['vid'] : '';
$video_title = (isset($_GET['vt'])) ? $_GET['vt'] : 'Youtube Embed Video';
$video_width = (isset($_GET['vw'])) ? $_GET['vw'] : '70';

$video_id = stripslashes(trim($video_id));
$video_title = htmlspecialchars_decode(stripslashes(trim($video_title)), ENT_QUOTES);
$video_width = stripslashes(trim($video_width));
?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>RSS Bridge: <?php echo $video_title; ?></title>
	<link rel="stylesheet" href="./embed-simple.css">
</head>

<body id="top">
	<header>
		<h1><?php echo $video_title; ?></h1>
	</header>
	
	<main>
		<section id="text">

			<?php if(!empty($video_id)) { ?>
			<div class="videowrap" style="width: <?php echo $video_width; ?>%;">
				<iframe 
					src="https://www.youtube.com/embed/<?php echo $video_id; ?>" 
					title="YouTube video player" 
					frameborder="0" 
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
					referrerpolicy="strict-origin-when-cross-origin" 
					allowfullscreen
				></iframe>
			</div>
			<?php } else { ?>
			<p>Please provide a video ID.</p>
			<?php } ?>

		</section>
	</main>

	<script src="./embed-keepalive.js"></script>

</body>
</html>