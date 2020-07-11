<?php

require_once("generic_html.php");
require_once("lib/session.php");
require_once("lib/permissions.php");

rh_session();

require_loggedin_or_redirect();

global $mysql, $session;

generic_header();
echo "\t<body>\n";
echo "\t\t<h1>GRB Raumstatus</h1>\n";

rh_loginform();

echo "<h2>Neuen Defekt melden</h2>\n<div>";

if (isset($_GET['roomid'])) {
    $room_id = (int) $_GET['roomid'];
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE id = " . $room_id);
    $rc = mysqli_num_rows($res);
}
else if (isset($_POST['roomid'])) {
    $room_id = (int) $_POST['roomid'];
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE id = " . $room_id);
    $rc = mysqli_num_rows($res);
}
else if (isset($_GET['classroom'])) {
    $classroom = $_GET['classroom'];
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE class = '" . mysqli_real_escape_string($mysql, $classroom) . "'");
    $rc = mysqli_num_rows($res);
}
else $rc = 0;

echo "<form action=\"newissue.php\" method=\"POST\">";

if ($rc == 0) print_room_selector(false);
else {
    $row = mysqli_fetch_assoc($res);
    print_room_selector($row);
    
    echo "<p>Defekter Gegenstand: <select name=\"itemid\">";
    $res = mysqli_query($mysql, "SELECT * FROM items WHERE room_id = " . (int) $row['id']);
    $rc = mysqli_num_rows($res);
    for ($i = 0; $i < $rc; $i++) {
        $row = mysqli_fetch_assoc($res);
        // echo "<option value=\"" . $row['id'] . "\"" . ($_POST['item_id'] == $row['id'] ? " selected" : "") .">" . $row['name'] . "</option>"; // too complicated (see below)
        echo "<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>";
    }
    echo "<option value=\"-1\">Sonstiges</option></select></p>";
    echo "<p>N&auml;here Angaben: <textarea name=\"comment\">";
    echo htmlentities($_POST['comment'], ENT_QUOTES);
    echo "</textarea></p>";
    echo "<p>Schweregrad: <ul>";
    echo "<li><input type=\"radio\" name=\"severity\" value=\"critical\"" . ($_POST['severity'] == "critical" ? " checked" : "") . ">schwerwiegend &mdash; Unterricht ist durch den Defekt praktisch nicht m&ouml;glich</li>";
    echo "<li><input type=\"radio\" name=\"severity\" value=\"high\"" . ($_POST['severity'] == "high" ? " checked" : "") . ">hoch &mdash; Unterricht wird durch den Defekt stark beeinflusst</li>";
    echo "<li><input type=\"radio\" name=\"severity\" value=\"normal\"" . ($_POST['severity'] == "normal" ? " checked" : "") . ">mittel &mdash; einige Unterrichtsmethoden werden beeinträchtigt</li>";
    echo "<li><input type=\"radio\" name=\"severity\" value=\"low\"" . ($_POST['severity'] == "low" ? " checked" : "") . ">niedrig &mdash; Unterricht wird kaum beeinflusst</li>";
    echo "</ul></p>";
    echo "<input type=\"submit\" formaction=\"postissue.php\" value=\"Defekt melden\">";
    
}
echo "</form></div>";
echo "</body></html>";
    
function print_room_selector($room) {
    global $mysql, $session;
    
    echo "Raum: ";
    if ($room === false) {
        $res = mysqli_query($mysql, "SELECT * FROM rooms ORDER BY name ASC");
        $rc = mysqli_num_rows($res);
        echo "<select name=\"roomid\">";
        for ($i = 0; $i < $rc; $i++) {
            $row = mysqli_fetch_assoc($res);
            if ($row['class'] == "") echo "<option value=\"" . $row['id'] . "\">" . $row['name'] . "</option>";
            else echo "<option value=\"" . $row['id'] . "\">" . $row['name'] . " (Klassenraum " . $row['class']. ")</option>";
        }
        echo "</select><input type=\"submit\" value=\"Ausw&auml;hlen\">";
        // echo "<input type=\"hidden\" name=\"item_id\" value=\"" . htmlentities($_POST['item_id'], ENT_QUOTES) . "\">";   // This makes no sense; if you change the room, this has to become invalid
        echo "<input type=\"hidden\" name=\"comment\" value=\"" . htmlentities($_POST['comment'], ENT_QUOTES) . "\">";
        echo "<input type=\"hidden\" name=\"severity\" value=\"" . htmlentities($_POST['severity'], ENT_QUOTES) . "\">";
    }
    else {
        echo "<span style=\"font-weight: bold;\">" . $room['name'] . "</span>";
        if ($room['class'] != "") echo " (Klassenraum " . $room['class'] . ")";
        echo " <input type=\"submit\" value=\"&Auml;ndern\">";
        echo "<input type=\"hidden\" name=\"roomid\" value=\"".$room['id']."\">";
    }
}

?>
