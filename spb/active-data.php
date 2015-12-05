<?php
include('php/autoload.php');
session_start();
header('Content-Type: application/json');
$fullpath = 'img/pending/t6e/'.$_SESSION['activeTemplate'];
$size = getimagesize($fullpath);
echo json_encode(array('x' => $size[0], 'y' => $size[1], 'path' => $fullpath));
?>