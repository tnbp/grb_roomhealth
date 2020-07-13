<?php 
require_once("lib/session.php");
rh_session();
$id = $_GET['id'];
$res = mysqli_query($mysql, "SELECT * FROM issues WHERE id = $id");
$row = mysqli_fetch_assoc($res);
$date = $row['time_reported'];
$reporterId = $row['reporter_id'];
$comment = $row['comment'];
$itemid = $row['item_id'];
$room = ($row['room']);
$severity = $row['severity'];
$assignee_id = $row['assignee_id'];
$status = $row['status'];
$resoulution = $row['resolution'];
echo '<div class="box">';
echo '<div class="box-title">';
echo '</div>';
echo '<p>';
echo "Error reported in Room: $room <br>";
echo "Error reported on $date";
echo '</p> </div>';

?>
