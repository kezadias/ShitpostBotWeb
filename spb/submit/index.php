<?php
$GlOBALS['title'] = 'ShitpostBot 5000 Submission';
$GlOBALS['style'] = '../style.css';
include('../header.php');

include('../dataaccess/loader.php');
?>

<form action='upload.php' method='post'>
	<input type='file' id='upload' name='upload' id='upload'/><br>
	
	<input type='radio' name='type' id='template' value='template' checked='true'/>
		<label for='template'> Template</label><br>
		
	<input type='radio' name='type' id='source' value='source'/>
		<label for='source'> Source Image</label><br><br>
		
	<input type='submit' value='Upload'/>
</form>

<script src="js/validation.js"></script>

<?php include('../footer.php'); ?>