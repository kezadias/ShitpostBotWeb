<?php
session_start();
$template = $_SESSION['activeTemplate'];
$t6eCode = pathinfo($template, PATHINFO_FILENAME);
file_put_contents("img/pending/t6e/$t6eCode.json", $_SESSION['lastPos']);
rename("img/uploaded/t6e/$template", "img/pending/t6e/$template");
$code = pathinfo($template, PATHINFO_FILENAME);
$overlayPath = "img/uploaded/t6e/$code-overlay.png";
if(file_exists($overlayPath)){
	rename($overlayPath, "img/pending/t6e/$code-overlay.png");
}
session_unset();
session_destroy();
header('Location: success.php');
?>