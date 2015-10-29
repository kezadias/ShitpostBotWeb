<?php

function getArrayFromFile($file){
	return json_decode(file_get_contents($file));
}

function getCategories(){
	return explode(PHP_EOL, file_get_contents($_SERVER['DOCUMENT_ROOT'].'/spb/data/categories.txt'));
}

?>