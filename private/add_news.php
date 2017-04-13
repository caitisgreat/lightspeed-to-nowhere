<?php  

	function __autoload($class_name) {
		include "../classes/". $class_name . ".php";
	}

	$uploadManager = new UploadManager();
	
	if($_POST['POST_STATE'] == 1) {
		$uploadManager->submitNews();
	}
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<title>Lightspeed to Nowhere Administration - Add News</title>
		<link rel="stylesheet" type="text/css" href="../scripts/wmd-new/wmd.css" media="screen" charset="utf-8">
		<link rel="icon" type="image/png" href="images/favicon.png">
		<script type="text/javascript" src="../scripts/wmd-new/showdown.js"></script>
		<script type="text/javascript" src="../scripts/upload.js"></script>
	</head>
	<body>
		<form action="add_news.php" method="post" enctype="multipart/form-data">
			<label for="newsTitle">News Title:</label>
			<input type="text" length="140" name="newsTitle">
			<br>
			<label for="forPost">Comic:</label>
			<select id="forPost" name="forPost">
			<?php $uploadManager->listLatestComics(); ?>
			</select>			
			<br>
			<label for="poster">Poster:</label>
			<input type="radio" name="poster" value="Greg">Greg</input> <!-- to be replaced with login system -->
			<input type="radio" name="poster" value="Todd">Todd</input>
			<br>
			<input type="hidden" name="POST_STATE" value="1">
			<br>
			<label for="news">News:</label>
			<div id="wmd-button-bar" class="wmd-panel"></div>
			<textarea accept-charset="utf-8" class="wmd-panel" type="text" rows="20" cols="80" wrap="virtual" name="news" id="wmd-input"></textarea>
			<input type="submit" name="submit" value="Submit News">						
			<br>			
		</form>
		<script type="text/javascript" src="../scripts/wmd-new/wmd.js"></script>
	</body>
</html>
