<?php
require 'generator/generator.php';

if(isset($_GET['p']) /*& isset($_SESSION['activeImg'])*/){
	$generator = new ImageGenerator('img/designer');
	$template = 'img.png';
	$pos = $_GET['p'];
	$img = $generator->generate($template, $pos);
}else{
	$generator = new ImageGenerator('img/accepted');
	$template = $generator->getRandomTemplate();
	$t6eCode = pathinfo($template, PATHINFO_FILENAME);
	$pos = file_get_contents("img/accepted/t6e/$t6eCode.json");
	$img = $generator->generate($template, $pos);
}

if (count(error_get_last())) exit();

header('Content-Type: image/jpg');
imagejpeg($img);
imagedestroy($img);