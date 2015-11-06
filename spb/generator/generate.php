<?php

require '../config/config.php';
require 'generator.php';

$db = new Database($dbfile);

if(isset($_GET['p'])){
	$img = generate($db, $tempath, $imgpath, 2, $_GET['p']);
}else{
	$img = generateRand($db, $tempath, $imgpath);
}

if (count(error_get_last())) exit();

header('Content-Type: image/jpg');
imagejpeg($img);
imagedestroy($img);