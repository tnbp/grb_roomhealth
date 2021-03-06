<?php

require_once("lib/session.php");
rh_session();

global $mysql, $session;

$res = mysqli_query($mysql, "SELECT * FROM users WHERE LOWER(login) = LOWER('" . mysqli_real_escape_string($mysql, $_POST['login']) . "') AND active = 1");
$rc = mysqli_num_rows($res);

$nexturi = urldecode($_POST['nexturi']);
$nexturi = preg_replace("/([?&])error=[^&]*(&|\$)/", "\$1", $nexturi);

if (verify_login($_POST['login'], $_POST['pwd']) === true) {
    redirect($nexturi);
}
else {
    if (strpos($nexturi, "?") === false) $nexturi .= "?error=login";
    else $nexturi .= "&error=login";
    redirect($nexturi);
}

function verify_login($login, $password) {
    global $mysql, $session;
    $res = mysqli_query($mysql, "SELECT * FROM users WHERE LOWER(login) = LOWER('" . mysqli_real_escape_string($mysql, $login). "') AND active = 1");
    $rc = mysqli_num_rows($res);
    if ($rc != 1) return false;
    $row = mysqli_fetch_assoc($res);
    if (password_verify($password, $row['pwhash']) === true) {
        $already_loggedin = mysqli_query($mysql, "SELECT * FROM sessions WHERE user_id = " . $row['id']);
        if (mysqli_num_rows($already_loggedin)) mysqli_query($mysql, "UPDATE sessions SET session_id = '" . mysqli_real_escape_string($mysql, $session['id']) . "', expires = " . (int)(time()+(60*60)) . " WHERE user_id = " . $row['id']);
        else mysqli_query($mysql, "INSERT INTO sessions SET user_id = " . (int)$row['id'] . ", session_id = '" . mysqli_real_escape_string($mysql, $session['id']) . "', expires = " . (int)(time()+(60*60)));
        $session['name'] = $row['name'];
        return true;
    }
    return false;
}

?>
