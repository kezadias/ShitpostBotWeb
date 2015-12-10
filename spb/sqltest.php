<?php
include('php/autoload.php');
$db = new Database();
$loginmsg = $db->login('zeroth', 'memes');

echo $twig->render('sqltest.html', array('loginmsg' => $loginmsg));
?>