<?php

require_once("lib/session.php");
rh_session();

global $mysql, $session;

$res = mysqli_query($mysql, "DELETE FROM sessions WHERE session_id = '" . $session['id'] . "'");
if (!isset($_GET['next'])) $next = "index.php";
else $next = urldecode($_GET['next']);

redirect($next);

?>
