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

function rh_loginform($nexturi = false) {
	if ($nexturi === false) $nexturi = $_SERVER['REQUEST_URI'];
	global $session, $mysql;
	if ($session['loggedin'] === true) {
		echo "<div>Eingeloggt als ";
		echo "<span style=\"font-weight: bold;\">" . htmlentities($session['name'], ENT_QUOTES) . "</span>";
		echo " (<a href=\"logout.php?next=" . urlencode($nexturi) . "\">Ausloggen</a>)</div>";
	}
	else {
		if ($_GET['error'] == "login") echo "<p style=\"color: red; font-weight: bold;\">Login fehlgeschlagen!</p>";
		echo "<form action=\"login.php\" method=\"POST\">";
		echo "<p>Login: <input type=\"text\" name=\"login\"> Passwort: <input type=\"password\" name=\"pwd\"> <input type=\"submit\" value=\"Login\"><input type=\"hidden\" name=\"nexturi\" value=\"" . urlencode($nexturi) . "\"></p></form>";
	}
}

?>
