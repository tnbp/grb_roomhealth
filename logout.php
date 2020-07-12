<?php

require_once("lib/session.php");
rh_session();

global $mysql, $session;

$res = mysqli_query($mysql, "DELETE FROM sessions WHERE session_id = '" . $session['id'] . "'");
header("Location: " . urldecode($_GET['next']));

?>