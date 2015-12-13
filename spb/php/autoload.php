<?php
session_start();
require('twig/lib/Twig/Autoloader.php');
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

require('php/database.php');
require('php/template.php');
require('php/source-image.php');
require('php/user.php');
require('php/admin.php');
require('php/generator.php');

$db = new Database();
$twig->addGlobal('db', $db);

$loginId = isset($_SESSION['login-id']) ? $_SESSION['login-id'] : 'NONE';
$isLoggedIn = false;
if($loginId !== 'NONE' && $loginId !== ''){
	$users = $db->getUsers('SELECT * FROM Users WHERE userId = ?', array($loginId), array(SQLITE3_TEXT));
	if(count($users) > 0){
		$me = $users[0];
		$isLoggedIn = true;
		$twig->addGlobal('me', $me);
	}
}
$twig->addGlobal('isLoggedIn', $isLoggedIn);
?>