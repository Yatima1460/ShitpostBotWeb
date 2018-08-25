<?php
require('php/autoload.php');
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
	$img = @imagecreatefrompng($filepath);
	if($img === false){
		return false;
	} else{
		imagedestroy($img);
		return true;
	}
}

function isJpeg($filepath){
	$img = @imagecreatefromjpeg($filepath);
	if($img === false){
		return false;
	} else{
		imagedestroy($img);
		return true;
	}
}

$selectedType = $_POST['type'];
$dir = $selectedType == 'template' ? 'temp' : 'sourceimages';
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
	} elseif(!isJpeg($_FILES["upload"]["tmp_name"]) && !isPng($_FILES["upload"]["tmp_name"])){
		$error .= "Main image corrupted/not a valid jpg/png, ";
		$valid = false;
	} elseif ($_FILES["upload"]["size"] > 10 * $megabyte) {
		$error .= "File larger than 10 MB, ";
		$valid = false;
	}
	
	if($selectedType == 'template'){
	
		$oType = strtolower(pathinfo(basename($_FILES["overlay"]["name"]), PATHINFO_EXTENSION));
		$overlayFileDest = "img/temp/$id-overlay.$oType";
		$size = @getimagesize($_FILES["overlay"]["tmp_name"]);
		$hasOverlay = true;
		
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
			} elseif(!isPng($_FILES["overlay"]["tmp_name"])){
				$error .= "Overlay corrupted/not a valid png, ";
				$valid = false;
			} elseif ($_FILES["upload"]["size"] > 10 * $megabyte) {
				$error .= "Overlay larger than 10 MB, ";
				$valid = false;
			}
		} else{
			$hasOverlay = false;
		}
	
	}
	
	if($valid){
		if($selectedType == 'template'){
			$_SESSION['activeId'] = $id;
			$_SESSION['activeImg'] = $uploadFileDest;
			$_SESSION['activeOverlay'] = $hasOverlay ? $overlayFileDest : '';
			move_uploaded_file($_FILES["upload"]["tmp_name"], $uploadFileDest);
			move_uploaded_file($_FILES["overlay"]["tmp_name"], $overlayFileDest);
			header('Location: designer.php');
		} else{
			$response = $db->addSourceImage($id, $type);
			if($response == ';success'){
				move_uploaded_file($_FILES["upload"]["tmp_name"], $uploadFileDest);
				$_SESSION['lastId'] = $id; //for the success message
				header('Location: success.php');
			} else{
				header('Location: submit.php?e='.urlencode("Database failed with message: $response"));
			}
		}
	} else{
		header('Location: submit.php?e='.urlencode(substr($error, 0, strlen($error) - 2)));
	}
	
}
?>