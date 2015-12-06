<?php
$ITEMS_PER_PAGE = 30;
include('php/autoload.php');
require("php/gallery-item.php");
$items = array();
foreach(glob('img/accepted/t6e/*.{jpg,png}', GLOB_BRACE) as $img){
	if(strpos($img, '-overlay') === false){
		$baseimg = urlencode(basename($img));
		array_push($items, new GalleryItem(urlencode($img), "view-t6e.php?i=$baseimg"));
	}
}
$totalItemCount = count($items);
$page = max(isset($_GET['p']) ? $_GET['p'] : 1, 1);
$items = array_slice($items, ($page-1) * $ITEMS_PER_PAGE, $ITEMS_PER_PAGE);
$pageCount = max(ceil($totalItemCount / $ITEMS_PER_PAGE), 1);
$page = min($page, $pageCount);
echo $twig->render('gallery.html', array('items' => $items, 'currentPage' => $page, 'pageCount' => $pageCount));
?>