<?php
include('php/autoload.php');
header('Content-Type: text/plain');
echo 'http://orange.jaxsgaming.com/spb/img/sourceimages/'.$db->scalar('SELECT sourceId || "." || filetype FROM SourceImages WHERE reviewState = "a" ORDER BY random() LIMIT 1');
$db->close();
?>