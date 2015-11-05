<?php
$GlOBALS['title'] = 'Template Designer';
$GlOBALS['style'] = '../style.css';
include('../header.php');

include("../dataaccess/loader.php");
?>

<link rel="stylesheet" href="css/style.css">
<div id='canvasdiv'><canvas id='canvas' width='625' height='790'></canvas></div><br>

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
<input type='button' id='eraser' value='Eraser'/>
<input type='button' id='clear' value='Clear'/>
<input type='button' id='undo' value='Undo'/><br><br>

<input id='fillcolourchk' type='checkbox'/>Use Background Fill Colour?<br>
<input id='fillcolour' class='color'/><br><br>

<textarea id='json' value=''/></textarea><br>

<div id='rectlist'></div><br>
<div id='log'></div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="jscolor/jscolor.js"></script>
<script src="js/design.js"></script>
<?php include('../footer.php');