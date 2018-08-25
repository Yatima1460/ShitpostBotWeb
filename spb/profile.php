<?php
include('php/autoload.php');
$user = null;
$userExists = false;
$templates = null;
$sourceImages = null;
$admin = null;
	
$userId = isset($_GET['u']) ? $_GET['u'] : (isset($_GET['n']) ? $_GET['n'] : 'NONE');
$checkPhrase = isset($_GET['u']) ? 'u.userId = ?' : (isset($_GET['n']) ? 'u.username = ?' : '');
$userExists = false;
$users = $db->getUsers("SELECT * FROM Users AS u WHERE $checkPhrase", array($userId, $userId), array(SQLITE3_TEXT, SQLITE3_TEXT));

if(count($users) > 0){
	$user = $users[0];
	$userExists = true;
	$templates = $db->getTemplates("SELECT * FROM Templates AS t, Users AS u WHERE ($checkPhrase) AND reviewState = 'a' AND u.userId = t.userId ORDER BY t.timeAdded", array($userId, $userId), array(SQLITE3_TEXT, SQLITE3_TEXT));
	$sourceImages = $db->getSourceImages("SELECT * FROM SourceImages AS s, Users AS u WHERE ($checkPhrase) AND reviewState = 'a' AND u.userId = s.userId ORDER BY s.timeAdded", array($userId, $userId), array(SQLITE3_TEXT, SQLITE3_TEXT));
	$admin = $user->getAdmin();
} else{
	$userExists = false;
}

echo $twig->render('profile.html', array('userExists' => $userExists, 'user' => $user, 'admin' => $admin, 'templates' => $templates, 'sourceImages' => $sourceImages));
$db->close();
?>