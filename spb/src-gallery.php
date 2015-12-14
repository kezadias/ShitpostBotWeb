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
	array_push($items, new GalleryItem($sourceImage->getImage(), "view-srcimg.php?id=".$sourceImage->getSourceId(), $sourceImage->getRating()));
}

$pageCount = max(ceil($totalItemCount / $ITEMS_PER_PAGE), 1);
$page = min($page, $pageCount);
echo $twig->render('gallery.html', array('db' => $db, 'items' => $items, 'currentPage' => $page, 'pageCount' => $pageCount));
$db->close();
?>