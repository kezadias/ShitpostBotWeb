<?php
include('php/autoload.php');
$userId = isset($_GET['u']) ? $_GET['u'] : 'NONE';
$userExists = $userId == 'NONE' ? false : $db->queryHasRows('SELECT * FROM Users WHERE userId = ?', array($userId), array(SQLITE3_TEXT));
$user = null;
if($userExists){
	$user = new UserInfo($db, $userId);
	$username = $user->getUsername();
}
echo $twig->render('profile.html', array('userExists' => $userExists, 'user' => $user));
$db->close();
?>