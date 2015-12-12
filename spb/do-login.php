<?php
include('php/autoload.php');
if(!isset($_POST['spb-user']) || !isset($_POST['spb-pass'])){
	echo ';failed-internal-error';
} else{
	echo $db->login($_POST['spb-user'], $_POST['spb-pass']);
}
$db->close();
?>