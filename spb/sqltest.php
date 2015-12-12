<?php
include('php/autoload.php');
$messages = array();
echo $twig->render('sqltest.html', array('messages' => $messages));
$db->close();
?>