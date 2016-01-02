<?php
include('php/autoload.php');

if(isset($_GET['p']) && isset($_SESSION['activeImg'])){
	$generator = new ImageGenerator('img/designer/', '');
	$template = $_SESSION['activeImg'];
	$overlay = $_SESSION['activeOverlay'] === '' ? null : $_SESSION['activeOverlay'];
	$pos = $_GET['p'];
	try{
		$img = $generator->generate($pos, $template, $overlay);
		$_SESSION['lastPos'] = $_GET['p'];
	} catch(Exception $e){
		$img = imagecreatefrompng('img/error.png');
	}
} elseif(isset($_GET['t'])){
	$generator = new ImageGenerator('img/designer/', '');
	$template = $db->getTemplates("SELECT Templates.*
						 FROM Templates
						 WHERE templateId = ?",
						 array($_GET['t']),
						 array(SQLITE3_TEXT))[0];
	$img = $generator->generate($template->getPositions(), $template->getImage(), $template->getOverlayFiletype() === null ? null : $template->getOverlayImage());
}else{
	exit();
}

if(isset($_GET['w']) || isset($_GET['h'])){
	$fullWidth = imagesx($img);
	$fullHeight = imagesy($img);
	$w = isset($_GET['w']) ? $_GET['w'] : $fullWidth * ($_GET['h'] / $fullHeight);
	$h = isset($_GET['h']) ? $_GET['h'] : $fullHeight * ($_GET['w'] / $fullWidth);
	$newimg = imagecreatetruecolor($w, $h);
	imagecopyresampled($newimg, $img, 0, 0, 0, 0, $w, $h, $fullWidth, $fullHeight);
	imagedestroy($img);
	$img = $newimg;
}

header('Content-Type: image/jpg');
imagejpeg($img);
imagedestroy($img);