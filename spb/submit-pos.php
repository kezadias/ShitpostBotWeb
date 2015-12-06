<?php
session_start();
$template = $_SESSION['activeTemplate'];
$t6eCode = pathinfo($template, PATHINFO_FILENAME);
file_put_contents("img/pending/t6e/$t6eCode.json", $_SESSION['lastPos']);
rename("img/uploaded/t6e/$template", "img/pending/t6e/$template");
session_unset();
session_destroy();
header('Location: success.php');
?>