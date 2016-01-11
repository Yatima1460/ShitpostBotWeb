<?php
require('php/autoload.php');

if(!$isLoggedIn){
	die('Not logged in');
}

if(is_null($me->getAdmin())){
	die('Insufficient Permissions');
}

if(!$me->getAdmin()->canMakeAdmin()){
	die('Insufficient Permissions');
}

$count = isset($_GET['count']) ? $_GET['count'] : 10;
echo "Generating ".$_GET['count']." memes<br>";
$startingTime = time();

$tempDbLocation = str_replace('.', '-temp.', $db->getLocation());
@unlink($tempDb);
copy($db->getLocation(), $tempDbLocation);
$tempDb = new Database($tempDbLocation);

$templates = $tempDb->getTemplates(isset($_GET['templateQuery']) ? urldecode($_GET['templateQuery']) : "SELECT * FROM Templates");
$sources = $tempDb->getSourceImages(isset($_GET['sourceQuery']) ? urldecode($_GET['sourceQuery']) : "SELECT * FROM SourceImages");

$sourcePaths = array();
for($i = 0; $i < count($sources); $i++){
	array_push($sourcePaths, $sources[$i]->getImage());
}

$generator = new ImageGenerator('', '');
for($i = 0; $i < $count; $i++){
	$t = $templates[mt_rand(0, count($templates) -1)];
	$img = $generator->generate($t->getPositions(), $t->getImage(), $t->getOverlayImage(), $sourcePaths);
	@unlink("img/output/Meme_$i.jpg");
	imagejpeg($img, "img/output/Meme_$i.jpg");
}

$db->close();
$tempDb->close();
@unlink($tempDbLocation);
echo "Done.<br>";
echo "Took ".(time() - $startingTime).' seconds.';
?>