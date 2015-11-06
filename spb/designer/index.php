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
	<div class="col-md-6 header">Live View</div>
</div>
<div class="row">
	<div class="col-md-6 help">(This is where you draw your boxes)</div>
	<div class="col-md-6 help">(This shows you what the memes will look like)</div>
</div>
<canvas id='canvas'></canvas>
<img id='liveupdate'/><br>

<div class="row">
	<div class="col-md-1"><input type='button' class='control pen' id='1' value='Red'/></div>
	<div class="col-md-1"><input type='button' class='control pen' id='2' value='Green'/></div>
	<div class="col-md-1"><input type='button' class='control pen' id='3' value='Blue'/></div>
	<div class="col-md-1"><input type='button' class='control pen' id='4' value='Purple'/></div>
	<div class="col-md-1"><input type='button' class='control pen' id='5' value='Pink'/></div>
	<div class="col-md-1"><input type='button' class='control pen' id='6' value='Yellow'/></div>
	<div class="col-md-6"></div>
</div>
<span class="pull-right"><input type='button' class='control' id='refresh' value='Refresh Live View'/></span>
<div class="row">
	<div class="col-md-1"><input type='button' class='control pen' id='7' value='Orange'/></div>
	<div class="col-md-1"><input type='button' class='control pen' id='8' value='Cyan'/></div>
	<div class="col-md-1"><input type='button' class='control pen' id='9' value='Teal'/></div>
	<div class="col-md-1"><input type='button' class='control pen' id='10' value='Grey'/></div>
	<div class="col-md-1"><input type='button' class='control pen' id='11' value='Dark Green'/></div>
	<div class="col-md-1"><input type='button' class='control pen' id='12' value='Crimson'/></div>
	<div class="col-md-6"></div>
</div>
<div class="row">
	<div class="col-md-6"></div>
</div>
<?php
	$colours = array('red', 'green', 'blue', 'purple');
	foreach($colours as $colour){
		//addCategoriesComboBox($colour."cat", "control");
	}
?><br>
<input type='button' class='control pen' id='0' value='Eraser'/>
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