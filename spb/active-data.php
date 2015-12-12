<?php
include('php/autoload.php');
header('Content-Type: application/json');
$fullpath = $_SESSION['activeImg'];
$size = getimagesize($fullpath);
echo json_encode(array('x' => $size[0], 'y' => $size[1], 'path' => $fullpath, 'lastPosJson' => isset($_SESSION['lastPos']) ? $_SESSION['lastPos'] : '[]'));
$db->close();
?>