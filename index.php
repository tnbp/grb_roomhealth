<?php

require_once("generic_html.php");
require_once("lib/session.php");
rh_session();

global $mysql, $session;

generic_header();
echo "\t<body>\n";
echo "\t\t<h1>GRB Raumstatus</h1>\n";

rh_loginform();

if ($session['loggedin'] === true) {
    $res = mysqli_query($mysql, "SELECT * FROM rooms ORDER BY name ASC");
    echo "\t\t<h2>Neuen Defekt melden...</h2>\n";
    echo "<div><ul><li><form action=\"newissue.php\" method=\"GET\">in Raumnummer: \n";
    echo "<select name=\"roomid\">\n";
    $rn = mysqli_num_rows($res);
    $classrooms = array();
    for ($i = 0; $i < $rn; $i++) {
        $row = mysqli_fetch_assoc($res);
        echo "<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>\n";
        if ($row['class'] != "" && !in_array($row['class'], $classrooms)) $classrooms[] = $row['class'];
    }
    echo "</select> <input type=\"submit\" value=\"melden\"></form></li>\n";
    echo "<li><form action=\"newissue.php\" method=\"GET\">im Klassenraum der Klasse: \n";
    echo "<select name=\"classroom\">\n";
    for ($i = 0; $i < sizeof($classrooms); $i++) {
        echo "<option value=\"" . $classrooms[$i] . "\">" . $classrooms[$i] . "</option>\n";
    }
    echo "</select> <input type=\"submit\" value=\"melden\"></form></li></ul></div>\n";
}

//$res = mysqli_query($mysql, "SELECT issues.*, items.name AS iname, r2.name AS rname, r2.id AS rid, r1.name AS rname_alt, r1.id AS rid_alt, users.name AS uname FROM issues LEFT JOIN users ON users.id = issues.reporter_id LEFT JOIN rooms AS r1 ON issues.room_id = r1.id, items LEFT JOIN rooms AS r2 ON items.room_id = r2.id WHERE issues.item_id = items.id OR (issues.item_id = -1 AND issues.room_id = r1.id) GROUP BY issues.id ORDER BY issues.time_reported DESC LIMIT 20");   // JESUS CHRIST!
$res = mysqli_query($mysql, "SELECT * FROM issues GROUP BY time_reported desc");
$rn = mysqli_num_rows($res);

echo "<h2>Momentan bestehende Defekte:</h2>";
if ($rn == 20) echo "<p style=\"font-style: italic;\">Zu viele Defekte... zeige nur die neuesten 20!</p>";
echo "<div><table><tr><th>ID</th><th>Raum</th><th>Defektes Gerät</th><th>gemeldet:</th><th>von:</th><th>Schweregrad:</th><th></th></tr>";
for ($i = 0; $i < $rn; $i++) {
    $issue = mysqli_fetch_assoc($res); 
    $item = NULL;
    $temp = mysqli_query($mysql, "SELECT * FROM items WHERE id = " . $issue['item_id']);
    $item = mysqli_fetch_assoc($temp);
    $room_id = -1;
    $user_id = $issue['reporter_id'];
    $temp = mysqli_query($mysql, "SELECT * FROM users WHERE id =" . $user_id);
    $user = mysqli_fetch_assoc($temp);
    if ($issue['item_id'] != -1) {
       $room_id = $item['room_id'];
    }
    else {
        $room_id = $issue['room_id'];
    }
    $temp = mysqli_query($mysql,"SELECT * FROM rooms WHERE id = " . $room_id);
    $room = mysqli_fetch_assoc($temp);
    echo "<tr class=\"" . (($i % 2) ? "tr-even" : "tr-odd"). "\">";
    echo "<td>" . $issue['id'] . "</td>";
    echo "<td>" . $room['name'] . "</td>";
    if ($issue['item_id'] != -1) echo "<td>" . $item['name'] . "</td>";
    else echo "<td>Sonstiges</td>";
    echo "<td>" . date("Y-m-d H:i:s", $issue['time_reported']) . "</td>";
    echo "<td>" . $user['name'] . "</td>";
    echo "<td>" . $issue['severity'] . "</td>";
    echo "<td><a href=\"showissue.php?id=" . $issue['id'] . "\">[ mehr ]</a></td>";
    echo "</tr>";
}
echo "</table>";

echo "</body></html>";

?>
