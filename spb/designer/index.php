<?php
$GlOBALS['title'] = 'Template Designer';
$GlOBALS['style'] = '../style.css';
include('../header.php');

include("../dataaccess/loader.php");
$w = 625;
$h = 790;
?>

<link rel="stylesheet" href="css/style.css">
<div class="row">
  <div class="col-md-6 header">Canvas</div>
  <div class="col-md-6 header">Live Update</div>
</div>
<div class="row">
  <div class="col-md-6 help">(This is where you draw your boxes)</div>
  <div class="col-md-6 help">(This shows you what the memes will look like)</div>
</div>
<canvas id='canvas'></canvas>
<img id='liveupdate'/><br>

<input type='button' class='control' id='red' value='Red'/>
<input type='button' class='control' id='green' value='Green'/>
<input type='button' class='control' id='blue' value='Blue'/>
<input type='button' class='control' id='purple' value='Purple'/><br>
<?php
	$colours = array('red', 'green', 'blue', 'purple');
	foreach($colours as $colour){
		//addCategoriesComboBox($colour."cat", "control");
	}
?><br>
<input type='button' class='control' id='eraser' value='Eraser'/>
<input type='button' class='control' id='clear' value='Clear'/>
<input type='button' class='control' id='undo' value='Undo'/><br><br>

<input id='fillcolourchk' type='checkbox'/>Use Background Fill Colour?<br>
<input id='fillcolour' class='color'/><br><br>

<textarea id='json' value=''/></textarea><br>

<div id='rectlist'></div><br>
<div id='log'></div>

<script type="text/javascript" src="jscolor/jscolor.js"></script>
<script src="js/design.js"></script>
<script>
$(document).ready(function(){
	init(<?="$w, $h"?>); //before any of you whinge about this line of code, THIS IS TEMPORARY
	//THIS SHIT WILL BE REPLACED BY AJAX
	//so SIT YOUR ASSES DOWN and wait for everything else to be in place
});
</script>
<?php include('../footer.php');