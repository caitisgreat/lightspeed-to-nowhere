<?php

	function __autoload($class_name) {
		include "classes/". $class_name . ".php";
	}

	$session = new visitSession();
	$comicManager = new ComicManager($_SESSION['cid']);
	$comic = $comicManager->comic;

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<meta name="description" content="Lightspeed to Nowhere: a webcomic about two enterprising gamers getting lost in the void.">		
		<title>Lightspeed to Nowhere - <?php echo $comic->title; ?></title>
		<link rel="stylesheet" type="text/css" href="style.css" media="screen" charset="utf-8">
		<link rel="icon" type="image/png" href="images/favicon.png">
		<link rel="alternate" type="application/atom+xml" href="feed.xml">		
	</head>
	<body>
		<div class="wingLeft"></div>
		<div class="wingRight"></div>
		<?php $comicManager->categoryBanner(); ?>
		<div id="Header">
			<a class="logo" href="http://www.lstncomic.com/" title="Lightspeed to Nowhere"></a>
			<div class="leaderboard">				
				<!-- <script type="text/javascript" src="http://www.projectwonderful.com/ad_display.js"></script> <-->
			</div>
			<div class="socialWrap">
			<p>SUBSCRIBE</p>
			<ol class="social">
				<li><a class="icon" id="DeviantArt" href="http://lightspeedtonowhere.deviantart.com/"></a></li>
				<li><a class="icon" id="Facebook" href="http://www.facebook.com/pages/Lightspeed-to-Nowhere/175309919146003"></a></li>
				<li><a class="icon" id="RSSFeed" href="http://www.lstncomic.com/feed.xml"></a></li>
				<li><a class="icon" id="Twitter" href="http://www.twitter.com/lstncomic"></a></li> 
			</ol>
			</div>
			<ol class="locations">
				<li><a class="loc" id="LocMain" href="index.php"></a></li>
				<li><img src="images/locations/shop_soon.png" alt="Shop Coming Soon"></li>
				<li><a class="loc" id="LocArchive" href="archive.php"></a></li>
				<li><a class="loc" id="LocForum" href="forums/index.php"></a></li>
				<li><a class="loc" id="LocAbout" href="about.php"></a></li>
				<li><a href="links.php"><img src="images/locations/links_lit.png" alt="Links"></a></li>
			</ol>
		</div>
		<div id="Index">
			<div class="ComicWrap">
				<h1 class="title"><?php echo $comic->title; ?></h1>
				<h4><?php echo $comic->category; ?></h4>
				<img class="Comic" src="<?php echo $comic->filePath; ?>" alt="lstncomic <?php $comicManager->comicCreationDate();?> <?php echo strtolower($comic->title); ?>">
				<ol class="Navigation">
					<li class="First"><?php $comicManager->navFirst(); ?>
					<li class="Back"><?php $comicManager->navLeft(); ?>
					<li class="Random"><?php $comicManager->navRandom(); ?>
					<li class="Next"><?php $comicManager->navRight(); ?>
					<li class="New"><?php $comicManager->navNew(); ?>
				</ol>
			</div>
			<div class="BodyWrap">
				<?php $comicManager->printNews(); ?>
				<fieldset>
					<legend>Comic Categories</legend>
					<hr>
					<ul><?php $comicManager->setupCategories(); ?></ul>
				</fieldset>
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

