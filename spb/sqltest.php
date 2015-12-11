<?php
include('php/autoload.php');
$db->login('zeroth', 'memes');
$templates = $db->getRandomSourceImages(5);
echo $twig->render('sqltest.html', array('messages' => $templates));
$db->close();
?>