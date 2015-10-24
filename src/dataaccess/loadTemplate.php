<?php

function getArrayFromFile($file){
	return json_decode(file_get_contents($file));
}

?>