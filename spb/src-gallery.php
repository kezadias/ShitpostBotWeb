<?php
$ITEMS_PER_PAGE = 30;
include('php/autoload.php');
require("php/gallery-item.php");
$items = array();
foreach(glob('img/accepted/src/*.{jpg,png}', GLOB_BRACE) as $img){
	$baseimg = urlencode(basename($img));
	array_push($items, new GalleryItem(urlencode($img), "view-srcimg.php?i=$baseimg"));
}
$totalItemCount = count($items);
$page = max(isset($_GET['p']) ? $_GET['p'] : 1, 1);
$items = array_slice($items, ($page-1) * $ITEMS_PER_PAGE, $ITEMS_PER_PAGE);
$pageCount = max(ceil($totalItemCount / $ITEMS_PER_PAGE), 1);
$page = min($page, $pageCount);
echo $twig->render('gallery.html', array('items' => $items, 'currentPage' => $page, 'pageCount' => $pageCount));
$db->close();
?>