<?php
$ITEMS_PER_PAGE = 30;
include('php/autoload.php');
require("php/gallery-item.php");

$totalItemCount = $db->scalar("SELECT count(templateId) FROM Templates WHERE reviewState = 'a'", array(), array());
$page = max(isset($_GET['p']) ? $_GET['p'] : 1, 1);

$startIndex = ($page-1) * $ITEMS_PER_PAGE;
$templates = $db->getTemplates("SELECT * FROM Templates WHERE reviewState = 'a' LIMIT ? OFFSET ?", array($ITEMS_PER_PAGE, $startIndex), array(SQLITE3_INTEGER, SQLITE3_INTEGER));
$items = array();
foreach($templates as $template){
	$query = 'SELECT isPositive FROM TemplateRatings WHERE userId = ? AND templateId = ?';
	$userRating = $isLoggedIn ? $db->scalar($query, array($me->getUserId(), $template->getTemplateId()), array_fill(0, 2, SQLITE3_TEXT)) : false;
	array_push($items, new GalleryItem(
		$template->getTemplateId(), 
		$template->getImage(), 
		"view-t6e.php?id=".$template->getTemplateId(), 
		$template->getRating(), 
		new User($template->getUserId(), $template->getSubmitterName()),
		$userRating === false ? '-' : $userRating === 'y' ? 'p' : 'n'
	));
}

$pageCount = max(ceil($totalItemCount / $ITEMS_PER_PAGE), 1);
$page = min($page, $pageCount);
echo $twig->render('gallery.html', array('items' => $items, 'type' => 't', 'currentPage' => $page, 'pageCount' => $pageCount));
$db->close();
?>