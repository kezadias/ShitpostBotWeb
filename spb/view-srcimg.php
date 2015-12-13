<?php
include('php/autoload.php');
$validItem = isset($_GET['id']);
$sourceImage = null;
if($validItem){
	$id = $_GET['id'];
	$sourceImages = $db->getSourceImages("SELECT * FROM SourceImages WHERE sourceId = ? AND reviewState = 'a'", array($id), array(SQLITE3_TEXT));
	if(count($sourceImages) > 0){
		$sourceImage = $sourceImages[0];
	} else{
		$validItem = false;
	}
}

echo $twig->render('view-srcimg.html', array('title' => 'Source Image', 'isValidItem' => $validItem, 's' => $sourceImage));
$db->close();
?>