<?php
session_start();
require('twig/lib/Twig/Autoloader.php');
Twig_Autoloader::register();
$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

require('php/database.php');
require('php/template.php');
require('php/source-image.php');
require('php/userinfo.php');
require('php/generator.php');

$db = new Database();

$meId = isset($_SESSION['login-id']) ? $_SESSION['login-id'] : 'NONE';
$isLoggedIn = $meId == 'NONE' ? false : $db->queryHasRows('SELECT * FROM Users WHERE userId = ?', array($meId), array(SQLITE3_TEXT));
if($isLoggedIn){
	$me = new UserInfo($db, $meId);
	$twig->addGlobal('me', $me);
}
$twig->addGlobal('db', $db);
$twig->addGlobal('isLoggedIn', $isLoggedIn);
?>