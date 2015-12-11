<?php
include('php/autoload.php');
header('Content-Type: application/json');
$fullpath = 'img/uploaded/t6e/'.$_SESSION['activeTemplate'];
$size = getimagesize($fullpath);
echo json_encode(array('x' => $size[0], 'y' => $size[1], 'path' => $fullpath, 'lastPosJson' => isset($_SESSION['lastPos']) ? $_SESSION['lastPos'] : '[]'));
$db->close();
?>