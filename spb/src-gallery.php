<?php
$ITEMS_PER_PAGE = 30;
include('php/autoload.php');
require("php/gallery-item.php");
$totalItemCount = $db->scalar('SELECT count(sourceId) FROM SourceImages', array(), array());
$page = max(isset($_GET['p']) ? $_GET['p'] : 1, 1);

$startIndex = ($page-1) * $ITEMS_PER_PAGE;
$sourceImages = $db->getSourceImages('SELECT * FROM SourceImages LIMIT ? OFFSET ?', array($ITEMS_PER_PAGE, $startIndex), array(SQLITE3_INTEGER, SQLITE3_INTEGER));
$items = array();
foreach($sourceImages as $sourceImage){
	$query = 'SELECT isPositive FROM SourceRatings WHERE userId = ? AND sourceId = ?';
	$userRating = $isLoggedIn ? $db->scalar($query, array($me->getUserId(), $sourceImage->getSourceId()), array_fill(0, 2, SQLITE3_TEXT)) : false;
	array_push($items, new GalleryItem(
		$sourceImage->getSourceId(), 
		$sourceImage->getImage(), 
		"view-srcimg.php?id=".$sourceImage->getSourceId(), 
		$sourceImage->getRating(), 
		new User($sourceImage->getUserId(), $sourceImage->getSubmitterName()),
		$userRating === false ? '-' : $userRating === 'y' ? 'p' : 'n'
	));
}

$pageCount = max(ceil($totalItemCount / $ITEMS_PER_PAGE), 1);
$page = min($page, $pageCount);
echo $twig->render('gallery.html', array('db' => $db, 'items' => $items, 'type' => 's', 'currentPage' => $page, 'pageCount' => $pageCount));
$db->close();
?>