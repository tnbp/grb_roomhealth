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

$res = mysqli_query($mysql, "SELECT issues.*, items.name AS iname, r2.name AS rname, r2.id AS rid, r1.name AS rname_alt, r1.id AS rid_alt, users.name AS uname FROM issues LEFT JOIN users ON users.id = issues.reporter_id LEFT JOIN rooms AS r1 ON issues.room_id = r1.id, items LEFT JOIN rooms AS r2 ON items.room_id = r2.id WHERE issues.item_id = items.id OR (issues.item_id = -1 AND issues.room_id = r1.id) GROUP BY issues.id ORDER BY issues.time_reported DESC LIMIT 20");   // JESUS CHRIST!

$rn = mysqli_num_rows($res);

echo "<h2>Momentan bestehende Defekte:</h2>";
if ($rn == 20) echo "<p style=\"font-style: italic;\">Zu viele Defekte... zeige nur die neuesten 20!</p>";
echo "<div><table><tr><th>ID</th><th>Raum</th><th>Defektes Gerät</th><th>gemeldet:</th><th>von:</th><th>Schweregrad:</th><th></th></tr>";
for ($i = 0; $i < $rn; $i++) {
    $row = mysqli_fetch_assoc($res);
    echo "<tr class=\"" . (($i % 2) ? "tr-even" : "tr-odd"). "\">";
    echo "<td>" . $row['id'] . "</td>";
    if ($row['item_id'] != -1) echo "<td>" . $row['rname'] . "</td>";
    else echo "<td>" . $row['rname_alt'] . "</td>";
    if ($row['item_id'] != -1) echo "<td>" . $row['iname'] . "</td>";
    else echo "<td>Sonstiges</td>";
    echo "<td>" . date("Y-m-d H:i:s", $row['time_reported']) . "</td>";
    echo "<td>" . $row['uname'] . "</td>";
    echo "<td>" . $row['severity'] . "</td>";
    echo "<td><a href=\"showissue.php?id=" . $row['id'] . "\">[ mehr ]</a></td>";
    echo "</tr>";
}
echo "</table>";

echo "</body></html>";

?>
