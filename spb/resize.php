<?php
ini_set('memory_limit','512M');

function createthumb($name, $target_w, $target_h, $transparency=true, $base64=false) {
    $arr = explode(".",$name);
    $ext = $arr[count($arr)-1];

    if($ext=="jpeg" || $ext=="jpg"){
        $img = @imagecreatefromjpeg($name);
    } elseif($ext=="png"){
        $img = @imagecreatefrompng($name);
    } elseif($ext=="gif") {
        $img = @imagecreatefromgif($name);
    } else{
        $img = imagecreatefromjpeg($name);
	}
	
    $oldw = imageSX($img);
    $oldh = imageSY($img);
    
	$neww;
	$newh;
	$x;
	$y;
	if($oldw > $oldh){
		$scale = $oldh/$oldw;
		$neww = $target_w;
		$newh = $neww * $scale;
		$x = 0;
		$y = ($target_h - $newh) / 2;
	} else{
		$scale = $oldw/$oldh;
		$newh = $target_h;
		$neww = $newh * $scale;
		$x = ($target_w - $neww) / 2;
		$y = 0;
	}
    $new_img = imagecreatetruecolor($target_w, $target_h);
    
    imagefill($new_img, 0, 0, imagecolorallocate($new_img, 255, 255, 255));
    
    imagecopyresampled($new_img, $img, $x,$y,0,0, $neww, $newh, $oldw, $oldh); 
    if($base64) {
        ob_start();
        imagepng($new_img);
        $img = ob_get_contents();
        ob_end_clean();
        $return = base64_encode($img);
    } else {
        if($ext=="jpeg" || $ext=="jpg"){
			header('Content-Type: image/jpeg');
            imagejpeg($new_img);
            $return = true;
        } elseif($ext=="png"){
			header('Content-Type: image/png');
            imagepng($new_img);
            $return = true;
        }
    }
    imagedestroy($new_img); 
    imagedestroy($img); 
    return $return;
}

if(!isset($_GET['i']) || (!isset($_GET['w']) && !isset($_GET['h']))){
	die();
}

if(!file_exists($_GET['i'])){
	die();
}

$file = urldecode($_GET['i']);
list($fullWidth, $fullHeight) = getimagesize($file);
$w = isset($_GET['w']) ? $_GET['w'] : $fullWidth * ($_GET['h'] / $fullHeight);
$h = isset($_GET['h']) ? $_GET['h'] : $fullHeight * ($_GET['w'] / $fullWidth);
createthumb($file, $w, $h);
?>