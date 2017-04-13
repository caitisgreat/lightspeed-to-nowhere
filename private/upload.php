<?php  

	function __autoload($class_name) {
		include "../classes/". $class_name . ".php";
	}

	$uploadManager = new UploadManager();
	
	if($_POST['POST_STATE'] == 1) {
		$uploadManager->uploadComic();
	}
	
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
		<title>Lightspeed to Nowhere Administration - Upload Page</title>
		<link rel="stylesheet" type="text/css" href="../scripts/wmd-new/wmd.css" media="screen" charset="utf-8">
		<link rel="icon" type="image/png" href="images/favicon.png">
		<script type="text/javascript" src="../scripts/wmd-new/showdown.js"></script>
		<script type="text/javascript" src="../scripts/upload.js"></script>
	</head>
	<body>
		<form action="upload.php" method="post" enctype="multipart/form-data" name="uploadForm">
			<label for="title">Comic Title:</label>
			<input type="text" length="140" name="title" onkeyup="updateNewsTitle();">
			<label for="ckNewsTitle">Use same title for News?</label>
			<input type="checkbox" name="ckNewsTitle" onchange="enNewsTitle();" value="checked">
			<br>
			<label name="lblNewsTitle" for="newsTitle">News Title:</label>
			<input type="text" length="140" name="newsTitle">
			<br>
			<label for="poster">Poster:</label>
			<input type="radio" name="poster" value="Greg">Greg</input> <!-- to be replaced with login system -->
			<input type="radio" name="poster" value="Todd">Todd</input>
			<br>
			<label for="category">Category: </label>
			<select name="category">
				<option>-- Select a Category --</option><?php $uploadManager->printCategories(); ?>
			</select>
			<br>
			<label for="file">Filename:</label>
			<input type="hidden" name="MAX_FILE_SIZE" value="3145728">
			<input type="hidden" name="POST_STATE" value="1">
			<input type="file" name="file">
			<br>
			<label for="news">News:</label>
			<div id="wmd-button-bar" class="wmd-panel"></div>
			<textarea accept-charset="utf-8" class="wmd-panel" type="text" rows="20" cols="80" wrap="virtual" name="news" id="wmd-input"></textarea>
			<input type="submit" name="submit" value="Submit Comic">						
			<br>			
		</form>
		<script type="text/javascript" src="../scripts/wmd-new/wmd.js"></script>
	</body>
</html>
