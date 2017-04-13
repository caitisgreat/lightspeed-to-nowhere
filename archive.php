<?php

	function __autoload($class_name) {
		include "classes/". $class_name . ".php";
	}

	$archive = new Archive();
	$session = new visitSession();
	
	$month = $_GET["m"];
	$year = $_GET["y"];
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<meta name="description" content="Lightspeed to Nowhere: a webcomic about two enterprising gamers getting lost in the void.">
		<title>Lightspeed to Nowhere - Archive</title>
		<link rel="stylesheet" type="text/css" href="style.css" media="screen" charset="utf-8">
		<link rel="icon" type="image/png" href="images/favicon.png">
		<link rel="alternate" type="application/atom+xml" href="feed.xml">
	</head>
	<body>
		<div class="wingLeft"></div>
		<div class="wingRight"></div>
		<div id="Header">
			<a class="logo" href="http://www.lstncomic.com/" title="Lightspeed to Nowhere"></a>
			<div class="leaderboard">
				<!-- <script type="text/javascript" src="http://www.projectwonderful.com/ad_display.js"></script> <-->
			</div>
			<div class="socialWrap">
			<p>SUBSCRIBE</p>
			<ol class="social">
				<li><a title="Watch us on deviantART!" class="icon" id="DeviantArt" href="http://lightspeedtonowhere.deviantart.com/"></a></li>
				<li><a title="Like us on Facebook!" class="icon" id="Facebook" href="http://www.facebook.com/pages/Lightspeed-to-Nowhere/175309919146003"></a></li>
				<li><a title="Subscribe to our feed!" class="icon" id="RSSFeed" href="http://www.lstncomic.com/feed.xml"></a></li>
				<li><a title="Follow us on Twitter!" class="icon" id="Twitter" href="http://www.twitter.com/lstncomic"></a></li> 
			</ol>
			</div>
			<ol class="locations">
				<li><a title="Main Index" class="loc" id="LocMain" href="index.php"></a></li>
				<li><img src="images/locations/shop_soon.png" alt="Shop Coming Soon"></li>
				<li><a title="Comic Archives" href="archive.php"><img src="images/locations/archive_lit.png" alt="Archive"></a></li>
				<li><a title="Message Board" class="loc" id="LocForum" href="forums/index.php"></a></li>
				<li><a title="About Lightspeed to Nowhere" class="loc" id="LocAbout" href="about.php"></a></li>
				<li><a title="Webcomic Allies" class="loc" id="LocLinks" href="links.php"></a></li>
			</ol>
		</div>
		<div id="Archive">
			<div class="topwide">
				<?php $archive->calNavLeft(); ?>
				<?php $archive->calGenerate($month-1, $year); ?>
				<?php $archive->calGenerate($month, $year); ?>
				<?php $archive->calGenerate($month+1, $year); ?>
				<?php $archive->calNavRight(); ?>
			</div>
			<div class="container">
				<?php $archive->calThumbnails($month, $year); ?>
				<div class="skyscraper">					
					<!-- <script type="text/javascript" src="http://www.projectwonderful.com/ad_display.js"></script><-->
				</div>
			</div>			
		</div>
		<div id="Footer">
			<p>Lightspeed to Nowhere LLC &copy; 2011.  <a class="invert" rel="license" href="http://creativecommons.org/licenses/by-nc-sa/3.0/">Some Rights Reserved</a>.</p>
		</div>
	</body>
</html>

