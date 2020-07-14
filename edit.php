<?php 
require_once("generic_html.php");
require_once("lib/session.php");
require_once("lib/permissions.php");

require_once("generic_html.php");
require_once("lib/session.php");
require_once("lib/permissions.php");

rh_session();
global $session;
require_loggedin_or_redirect();
$id = $_GET['id'];
$res = mysqli_query($mysql, "SELECT * FROM issues WHERE id = $id");

$issue = mysqli_fetch_assoc($res);
if ($issue !== NULL) {
    $room_id = $issue['room_id'];
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE id = $room_id");
    $room = mysqli_fetch_assoc($res);
    //$res = mysqli_query($mysql, "SELECT * FROM");
    $item_id = $issue['item_id'];
    $res = mysqli_query($mysql, "SELECT * FROM items WHERE id = $item_id");
    $item = mysqli_fetch_assoc($res);
    echo "Problem mit Gerät:  " . $item['name'] . " in Raum: " . $room['name'];
    echo "<hr>";
    echo "<form method=\"post\">";
    echo "<input id=\"has_changed\" value=\"Yes\" type=\"hidden\">";
    if (!$session['userid'] == $issue['user_id']) {
        echo "<textarea name=\"comment\">" . $issue['comment'] . "</textarea><br>";
    }
    else {
        echo "<p> Beschreibung: " . $issue['comment']. "</p>";    
    }
    echo "<select id=\"assignee\">";
    if ($issue['assignee_id' !== -1]) {
        $res = mysqli_query($mysql, "SELECT * FROM users WHERE id = " . $issue['assignee_id']);
        $assignee = mysqli_fetch_assoc($res);
        echo "<option value=\"" . $assignee['id'] . "\">Zugewiesen: " . $assignee['name'] . "</option><br>";
    }
    else {
    echo "<option value=\"-1\">Niemand</option>" ;
    }
    $res = mysqli_query($mysql, "SELECT * FROM users WHERE NOT id = " . $issue['assignee_id']);
    $rows = mysqli_num_rows($res);
    for ($i=0; $i < $rows; $i++) { 
        $row = mysqli_fetch_assoc($res);
        echo "<option value= \"" . $row['id'] . "\">" . $row['name'] . "</option>";
    }
    echo "</select>";
    echo "</form>";
}
else {
    echo "Es wurde kein Problem mit der ID $id gefunden. Das ist ein Problem";
}

?>