<?php

require_once("lib/session.php");
rh_session();

global $mysql, $session;

$res = mysqli_query($mysql, "SELECT * FROM users WHERE login = '" . mysqli_real_escape_string($mysql, $_POST['login']) . "'");
$rc = mysqli_num_rows($res);
	
if (verify_login($_POST['login'], $_POST['pwd']) === true) {
	header("Location: " . urldecode($_POST['nexturi']));
	die();
}
else {
	$nexturi = urldecode($_POST['nexturi']);
	$nexturi = preg_replace("/[?&]error=[^&]*/", "", $nexturi);
	if (strpos($nexturi, "?") === false) $nexturi .= "?error=login";
	else $nexturi .= "&error=login";
	header("Location: " . $nexturi);
	die();
}
		
function verify_login($login, $password) {
	global $mysql, $session;
	$res = mysqli_query($mysql, "SELECT * FROM users WHERE login = '" . mysqli_real_escape_string($mysql, $login). "'");
	$rc = mysqli_num_rows($res);
	if ($rc != 1) return false;
	$row = mysqli_fetch_assoc($res);
	if (password_verify($password, $row['pwhash']) === true) {
		mysqli_query($mysql, "INSERT INTO sessions SET user_id = " . (int)$row['id'] . ", session_id = '" . mysqli_real_escape_string($mysql, $session['id']) . "', expires = " . (int)(time()+(60*60)));
		$session['name'] = $row['name'];
		return true;
	}
	return false;
}

?>