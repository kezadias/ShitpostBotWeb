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

function isPng($filepath){
	try{
		$img = imagepng($filepath);
	} catch(Exception $e){
		return false;
	}
	imagedestroy($img);
	return true;
}

function isJpeg($filepath){
	try{
		$img = imagejpeg($filepath);
	} catch(Exception $e){
		return false;
	}
	imagedestroy($img);
	return true;
}

$selectedType = $_POST['type'];
$dir = $selectedType == 'template' ? 'uploaded/t6e' : 'pending/src';
if(isset($_POST["submit"])) {
	
	$valid = true;
	$id = uniqid();
	$type = strtolower(pathinfo(basename($_FILES["upload"]["name"]), PATHINFO_EXTENSION));
	$uploadFileDest = "img/$dir/$id.$type";
	$size = @getimagesize($_FILES["upload"]["tmp_name"]);
	$error = '';
	
    if($size === false) {
        $error .= "Main image not a valid image, ";
        $valid = false;
    } elseif($size[0] > 2000 || $size[1] > 2000){
		$error .= "Image larger than 2000px, ";
		$valid = false;
	} elseif(!isValidFileType($type)){
		$error .= "Not a valid filetype, ";
        $valid = false;
	} elseif(!isJpeg() && !isPng()){
		$error .= "Main image corrupted/not a valid jpg/png, ";
		$valid = false;
	} elseif ($_FILES["upload"]["size"] > 10 * $megabyte) {
		$error .= "File larger than 10 MB, ";
		$valid = false;
	}
	
	if($selectedType == 'template'){
	
		$oType = strtolower(pathinfo(basename($_FILES["overlay"]["name"]), PATHINFO_EXTENSION));
		$overlayFileDest = "img/uploaded/t6e/$id-overlay.$oType";
		$size = @getimagesize($_FILES["overlay"]["tmp_name"]);
		
		if($_FILES["overlay"]["tmp_name"] != ''){
			if($size === false) {
				$error .= "Overlay not an image, ";
				$valid = false;
			} elseif($size[0] > 2000 || $size[1] > 2000){
				$error .= "Overlay larger than 2000px, ";
				$valid = false;
			} elseif($oType != 'png'){
				$error .= "Overlay not a valid filetype, ";
				$valid = false;
			} elseif(!isPng()){
				$error .= "Overlay corrupted/not a valid png, ";
				$valid = false;
			} elseif ($_FILES["upload"]["size"] > 10 * $megabyte) {
				$error .= "Overlay larger than 10 MB, ";
				$valid = false;
			}
		}
	
	}
	
	if($valid){
		$_SESSION['activeTemplate'] = "$id.$type";
		$_SESSION['activeCode'] = $id;
		move_uploaded_file($_FILES["upload"]["tmp_name"], $uploadFileDest);
		if($selectedType == 'template'){
			move_uploaded_file($_FILES["overlay"]["tmp_name"], $overlayFileDest);
			header('Location: designer.php');
		} else{
			header('Location: success.php');
		}
	} else{
		header('Location: submit.php?e='.urlencode(substr($error, 0, strlen($error) - 2)));
	}
	
}
?>