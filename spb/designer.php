<?php
include('php/autoload.php');
echo $twig->render('designer.html', array());
$db->close();
?>