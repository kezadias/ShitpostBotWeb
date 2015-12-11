<?php
include('php/autoload.php');
$validItem = isset($_GET['i']);
if($validItem){
	$img = 'img/accepted/t6e/'.urldecode($_GET['i']);
	$validItem = file_exists($img);
	if($validItem){
		$meme = 'generate.php?t='.$_GET['i'];
		$code = pathinfo(urldecode($_GET['i']), PATHINFO_FILENAME);
		$overlay = "img/accepted/t6e/$code-overlay.png";
		$hasOverlay = file_exists($overlay);
	}
}

echo $twig->render('view-t6e.html', array('title' => 'Template', 'isValidItem' => $validItem, 'img' => $img, 'meme' => $meme, 'hasOverlay' => $hasOverlay, 'overlay' => $overlay));
$db->close();
?>