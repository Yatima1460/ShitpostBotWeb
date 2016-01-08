<?php
include('php/autoload.php');
echo $twig->render('success.html', array('id' => $_SESSION['lastId']));
$db->close();
?>