<?php
include('php/autoload.php');
$templates = $db->getTemplates("SELECT t.*
				   FROM Templates as t 
				   WHERE t.reviewState = 'p'
				   ORDER BY random()
				   LIMIT 1");
if(count($templates) > 0){
	$template = $templates[0];
}
$count = $db->scalar("SELECT count(*) FROM Templates WHERE reviewState = 'p'", array(), array());
echo $twig->render('template-review.html', count($templates) == 0 ? array('msg' => 'No templates found.') : array('template' => $template, 'count' => $count));
$db->close();
?>