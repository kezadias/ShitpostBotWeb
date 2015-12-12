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
$db->addTemplate($templateId, $pos, $templateFiletype, $overlayFiletype);
header('Location: success.php');
?>