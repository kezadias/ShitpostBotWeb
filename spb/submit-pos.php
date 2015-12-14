<?php
include('php/autoload.php');

function kill(){
	$_SESSION['lastPos'] = '[]';
	header('Location: designer.php');
	die;
}

if(!isset($_SESSION['lastPos'])){
	kill();
}

if(strlen($_SESSION['lastPos']) < 5){
	kill(); //there probably aren't any boxes drawn if the json is less than 5 chars long
}

try{
	json_decode($_SESSION['lastPos']);
	$json = $_SESSION['lastPos'];
	$generator = new ImageGenerator('img/designer/', '');
	$generator->generate($json, $_SESSION['activeImg']);
} catch(Exception $e){
	kill();
}

$templateId = $_SESSION['activeId'];
$templateFiletype = pathinfo($_SESSION['activeImg'], PATHINFO_EXTENSION);
$pos = $_SESSION['lastPos'];
$overlayFiletype = $_SESSION['activeOverlay'] == '' ? 'NONE' : pathinfo($_SESSION['activeOverlay'], PATHINFO_EXTENSION);
$result = $db->addTemplate($templateId, $pos, $templateFiletype, $overlayFiletype);
if($result === ';success'){
	$file = pathinfo($_SESSION['activeImg'], PATHINFO_BASENAME);
	$overlayFile = pathinfo($_SESSION['activeOverlay'], PATHINFO_BASENAME);
	rename('img/temp/'.$file, 'img/template/'.$file);
	if($overlayFile != ''){
		rename('img/temp/'.$overlayFile, 'img/template/'.$overlayFile);
	}
	header('Location: success.php');
} else{
	header('Location: submit.php?e='.urlencode('Database error: '.$result));
}
?>