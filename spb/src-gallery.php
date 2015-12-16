<?php
$ITEMS_PER_PAGE = 30;
include('php/autoload.php');
require("php/gallery-item.php");

$orderByReq = isset($_GET['by']) ? $_GET['by'] : 'time-acc';
switch($orderByReq){
	case 'submitter':
		$orderBy = 'ORDER BY u.username';
		break;
		
	case 'time-up':
		$orderBy = 'ORDER BY s.timeAdded';
		break;
		
	case 'time-acc':
		$orderBy = 'ORDER BY s.timeReviewed';
		break;
		
	default:
		$orderBy = "ORDER BY SUM(CASE WHEN r.isPositive = 'y' THEN 1 ELSE 0 END) - SUM(CASE WHEN r.isPositive = 'n' THEN 1 ELSE 0 END)";
}

$dir = isset($_GET['dir']) ? ($_GET['dir'] == 'asc' ? 'asc' : 'desc') : 'desc';

$totalItemCount = $db->scalar("SELECT count(sourceId) FROM SourceImages WHERE reviewState = 'a'", array(), array());
$page = max(isset($_GET['p']) ? $_GET['p'] : 1, 1);

$startIndex = ($page-1) * $ITEMS_PER_PAGE;
$query = "SELECT s.* FROM Users as u, SourceImages as s LEFT OUTER JOIN SourceRatings as r ON  r.sourceId = s.sourceId WHERE s.userId = u.userId AND reviewState = 'a' GROUP BY s.sourceId  $orderBy $dir LIMIT ? OFFSET ?";
$sourceImages = $db->getSourceImages($query, array($ITEMS_PER_PAGE, $startIndex), array(SQLITE3_INTEGER, SQLITE3_INTEGER));
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
		$userRating === false ? '-' : ($userRating === 'y' ? 'p' : 'n')
	));
}

$pageCount = max(ceil($totalItemCount / $ITEMS_PER_PAGE), 1);
$page = min($page, $pageCount);
echo $twig->render('gallery.html', array('items' => $items, 'type' => 's', 'currentPage' => $page, 'pageCount' => $pageCount, 'orderBy' => $orderByReq, 'dir' => $dir));
$db->close();
?>