<?php
include('php/autoload.php');
if(!isset($_GET['id']) || !isset($_GET['s']) || !isset($_GET['t'])){
	echo ';failed';
	die();
}
$id = $_GET['id'];
$s = $_GET['s']; //state
$t = $_GET['t']; //type

if($s === 'a' && $t === 't'){
	echo $db->acceptTemplate($id);
	
} elseif($s === 'd' && $t === 't'){
	echo $db->denyTemplate($id);
	
} elseif($s === 'a' && $t === 's'){
	echo $db->acceptSourceImage($id);
	
} elseif($s === 'd' && $t === 's'){
	echo $db->denySourceImage($id);
	
} elseif($s === 'p' && $t === 't'){
	echo $db->makeTemplatePending($id);
	
} elseif($s === 'p' && $t === 's'){
	echo $db->makeSourceImagePending($id);
	
} else{
	echo ';failed';
}

$db->close();
?>