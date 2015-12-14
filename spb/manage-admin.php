<?php
include('php/autoload.php');

function kill(){
	$GLOBALS['db']->close();
	die();
}

if(!$isLoggedIn){
	kill(';failed-not-logged-in');
}

if(is_null($me->getAdmin()) || !$me->getAdmin()->canMakeAdmin()){
	kill(';failed-insufficient-permissions');
}

$action = $_GET['action'];

if($action == 'delete'){
	$id = $_GET['id'];
	kill($db->query('DELETE FROM Admins WHERE userId = ?', array($id), array(SQLITE3_TEXT)));
} elseif($action == 'recruit'){
	$username = $_GET['username'];
	$id = $db->scalar('SELECT userId FROM Users WHERE username = ?', array($username), array(SQLITE3_TEXT));
	if($id === false){
		kill(';failed-user-doesnt-exist');
	} else{
		$canReview = $_GET['canReview'] == 'y';
		$canMakeAdmin = $_GET['canMakeAdmin'] == 'y';
		kill($db->addAdmin($id, $canReview, $canMakeAdmin));
	}
}

?>