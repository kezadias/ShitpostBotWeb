<?php
require '../dataaccess/loader.php';

function imagexy($img) {
	return array(imagesx($img), imagesy($img));
}

function getending($file) {
	return strtolower(array_slice(explode('.', $file), -1)[0]);
}

function openimage($file) {
	if (getending($file) == 'png') {
		return imagecreatefrompng($file);
	}
	return imagecreatefromjpeg($file);
}

function generate($db, $tempath, $imgpath, $imgid, $pos) {
	$data = json_decode($pos);
	$imgcount = count($data);

	$query = 'SELECT image FROM template_images WHERE ROWID = %u';
	list($file) = $db->queryOne(sprintf($query, $imgid));

	$query = 'SELECT image FROM source_images ORDER BY RANDOM() LIMIT %u';
	$imgs = $db->queryFirstEach(sprintf($query, $imgcount));

	$img = openimage($tempath.$file);
	list($sx, $sy) = imagexy($img);

	for ($i = 0; $i < $imgcount; $i++) {
		$imgpos = $data[$i];
		$im2 = openimage($imgpath.$imgs[$i]);
		$poscount = count($imgpos);
		list($sx2,$sy2) = imagexy($im2);
		for ($p = 0; $p < $poscount; $p++) {
			list($x1, $y1, $x2, $y2) = getBestFit($imgpos[$p], $sx2, $sy2, $sx, $sy);
			imagecopyresized($img, $im2, $x1, $y1, 0, 0, $x2-$x1, $y2-$y1, $sx2, $sy2);
		}
		imagedestroy($im2);
	}
	return $img;
}

function generateWithTemplate($db, $tempath, $imgpath, $imgid){
	$query = "SELECT positions FROM templates WHERE $imgid = image_id";
	list($pos) = $db->queryOne($query);
	return generate($db, $tempath, $imgpath, $imgid, $pos);
}

function generateRand($db, $tempath, $imgpath){
	$query = 'SELECT image_id, positions FROM templates ORDER BY RANDOM() LIMIT 1';
	list($imgid, $pos) = $db->queryOne($query);
	return generate($db, $tempath, $imgpath, $imgid, $pos);
}

//args: x1, y1, x2, y2, image width, image height, total width, total height
//image is the source image, total is the template
function getBestFit($pos, $iw, $ih, $tw, $th){
	$x = $pos[0] * $tw;
	$y = $pos[1] * $th;
	$w = $pos[2] * $tw - $x;
	$h = $pos[3] * $th - $y;
	
	// Calculate resize ratios for resizing 
	$ratioW = $w / $iw; 
	$ratioH = $h / $ih;
	
	// smaller ratio will ensure that the image fits in the view
	$ratio = min($ratioW, $ratioH);

	$nw = $iw * $ratio;
	$nh = $ih * $ratio;
	
	if($w > $h){
		$x += ($w - $nw) / 2;
	} else{
		$y += ($h - $nh) / 2;
	}
	
	return array(round($x), round($y), round($x + $nw), round($y + $nh));
}
