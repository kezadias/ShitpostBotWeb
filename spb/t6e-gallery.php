<?php
$ITEMS_PER_PAGE = 30;
include('php/autoload.php');
require("php/gallery-item.php");

$totalItemCount = $db->scalar('SELECT count(templateId) FROM Templates', array(), array());
$page = max(isset($_GET['p']) ? $_GET['p'] : 1, 1);

$startIndex = ($page-1) * $ITEMS_PER_PAGE;
$templates = $db->getTemplates('SELECT * FROM Templates LIMIT ? OFFSET ?', array($ITEMS_PER_PAGE, $startIndex), array(SQLITE3_INTEGER, SQLITE3_INTEGER));
$items = array();
foreach($templates as $template){
	array_push($items, new GalleryItem($template->getImage(), "view-t6e.php?id=".$template->getTemplateId(), $template->getRating()));
}

$pageCount = max(ceil($totalItemCount / $ITEMS_PER_PAGE), 1);
$page = min($page, $pageCount);
echo $twig->render('gallery.html', array('items' => $items, 'currentPage' => $page, 'pageCount' => $pageCount));
$db->close();
?>