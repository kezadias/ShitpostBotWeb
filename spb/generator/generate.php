<?php

require '../config/config.php';
require 'generator.php';

$db = new Database($dbfile);

$img = generate($db, $tempath, $imgpath);

if (count(error_get_last())) exit();

header('Content-Type: image/jpg');
imagejpeg($img);
imagedestroy($img);