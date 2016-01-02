<?php
include('php/autoload.php');
$template = $db->getTemplates("SELECT t.*
				   FROM Templates as t 
				   WHERE t.reviewState = 'p'
				   ORDER BY random()
				   LIMIT 1")[0];

$count = $db->scalar("SELECT count(*) FROM Templates WHERE reviewState = 'p'", array(), array());
echo $twig->render('template-review.html', $row === false ? array('msg' => 'No templates found.') : array('template' => $template, 'count' => $count));
$db->close();
?>