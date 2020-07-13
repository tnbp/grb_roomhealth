<?php 
require_once("lib/session.php");
rh_session();
$id = $_GET['id'];

$res = mysqli_query($mysql, "SELECT * FROM issues WHERE id = $id");
$issue = mysqli_fetch_assoc($res);
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

function Box($title, $body) { 
echo '<div class="box"><div class=box-"title">';
echo $title;
echo '</div><div class="box-content">';
echo $body;
echo '</div></div>';
};
?>
