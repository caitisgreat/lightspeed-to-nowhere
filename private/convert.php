<?php  

	function __autoload($class_name) {
		include "../classes/". $class_name . ".php";
	}

	$XMLManager = new XMLManager();
	
	if($_POST['POST_STATE'] == 1) {
		$XMLManager->xml2db($_POST['id'], $_POST['category']);
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
	</head>
	<body>
		<form action="convert.php" method="post" enctype="multipart/form-data">
			<label for="title">Enter Comic Id:</label>
			<input type="text" length="8" name="id">
			<br>
			<select name="category">
				<option>-- Select a Category --</option><?php $XMLManager->printCategories(); ?>
			</select>
			<br>
			<input type="hidden" name="POST_STATE" value = "1">
			<input type="submit" name="submit" value="Convert">						
			<br>			
		</form>
	</body>
</html>
