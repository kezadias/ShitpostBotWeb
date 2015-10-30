<?php
#include $_SERVER['DOCUMENT_ROOT']."/spb/dataaccess/loader.php";
include "../dataaccess/loader.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Template Designer</title>
	<link rel="stylesheet" href="css/style.css">
</head>
<body>
	<canvas id='canvas'></canvas><br>

	<input type='button' id='red' value='Red'/>
	<input type='button' id='green' value='Green'/>
	<input type='button' id='blue' value='Blue'/>
	<input type='button' id='purple' value='Purple'/><br>
	<?php
		$colours = array('red', 'green', 'blue', 'purple');
		foreach($colours as $colour){
			addCategoriesComboBox($colour."cat");
		}
	?><br>

	<input type='button' id='clear' value='Clear'/>
	<input type='button' id='undo' value='Undo'/><br>
	
	<textarea id='json' value=''/></textarea><br>
	
	<div id='rectlist'></div><br>
	<div id='log'></div>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script src="js/design.js"></script>
</body>
</html>