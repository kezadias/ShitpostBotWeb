<?php
include('php/autoload.php');

echo $twig->render('sqltest.html', array('messages' => $messages));
$db->close();
?>