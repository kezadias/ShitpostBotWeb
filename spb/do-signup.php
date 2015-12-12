<?php
include('php/autoload.php');
echo $db->addUser($_POST['spb-user'], $_POST['spb-pass']);
$db->close();
?>