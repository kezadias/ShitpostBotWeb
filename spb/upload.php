<?php
session_start();

$megabyte = 8000000;

function isValidFileType($type){
	$type = strtolower($type);
	$types = array('jpeg', 'jpg', 'png');
	for($i = 0; $i < count($types); $i++){
		if($type === $types[$i]){
			return true;
		}
	}
	return false;
}

if(isset($_POST["submit"])) {
	
	
	$valid = true;
	$id = uniqid();
	$type = pathinfo(basename($_FILES["upload"]["name"]), PATHINFO_EXTENSION);
	$uploadFileDest = "img/pending/t6e/$id.$type";
	
    if(getimagesize($_FILES["upload"]["tmp_name"]) === false) {
        echo "Not an image";
        $valid = false;
    } elseif(!isValidFileType($type)){
		echo "Not a valid filetype";
        $valid = false;
	} elseif ($_FILES["upload"]["size"] > 10 * $megabyte) {
		echo "File larger than 10 MB";
		$valid = false;
	}
	
	if($valid){
		$_SESSION['activeTemplate'] = "$id.$type";
		$_SESSION['activeCode'] = $id;
		move_uploaded_file($_FILES["upload"]["tmp_name"], $uploadFileDest);
		header('Location: designer.php');
	}
	
	$valid = true;
	$type = pathinfo(basename($_FILES["overlay"]["name"]), PATHINFO_EXTENSION);
	$uploadFileDest = "../uploads/$id.$type";
	
	if($_FILES["overlay"]["tmp_name"] == ''){
		$valid = false;
	}elseif(getimagesize($_FILES["overlay"]["tmp_name"]) === false) {
        echo "Overlay not an image";
        $valid = false;
    } elseif(!isValidFileType($type)){
		echo "Overlay a valid filetype";
        $valid = false;
	} elseif ($_FILES["upload"]["size"] > 10 * $megabyte) {
		echo "Overlay larger than 10 MB";
		$valid = false;
	}
	
	if($valid){
		move_uploaded_file($_FILES["overlay"]["tmp_name"], $uploadFileDest);
	}
	
}
?>