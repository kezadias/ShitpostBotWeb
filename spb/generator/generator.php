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

function generate($db, $tempath, $imgpath) {
	$query = 'SELECT image_id, positions FROM templates ORDER BY RANDOM() LIMIT 1';
	list($imgid, $pos) = $db->queryOne($query);
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
			list($x1, $y1, $x2, $y2) = $imgpos[$p];
			list($xo, $yo) = array($x1*$sx, $y1*$sy);
			imagecopyresized($img, $im2, $xo, $yo, 0, 0, $x2*$sx-$xo, $y2*$sy-$yo, $sx2, $sy2);
		}
		imagedestroy($im2);
	}
	return $img;
}
