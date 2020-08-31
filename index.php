<?php

require_once("lib/rh_html_parts.php");
require_once("lib/session.php");
require_once("lib/rh_errorhandler.php");
include("include/acceptable.php");

rh_session();
rh_html_init();

global $mysql, $session;

rh_html_head("GRB: IT-Defekte", "GRB: IT-Defekte", "Fehlermeldung, IT-Defekte");
rh_html_add("body", true);
rh_html_down();
rh_html_add_js(false, "rh_version.js");
rh_html_add("div", true, array("id" => "allcontainer"));
rh_html_down();
rh_html_add("h1", true, array(), false);
rh_html_add_text("GRB: IT-Defekte");
rh_html_close();

rh_header();

/*if (isset($_GET['error'])) {
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
}*/
rh_errorhandler();

if ($session['loggedin'] === true) {
    rh_html_add("h2", true, array(), false);
    rh_html_down();
    rh_html_add("img", false, array("src" => "img/newissue.png", "alt" => "Neuen Defekt melden", "style" => "vertical-align: bottom"));
    rh_html_add_text("Neuen Defekt melden...");
    rh_html_up();
    rh_html_add("div", true, array(), true);
    rh_html_down();
    rh_html_room_selector(false, "newissue.php");
    rh_html_up();
}

$res = mysqli_query($mysql, "SELECT issues.*, items.name AS iname, r2.name AS rname, r2.id AS rid, r1.name AS rname_alt, r1.id AS rid_alt, users.name AS uname FROM issues LEFT JOIN users ON users.id = issues.reporter_id LEFT JOIN rooms AS r1 ON issues.room_id = r1.id, items LEFT JOIN rooms AS r2 ON items.room_id = r2.id WHERE (issues.item_id = items.id OR (issues.item_id = -1 AND issues.room_id = r1.id)) AND issues.status = 'OPEN' GROUP BY issues.id ORDER BY issues.time_reported DESC LIMIT 20");   // JESUS CHRIST!

$rn = mysqli_num_rows($res);

rh_html_add("h2", true, array(), false);
rh_html_down();
rh_html_add("img", false, array("src" => "img/listissues.png", "alt" => "Momentan bestehende Defekte", "style" => "vertical-align: bottom"));
rh_html_add_text("Momentan bestehende Defekte:");
rh_html_up();
if ($rn == 20) {
    rh_html_add("p", true, array("style" => "font-style: italic"), false);
    rh_html_add_text("Zu viele Defekte... zeige nur die neuesten 20!");
}

$t_data = array();
for ($i = 0; $i < $rn; $i++) {
    $row = mysqli_fetch_assoc($res);
    $cur = array($row['id'], ($row['item_id'] == -1) ? $row['rname_alt'] : $row['rname'], ($row['item_id'] == -1) ? "Sonstiges" : $row['iname'], date("Y-m-d H:i:s", $row['time_reported']), $row['uname'], "<span class=\"sev_" . $row['severity'] . "\"><img src=\"img/sev_" . $row['severity'] . ".png\" alt=\"" . $row['severity'] . "\">" . $severity_description[$row['severity']] . "</span>", "<a href=\"showissue.php?id=" . $row['id'] ."\" class=\"showissue\"><img src=\"img/moreinfo.png\" alt=\"mehr Informationen\" title=\"mehr Informationen\"></a>");
    $t_data[] = $cur;
}
$t_header = rh_htmlentities_array(array("id" => "ID", "Raum", "Defektes Gerät", "tr" => "gemeldet", "von", "sev" => "Schweregrad", "Aktionen", "th_attr" => array("style" => "font-weight: bold; border: 2px solid black; padding: 1em 1em")));
$t_attr = array("style" => "margin-left: auto; margin-right: auto; width: 100%");
$t_tdattr = array("style" => "text-align: center; border: 1px solid black; padding: .5em .5em");

rh_html_table($t_header, $t_data, $t_attr, $t_tdattr, array(), "listissues.php", array("order_by" => array("tr_d")));

rh_html_end();

?>
