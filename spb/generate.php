<?php
include('php/autoload.php');

if(isset($_GET['p']) && isset($_SESSION['activeImg'])){
	$generator = new ImageGenerator('img/designer/', '');
	$template = $_SESSION['activeImg'];
	$overlay = $_SESSION['activeOverlay'] === '' ? null : $_SESSION['activeOverlay'];
	$pos = $_GET['p'];
	try{
		$img = $generator->generate($pos, $template, $overlay);
		$_SESSION['lastPos'] = $_GET['p'];
	} catch(Exception $e){
		$img = imagecreatefrompng('img/error.png');
	}
} elseif(isset($_GET['t'])){
	$generator = new ImageGenerator('img/designer/', 'img/template/');
	$result = $db->query("SELECT templateId || '.' || filetype AS file, 
								 CASE WHEN isnull(overlayFiletype) THEN null ELSE templateId || '.' || overlayFiletype AS overlayFile, 
								 positions AS pos
						 FROM Templates
						 WHERE templateId = ?",
						 array($_GET['t']),
						 array(SQLITE3_TEXT));
	if($db->resultHasRows($result)){
		$row = $result->fetchArray();
		$templateId = $_GET['t'];
		$file = $row['file'];
		$overlayFile = $row['overlayFile'];
		$pos = $row['pos'];
		$img = $generator->generate($pos, $file, $overlayFile);
	} else{
		$img = imagecreatefrompng('img/error.png');
	}
}else{
	exit();
}

header('Content-Type: image/jpg');
imagejpeg($img);
imagedestroy($img);