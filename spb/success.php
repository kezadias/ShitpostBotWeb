<?php
include('php/autoload.php');
echo $twig->render('success.html', array());
$db->close();
?>