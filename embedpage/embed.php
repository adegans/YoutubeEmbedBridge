<?php
// Fetch the url parameters
$video_id = (isset($_GET['vid'])) ? $_GET['vid'] : '';
$video_title = (isset($_GET['vt'])) ? $_GET['vt'] : 'Youtube Embed Video';
$video_quality = (isset($_GET['vq'])) ? $_GET['vq'] : 'hd720';

// Basic 'security' and formatting
$video_id = stripslashes(trim($video_id));
$video_title = htmlspecialchars_decode(stripslashes(trim($video_title)), ENT_QUOTES);
$video_quality = stripslashes(trim($video_quality));

// Figure out the URL (for sharing this page)
$current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$current_url .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
?>

<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>RSS Bridge: <?php echo $video_title; ?></title>
	<link rel="stylesheet" href="./embed-simple.css">

	<meta name="description" content="<?php echo $video_title; ?>" />
	<meta name="generator" content="RSS Bridge - Youtube Embeds" />

	<meta property="og:type" content="website" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:url" content="<?php echo $current_url; ?>" />
	<meta property="og:site_name" content="RSS Bridge - Youtube Embeds" />
	<meta property="og:title" content="Watch this embedded video:" />
	<meta property="og:description" content="<?php echo $video_title; ?>" />
</head>

<body id="top">
	<header>
		<h1><?php echo $video_title; ?></h1>
	</header>
	
	<main>
		<section id="text">

			<?php if(!empty($video_id)) { ?>
			<div class="videowrap">
				<iframe 
					src="https://www.youtube.com/embed/<?php echo $video_id; ?>?vq=<?php echo $video_quality; ?>" 
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

	<footer>
		<p>This page does not store or distribute videos.</p>
	</footer>

	<script src="./embed-keepalive.js"></script>

</body>
</html>