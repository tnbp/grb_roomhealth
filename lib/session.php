<?php

require_once("config.inc.php");
require_once("lib/rh_errorhandler.php");

if (DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
else {
    ini_set('display_errors', 0);
    error_reporting(E_ERROR | E_WARNING | E_PARSE);
}

function rh_session() {
	session_start();
	global $mysql, $session;
	$session['id'] = session_id();
	$session['loggedin'] = false;
	
	$mysql = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB) or die("Could not connect to database :-(");
	mysqli_set_charset($mysql, MYSQL_CHARSET);
	mysqli_query($mysql, "DELETE FROM sessions WHERE expires < " . time());
	$res = mysqli_query($mysql, "SELECT sessions.*, users.name, users.permissions, users.gender, users.email, classes.room_id AS classroom, classes.name AS classname FROM sessions,users LEFT JOIN classes ON classes.teacher_id = users.id WHERE session_id = '" . mysqli_real_escape_string($mysql, $session['id'])."' AND sessions.user_id = users.id LIMIT 1");
	$rc = mysqli_num_rows($res);
	if ($rc == 1) {
		$row = mysqli_fetch_assoc($res);
		$session['name'] = $row['name'];
		$session['userid'] = $row['user_id'];
		$session['expires'] = time() + SESSION_VALIDITY;
		$session['loggedin'] = true;
		$session['permissions'] = $row['permissions'];
		$session['classroom'] = (($row['classroom'] != NULL) ? $row['classroom'] : false);
		$session['classname'] = (($row['classname'] != NULL) ? $row['classname'] : false);
		$session['gender'] = $row['gender'];
		$session['email'] = $row['email'];
		mysqli_query($mysql, "UPDATE sessions SET expires = " . $session['expires'] . " WHERE session_id = '" . mysqli_real_escape_string($mysql, $session['id']) . "'");
	}
}

function get_session($info = false) {
    global $session;
    if ($info === false) return $session;
    return (isset($session[$info]) ? $session[$info] : false);
}

function redirect($target) {
    if (preg_match("/[?&]error=/", $target)) $target .= "#errorbox";
    header("Location: " . $target);
    global $mysql;
    if ($mysql) mysqli_close($mysql);
    die();
}

/*  script.php?a[]=one&a[]=two&a[]=three is PHP's way of transferring arrays via HTTP GET, but
    "[" and "]" (square brackets) are not valid characters in HTML href parameters.
    This function extracts arrays from the REQUEST_URI string; arrays are possible like:
    script.php?a=one&a=two&a=three
*/
    
function http_get_array($param) {
    if (strpos($_SERVER['REQUEST_URI'], "?") === false) return false;
    $request_params = explode("&", preg_replace("/.*\?[^?]*/U", "", $_SERVER['REQUEST_URI']));
    $c = count($request_params);
    $ret = array();
    if ($c == 1 && $request_params[0] == "") return false;
    for ($i = 0; $i < $c; $i++) {
        if (strpos($request_params[$i], "=") === false) {
            if ($cur[0] == $param) $ret[] = true;
            continue;
        }
        $cur = explode("=", $request_params[$i]);
        if ($cur[0] == $param) $ret[] = $cur[1];
    }
    if (!count($ret)) return false;
    return $ret;
}

if (!function_exists("array_key_last")) {
    function array_key_last($a) {
        if (!is_array($a)) return NULL;
        if (!count($a)) return NULL;
        return key(array_slice($a, -1, 1, true));
    }
}

?>
