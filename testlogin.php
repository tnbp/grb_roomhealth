<?php

require_once("generic_html.php");
require_once("lib/session.php");
rh_session();
generic_header();
echo "\t<body>\n";

global $mysql, $session;
echo "\t\t<p>Current PHPSESSID: " . $session['id']. "</p>\n";

if ($_GET['action'] == "login") {
	echo"\t\t<p>Received login: " . htmlentities($_POST['login'], ENT_QUOTES) . "</p>
\t\t<p>Received password: " . htmlentities($_POST['pwd'], ENT_QUOTES) . "</p>\n";
	$bcrypt_pwd = password_hash($_POST['pwd'], PASSWORD_BCRYPT);
	echo "\t\t<p>bcrypt password hash: " . htmlentities($bcrypt_pwd, ENT_QUOTES) . "</p>\n";

	$res = mysqli_query($mysql, "SELECT * FROM users WHERE login = '" . mysqli_real_escape_string($mysql, $_POST['login']) . "'");
	$rc = mysqli_num_rows($res);
	
	if (verify_login($_POST['login'], $_POST['pwd']) === true) {
		echo "\t\t<p style=\"color: green; font-weight: bold;\">Login erfolgreich! Eingeloggt als " . $session['name'] . "</p>";
	}
	else {
		echo "\t\t<p style=\"color: red; font-weight: bold;\">Login fehlgeschlagen!</p>";
	}
}
else {
	$res = mysqli_query($mysql, "SELECT sessions.*,users.name FROM sessions,users WHERE session_id = '" . mysqli_real_escape_string($mysql, $session['id'])."' AND sessions.user_id = users.id");
	$rc = mysqli_num_rows($res);

	if ($rc === 0) {
		echo "\t\t<p>Currently <b>not</b> logged in!</p>\n";
		echo "\t\t<div>
\t\t\t<form action=\"login.php?action=login\" method=\"POST\">
\t\t\t\t<p>Kürzel: <input type=\"text\" name=\"login\"></p>
\t\t\t\t<p>Passwort: <input type=\"password\" name=\"pwd\"> <input type=\"submit\" value=\"Login\"></p>
\t\t\t</form>
\t\t</div>\n";
	}
	else if ($rc > 1) {
		echo "\t\t<p style=\"color: red; font-weight: bold;\">Duplicate sessions detected! Deleting all...</p>\n";
		mysqli_query($mysql, "DELETE FROM sessions WHERE session_id = '" . mysqli_real_escape_string($mysql, $session['id'])."'");
	}
	else {
		$row = mysqli_fetch_assoc($res);
		$session['name'] = $row['name'];
		$session['userid'] = $row['user_id'];
		$session['expires'] = $row['expires'];
		echo "\t\t<p style=\"font-weight: bold;\">Eingeloggt als: <span style=\"font-weight: normal;\">" . htmlentities($session['name'], ENT_QUOTES) . "</span>
\t\t</p>";
	}
}

echo "\t</body>
</html>";

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
