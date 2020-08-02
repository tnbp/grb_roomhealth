<?php

require_once("lib/rh_html_parts.php");
require_once("lib/session.php");
include("include/acceptable.php");

rh_session();
rh_html_init();

global $mysql, $session;

rh_html_head("GRB Room Health", "GRB Room Health", "Fehlermeldung, IT-Defekte");
rh_html_add("body", true);
rh_html_down();
rh_html_add("h1", true, array(), false);
rh_html_add_text("GRB Raumstatus");
rh_html_close();

rh_loginform();

if (isset($_GET['error'])) {
    $errorbox_style = array("style" => "border: 2px solid red; background-color: #ffa0a0");
    if ($_GET['error'] == "invalid_issue_post") {
        if ($_GET['part'] == "room") rh_html_box("<span style=\"font-weight: bold\">FEHLER: </span>Ung&uuml;ltiger Raum.", $errorbox_style);
        else if ($_GET['part'] == "item") rh_html_box("<span style=\"font-weight: bold\">FEHLER: </span>Ung&uuml;ltiger Gegenstand.", $errorbox_style);
        else if ($_GET['part'] == "severity") rh_html_box("<span style=\"font-weight: bold\">FEHLER: </span>Ung&uuml;ltige Dringlichkeit.", $errorbox_style);
        else rh_html_box("<span style=\"font-weight: bold\">FEHLER: </span>Unbekannter Fehler.", $errorbox_style);
    }
    else if ($_GET['error'] == "invalid_issue_show") {
        rh_html_box("<span style=\"font-weight: bold\">FEHLER: </span>Ung&uuml;ltige Fehler-ID.", $errorbox_style);
    }
    else rh_html_box("<span style=\"font-weight: bold\">FEHLER: </span>Unbekannter Fehler (error: " . $_GET['error'] . ", part: " . $_GET['part'] . ").", $errorbox_style);
}

if ($session['loggedin'] === true) {
    $res = mysqli_query($mysql, "SELECT * FROM rooms ORDER BY name ASC");
    rh_html_add("h2", true, array(), false);
    rh_html_add_text("Neuen Defekt melden...");
    rh_html_close();
    rh_html_add("div", true, array(), true);
    rh_html_down(); // now in div
    rh_html_add("ul", true, array(), true);
    rh_html_down(); // now in ul
    rh_html_add("li", true, array(), true);
    rh_html_down(); // now in li
    rh_html_add("form", true, array("action" => "newissue.php", "method" => "GET"), true);
    rh_html_down(); // now in form
    rh_html_add_text("in Raumnummer: ", true, true);
    rh_html_add("select", true, array("name" => "roomid"), true);
    rh_html_down(); // now in select
    $rn = mysqli_num_rows($res);
    $classrooms = array();
    for ($i = 0; $i < $rn; $i++) {
        $row = mysqli_fetch_assoc($res);
        rh_html_add("option", true, array("value" => $row['id']), false);
        rh_html_add_text($row['name']);
        if ($row['class'] != "" && !in_array($row['class'], $classrooms)) $classrooms[] = $row['class'];
    }
    rh_html_indent_on_next(false);
    rh_html_close();
    rh_html_up(); // leaving select
    rh_html_add("input", false, array("type" => "submit", "value" => "melden"), true);
    rh_html_up(2); // leaving form, li
    rh_html_add("li", true, array(), true);
    rh_html_down(); // now in li
    rh_html_add("form", true, array("action" => "newissue.php", "method" => "GET"), true);
    rh_html_down(); // now in form
    rh_html_add_text("im Klassenraum der Klasse: ", true, true);
    rh_html_add("select", true, array("name" => "classroom"), true);
    rh_html_down(); // now in select
    for ($i = 0; $i < sizeof($classrooms); $i++) {
        rh_html_add("option", true, array("value" => $classrooms[$i]), false);
        rh_html_add_text($classrooms[$i]);
    }
    rh_html_indent_on_next(false);
    rh_html_close();
    rh_html_up(); // leaving select
    rh_html_add("input", false, array("type" => "submit", "value" => "melden"), true);
    rh_html_up(4); // leaving form, li, ul, div
}

$res = mysqli_query($mysql, "SELECT issues.*, items.name AS iname, r2.name AS rname, r2.id AS rid, r1.name AS rname_alt, r1.id AS rid_alt, users.name AS uname FROM issues LEFT JOIN users ON users.id = issues.reporter_id LEFT JOIN rooms AS r1 ON issues.room_id = r1.id, items LEFT JOIN rooms AS r2 ON items.room_id = r2.id WHERE (issues.item_id = items.id OR (issues.item_id = -1 AND issues.room_id = r1.id)) AND issues.status = 'OPEN' GROUP BY issues.id ORDER BY issues.time_reported DESC LIMIT 20");   // JESUS CHRIST!

$rn = mysqli_num_rows($res);

rh_html_add("h2", true, array(), false);
rh_html_add_text("Momentan bestehende Defekte:");
if ($rn == 20) {
    rh_html_add("p", true, array("style" => "font-style: italic"), false);
    rh_html_add_text("Zu viele Defekte... zeige nur die neuesten 20!");
}

$t_data = array();
for ($i = 0; $i < $rn; $i++) {
    $row = mysqli_fetch_assoc($res);
    $cur = array($row['id'], ($row['item_id'] == -1) ? $row['rname_alt'] : $row['rname'], ($row['item_id'] == -1) ? "Sonstiges" : $row['iname'], date("Y-m-d H:i:s", $row['time_reported']), $row['uname'], $severity_description[$row['severity']], "[&nbsp;<a href=\"showissue.php?id=" . $row['id'] ."\">mehr</a>&nbsp;]");
    $t_data[] = $cur;
}
$t_header = array("ID:", "Raum:", "Defektes Ger&auml;t:", "gemeldet:", "von:", "Schweregrad:", "Aktionen", "th_attr" => array("style" => "font-weight: bold; color: orange; border: 2px solid black; padding: 5px 5px"));
$t_attr = array();
$t_tdattr = array("style" => "text-align: center; border: 1px solid black; padding: 2px 2px");

rh_html_table($t_header, $t_data, $t_attr, $t_tdattr);

rh_html_up(999);

?>
