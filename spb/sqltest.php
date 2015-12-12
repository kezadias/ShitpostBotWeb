<?php
include('php/autoload.php');
//$messages = $db->getRandomAcceptedSourceImages(5);
$messages = array();
$messages[] = $db->acceptSourceImage('566a31310e499');
$messages[] = $db->acceptSourceImage('566a31310715b');
$messages[] = $db->acceptSourceImage('566a3131098ab');
$messages[] = $db->acceptSourceImage('566a313108829');
echo $twig->render('sqltest.html', array('messages' => $messages));
$db->close();
?>