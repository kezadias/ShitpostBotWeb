<?php
include('php/autoload.php');
$error = 'notset';
if(isset($_GET['e'])){
	$error = urldecode($_GET['e']);
}
echo $twig->render('submit.html', array('title' => 'Submission', 'error' => $error));
?>