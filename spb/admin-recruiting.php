<?php
include('php/autoload.php');

$admins = $db->getUsers('SELECT u.* FROM Users as u, Admins WHERE u.userId = Admins.userId', array(), array());

echo $twig->render('admin-recruiting.html', array('admins' => $admins));
$db->close();
?>