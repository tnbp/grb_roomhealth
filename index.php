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

$res = mysqli_query($mysql, "SELECT issues.*, items.name as iname, rooms.name as rname, users.name as uname FROM issues LEFT JOIN users ON users.id = issues.reporter_id, items LEFT JOIN rooms ON items.room_id = rooms.id WHERE issues.item_id = items.id ORDER BY issues.time_reported DESC LIMIT 20");
$rn = mysqli_num_rows($res);

echo "<h2>Momentan bestehende Defekte:</h2>";
if ($rn == 20) echo "<p style=\"font-style: italic;\">Zu viele Defekte... zeige nur die neuesten 20!</p>";
echo "<div><table><tr><th>ID</th><th>Raum</th><th>Defektes Gerät</th><th>gemeldet:</th><th>von:</th><th>Schweregrad:</th><th></th></tr>";
for ($i = 0; $i < $rn; $i++) {
    $row = mysqli_fetch_assoc($res);
    echo "<tr class=\"" . (($i % 2) ? "tr-even" : "tr-odd"). "\">";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . $row['rname'] . "</td>";
    echo "<td>" . $row['iname'] . "</td>";
    echo "<td>" . date("Y-m-d H:i:s", $row['time_reported']) . "</td>";
    echo "<td>" . $row['uname'] . "</td>";
    echo "<td>" . $row['severity'] . "</td>";
    echo "<td><a href=\"showissue.php?id=" . $row['id'] . "\">[ mehr ]</a></td>";
    echo "</tr>";
}
echo "</table>";

echo "</body></html>";

?>
