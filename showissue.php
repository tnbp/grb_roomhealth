<?php 
require_once("lib/session.php");
require_once("generic_html.php");
rh_session();
generic_header();
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
    echo "<p> Beschreibung: " . $issue['comment']. "</p>";
    if ($issue['assignee_id' !== -1]) {
        $res = mysqli_query($mysql, "SELECT * FROM users WHERE id = " . $issue['assignee_id']);
        $assignee = mysqli_fetch_assoc($res);
        echo "Zugewiesen: " . $assignee['name'] . "<br>";
    }
    else {
    echo "Es wurde noch niemand zugewiesen <br>";      
    }
    //echo $issue[]
}
else {
    echo "Es wurde kein Problem mit der ID $id gefunden. Das ist ein Problem";
}
function Box($title, $body) { 
echo '<div class="box"><div class=box-"title">';
echo $title;
echo '</div><div class="box-content">';
echo $body;
echo '</div></div>';
};
?>
