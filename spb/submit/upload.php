<?php

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
	
	$megabyte = 8000000;
	
	$valid = true;
	$id = uniqid();
	$type = pathinfo(basename($_FILES["upload"]["name"]), PATHINFO_EXTENSION);
	$uploadFileDest = "../uploads/$id.$type";
	
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
		move_uploaded_file($_FILES["upload"]["tmp_name"], $uploadFileDest);
	}
	
}
?>