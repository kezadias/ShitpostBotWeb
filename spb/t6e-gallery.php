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
		$orderBy = 'ORDER BY t.timeAdded';
		break;
		
	case 'time-acc':
		$orderBy = 'ORDER BY t.timeReviewed';
		break;
		
	default:
		$orderBy = "ORDER BY SUM(CASE WHEN r.isPositive = 'y' THEN 1 ELSE 0 END) - SUM(CASE WHEN r.isPositive = 'n' THEN 1 ELSE 0 END)";
}

$dir = isset($_GET['dir']) ? ($_GET['dir'] == 'asc' ? 'asc' : 'desc') : 'desc';

$totalItemCount = $db->scalar("SELECT count(templateId) FROM Templates WHERE reviewState = 'a'", array(), array());
$page = max(isset($_GET['p']) ? $_GET['p'] : 1, 1);

$startIndex = ($page-1) * $ITEMS_PER_PAGE;
$query = "SELECT t.* FROM Users as u, Templates as t LEFT OUTER JOIN TemplateRatings as r ON r.templateId = t.templateId WHERE t.userId = u.userId AND reviewState = 'a' GROUP BY t.templateId $orderBy $dir LIMIT ? OFFSET ?";
$templates = $db->getTemplates($query, array($ITEMS_PER_PAGE, $startIndex), array(SQLITE3_INTEGER, SQLITE3_INTEGER));
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
		$userRating === false ? '-' : ($userRating === 'y' ? 'p' : 'n')
	));
}

$pageCount = max(ceil($totalItemCount / $ITEMS_PER_PAGE), 1);
$page = min($page, $pageCount);
echo $twig->render('gallery.html', array('items' => $items, 'type' => 't', 'currentPage' => $page, 'pageCount' => $pageCount, 'orderBy' => $orderByReq, 'dir' => $dir));
$db->close();
?>