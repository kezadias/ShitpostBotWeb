<?php
include('php/autoload.php');
$validItem = isset($_GET['i']);
if($validItem){
	$img = 'img/accepted/src/'.urldecode($_GET['i']);
	$validItem = file_exists($img);
}

echo $twig->render('view-srcimg.html', array('title' => 'Source Image', 'isValidItem' => $validItem, 'img' => $img));
$db->close();
?>