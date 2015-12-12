<?php
include('php/autoload.php');
echo $twig->render('login-signup.html', array('actionpage' => 'do-login.php', 'title' => 'Login'));
$db->close();
?>