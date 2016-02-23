<?php
header('Content-Type: image/jpg');
include('php/autoload.php');
$MINUTE = 60;
$interval = 30 * $MINUTE;

$time = time();
$seededTime = $time - ($time % $interval);
mt_srand($seededTime);
srand($seededTime); //used in the shuffle function for ImageGenerator->generate

//this next bit was from
//http://stackoverflow.com/questions/2171578/seeding-sqlite-random/2469943#2469943
$seed = md5(mt_rand());
$prng = ('0.' . str_replace(array('0', 'a', 'b', 'c', 'd', 'e', 'f'), array('7', '3', '1', '5', '9', '8', '4'), $seed )) * 1;
$templateQuery = "SELECT * FROM Templates WHERE reviewState = 'a' ORDER BY (substr(templateId * $prng, length(templateId) + 2))";
$sourceQuery = "SELECT * FROM SourceImages WHERE reviewState = 'a' ORDER BY (substr(sourceId * $prng, length(sourceId) + 2))";

$templates = $db->getTemplates($templateQuery);
$sources = $db->getSourceImages($sourceQuery);

$sourcePaths = array();
for($i = 0; $i < count($sources); $i++){
	array_push($sourcePaths, $sources[$i]->getImage());
}
$generator = new ImageGenerator('', '');
$template = $templates[mt_rand(0, count($templates) -1)];
$img = $generator->generate($template->getPositions(), $template->getImage(), $template->getOverlayImage(), $sourcePaths);
imagejpeg($img);

$db->close();
?>