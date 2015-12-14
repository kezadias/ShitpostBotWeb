<?php
include('php/autoload.php');
if(!isset($_GET['t']) || !isset($_GET['id']) || !isset($_GET['r'])){
	echo ';failure-internal';
	$db->close();
	die();
}

$type = $_GET['t'];
$id = $_GET['id'];
$rating = $_GET['r'];
$mainTableName = $type == 't' ? 'Templates' : 'SourceImages';
$tablename = $type == 't' ? 'TemplateRatings' : 'SourceRatings';
$idFieldName = $type == 't' ? 'templateId' : 'sourceId';
if($rating === 'y' || $rating === 'n'){
	if($db->scalar("SELECT count($idFieldName) FROM $mainTableName WHERE $idFieldName = ?", array($id), array(SQLITE3_TEXT)) > 0){
		$result = $db->addRating($tablename, $idFieldName, $id, $rating);
		if($result == ';success'){
			$query = "SELECT count(*) AS total FROM TemplateRatings WHERE templateId = ? AND isPositive = ?";
			$positives = $db->scalar($query, array($id, 'y'), array_fill(0, 2, SQLITE3_TEXT));
			$negatives = $db->scalar($query, array($id, 'n'), array_fill(0, 2, SQLITE3_TEXT));
			$newRating = $positives - $negatives;
			echo "$result($newRating)";
		}else{
			echo $result;
		}
		$db->close();
		die();
	}
}

echo ";failure-invalid-args";
$db->close();

?>