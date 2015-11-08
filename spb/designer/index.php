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

<p class='help' style='width: 600px'>Use different colours for different images, and the same colour in multiple places to have the same image used across them all</p>
<div class="row palette">
	<div class="col-md-1 tbutton pen noselect active" id='1'>Red</div>
	<div class="col-md-1 tbutton pen noselect" id='2'>Green</div>
	<div class="col-md-1 tbutton pen noselect" id='3'>Blue</div>
	<div class="col-md-1 tbutton pen noselect" id='4'>Purple</div>
	<div class="col-md-1 tbutton pen noselect" id='5'>Pink</div>
	<div class="col-md-1 tbutton pen noselect" id='6'>Yellow</div>
	<div class="col-md-6"></div>
</div>
<span class="pull-right"><input type='button' class='control' id='refresh' value='Refresh Live View'/></span><br>
<div class="row palette">
	<div class="col-md-1 tbutton pen noselect" id='7'>Orange</div>
	<div class="col-md-1 tbutton pen noselect" id='8'>Cyan</div>
	<div class="col-md-1 tbutton pen noselect" id='9'>Teal</div>
	<div class="col-md-1 tbutton pen noselect" id='10'>Grey</div>
	<div class="col-md-1 tbutton pen noselect" id='11'>DGreen</div>
	<div class="col-md-1 tbutton pen noselect" id='12'>Crimson</div>
	<div class="col-md-6"></div>
</div><br>
<div class="row palette">
	<div class="col-md-1 tbutton pen" id='0'>Eraser</div>
	<div class="col-md-11"></div>
</div><br>
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
<?php include('../footer.php'); ?>