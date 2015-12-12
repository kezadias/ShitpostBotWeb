<?php
include('php/autoload.php');
echo $twig->render('login-signup.html', array('actionpage' => 'do-signup.php', 'title' => 'Sign Up'));
$db->close();
?>