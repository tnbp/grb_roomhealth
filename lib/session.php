<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

function rh_session() {
	session_start();
	global $mysql, $session;
	$session['id'] = session_id();
	$session['loggedin'] = false;
	
	$mysql = mysqli_connect("localhost", "root", "", "roomhealth") or die(">:(");
	mysqli_query($mysql, "DELETE FROM sessions WHERE expires < " . time());
	$res = mysqli_query($mysql, "SELECT sessions.*, users.name, users.permissions FROM sessions,users WHERE session_id = '" . mysqli_real_escape_string($mysql, $session['id'])."' AND sessions.user_id = users.id");
	$rc = mysqli_num_rows($res);
	if ($rc == 1) {
		$row = mysqli_fetch_assoc($res);
		$session['name'] = $row['name'];
		$session['userid'] = $row['user_id'];
		$session['expires'] = time()+60*60;
		$session['loggedin'] = true;
		$session['permissions'] = $row['permissions'];
		mysqli_query($mysql, "UPDATE sessions SET expires = " . $session['expires'] . " WHERE session_id = '" . mysqli_real_escape_string($mysql, $session['id']) . "'");
	}
}

function redirect($target) {
    header("Location: " . $target);
    die();
}

?>
