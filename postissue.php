<?php

require_once("generic_html.php");
require_once("lib/session.php");
require_once("lib/permissions.php");

rh_session();
global $mysql, $session;

require_loggedin_or_redirect();

$roomid = (int)$_POST['roomid'];
$itemid = (int)$_POST['itemid'];
$comment = htmlentities($_POST['comment'], ENT_QUOTES); 
$severity = $_POST['severity'];

// sanity checks
$sanerequest = true;

if ($roomid != -1) {
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE id = " . $roomid);
    if (! mysqli_num_rows($res)) $sanerequest = false;
}
if ($itemid != -1) {
    $res = mysqli_query($mysql, "SELECT * FROM items WHERE id = " . $itemid);
    if (! mysqli_num_rows($res)) $sanerequest = false;
}
$severity_acceptable = array("critical", "high", "normal", "low");
if (! in_array($_POST['severity'], $severity_acceptable)) $sanerequest = false;

if ($sanerequest === true) {
    mysqli_query($mysql, "INSERT INTO issues SET time_reported = " . time() . ", reporter_id = " . $session['userid'] . ", comment = '" . mysqli_real_escape_string($mysql, $comment) . "', item_id = " . $itemid . ", room_id = " . $roomid . ", severity = '" . $severity . "', assignee_id = -1, status = 'OPEN', resolution = 'REPORTED'");
    header("Location: index.php");
}
else header("Location: index.php?error=invalid_issue");

?>
