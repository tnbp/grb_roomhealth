<?php

require_once("lib/rh_html_parts.php");
require_once("lib/session.php");
require_once("lib/permissions.php");
include("include/acceptable.php");

rh_session();
rh_html_init();

global $mysql, $session;

rh_html_head("GRB Room Health", "GRB Room Health", "Fehlermeldung, IT-Defekte");
rh_html_add("body", true);
rh_html_down();
rh_html_add("h1", true, array(), false);
rh_html_add_text("GRB Raumstatus - Alle Defekte");
rh_html_close();

rh_loginform();

if (isset($_GET['error'])) {
    $errorbox_style = array("style" => "border: 2px solid red; background-color: #ffa0a0");
    // ...
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

$order_by_raw = is_array($_GET['order_by']) ? $_GET['order_by'] : false;
$order_by = array();
if (is_array($order_by_raw)) {
    foreach ($order_by_raw as $cur_o) {
        if (!in_array($cur_o, $order_acceptable)) continue;
        $o = explode("_", $cur_o);
        $order_by[] = $order_description[$o[0]] . " " . ($o[1] == "a" ? "ASC" : "DESC");
    }
}
if (count($order_by)) $sql_order = "ORDER BY " . implode(", ", $order_by);
else $sql_order = "ORDER BY issues.id DESC";

$min_severity = isset($_GET['min_sev']) ? (int) $_GET['min_sev'] : 4;
$reported_since = isset($_GET['min_time']) ? (int) $_GET['min_time'] : false;
$assignee = isset($_GET['assignee']) ? (int) $_GET['assignee'] : false;
$status = isset($_GET['status']) ? $_GET['status'] : false;
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : false;
$page = (isset($_GET['page']) && $limit) ? (int) $_GET['page'] : false;

$sql_where = " (issues.item_id = items.id OR (issues.item_id = -1 AND issues.room_id = r1.id))";
if ($status !== false) $sql_where .= " AND issues.status = '" . mysqli_real_escape_string($mysql, $status) . "'";
if ($assignee !== false) $sql_where .= " AND issues.assignee_id = " . $assignee;
if ($min_severity !== false) $sql_where .= " AND issues.severity+0 <= " . $min_severity;
if ($reported_since !== false) $sql_where .= " AND issues.time_reported >= " . $reported_since;

$sql_query = "SELECT issues.*, items.name AS iname, r2.name AS rname, r2.id AS rid, r1.name AS rname_alt, r1.id AS rid_alt, u1.name AS repname, u2.name AS asgname 
             FROM issues LEFT JOIN users AS u1 ON u1.id = issues.reporter_id 
             LEFT JOIN users AS u2 ON u2.id = issues.assignee_id 
             LEFT JOIN rooms AS r1 ON issues.room_id = r1.id, items 
             LEFT JOIN rooms AS r2 ON items.room_id = r2.id 
             WHERE" . $sql_where . " 
             GROUP BY issues.id "
             . $sql_order .
             ($limit ? " LIMIT " . ($limit + 1) : "") .
             ($page ? " OFFSET " . ($page * $limit) : ""); // I ACCEPT SATAN AS MY LORD AND SAVIOR

$res = mysqli_query($mysql, $sql_query);
$rn = mysqli_num_rows($res);
/*rh_html_add("h1", true, array(), false);
rh_html_add_text("DEBUG: " . $sql_query);*/

rh_html_add("h2", true, array(), false);
rh_html_add_text("Momentan bestehende Defekte:");
if ($rn > $limit) {
    // pagination!
}
rh_html_add("div", true, array("style" => "max-width: 50%"));
rh_html_down();
rh_html_add("form", true, array("action" => "listfilter.php", "method" => "POST"));
rh_html_down();
rh_html_add("p", true, array("style" => "font-weight: bold; font-size: 1.2em"), false);
rh_html_add_text("Filter:");
rh_html_close();
rh_html_add("p", true, array("style" => "margin-left: 2em"));
rh_html_down();
rh_html_add("input", false, array("name" => "c_min_sev", "value" => "ok", "type" => "checkbox", "checked" => (isset($_GET['min_sev']))), false);
rh_html_add_text("Schweregrad mindestens: ", false, true);
rh_html_add("select", true, array("name" => "min_sev"));
rh_html_down();
rh_html_add("option", true, array("value" => "4", "selected" => ($min_severity == 4)), false);
rh_html_add_text("egal");
rh_html_close();
rh_html_add("option", true, array("value" => "3", "selected" => ($min_severity == 3)), false);
rh_html_add_text("mittel");
rh_html_close();
rh_html_add("option", true, array("value" => "2", "selected" => ($min_severity == 2)), false);
rh_html_add_text("hoch");
rh_html_close();
rh_html_add("option", true, array("value" => "1", "selected" => ($min_severity == 1)), false);
rh_html_add_text("schwerwiegend");
rh_html_close();
rh_html_up(2);
rh_html_add("p", true, array("style" => "margin-left: 2em"));
rh_html_down();
rh_html_add("input", false, array("name" => "c_min_time", "value" => "ok", "type" => "checkbox", "checked" => $reported_since !== false), false);
rh_html_add_text("gemeldet seit: ", false, false);
rh_html_add("input", false, array("type" => "date", "name" => "min_date", "value" => ($reported_since !== false ? date("Y-m-d", $reported_since) : false)), false);
rh_html_add("input", false, array("type" => "time", "name" => "min_time", "value" => ($reported_since !== false ? date("H:i", $reported_since) : false)));
rh_html_up();
rh_html_add("p", true, array("style" => "margin-left: 2em"));
rh_html_down();
rh_html_add("input", false, array("name" => "c_assignee", "value" => "ok", "type" => "checkbox", "checked" => ($assignee !== false)), false);
rh_html_add_text("zugewiesen: ", false, true);
rh_html_add("select", true, array("name" => "assignee"));
rh_html_down();
rh_html_add("option", true, array("value" => "-1", "selected" => ($assignee === false || $assignee == -1)), false);
rh_html_add_text("niemandem");
rh_html_close();
$assignable_users = mysqli_query($mysql, "SELECT * FROM users WHERE permissions & " . PERMISSION_ISSUE_ASSIGNABLE);
while (($row = mysqli_fetch_assoc($assignable_users)) !== NULL) {
    rh_html_add("option", true, array("value" => $row['id'], "selected" => ($row['id'] == $assignee)), false);
    rh_html_add_text($row['name']);
    rh_html_close();
}
rh_html_up(2);
rh_html_add("p", true, array("style" => "margin-left: 2em"));
rh_html_down();
rh_html_add("input", false, array("name" => "c_status", "value" => "ok", "type" => "checkbox", "checked" => ($status !== false)), false);
rh_html_add_text("Status: ", false, true);
rh_html_add("select", true, array("name" => "status"));
rh_html_down();
rh_html_add("option", true, array("value" => "OPEN", "selected" => ($status == "OPEN")), false);
rh_html_add_text("OPEN");
rh_html_close();
rh_html_add("option", true, array("value" => "CLOSED", "selected" => ($status == "CLOSED")), false);
rh_html_add_text("CLOSED");
rh_html_close();
rh_html_up(2);
rh_html_add("p", true, array("style" => "margin-left: 2em"));
rh_html_down();
rh_html_add("input", false, array("name" => "c_limit", "value" => "ok", "type" => "checkbox", "checked" => ($limit !== false)), false);
rh_html_add_text("zeige höchstens ", false, false);
rh_html_add("input", false, array("name" => "limit", "value" => $limit, "size" => "2", "style" => "text-align: center"), false);
rh_html_add_text(" Fehler", false, true);
rh_html_up();
rh_html_add("p", true, array("style" => "margin-left: 2em; text-align: right;"));
rh_html_down();
rh_html_add("input", false, array("value" => "Filter anwenden", "type" => "submit"));
rh_html_up(3);

if ($limit === false || $limit > $rn) $limit = $rn;

$t_data = array();
for ($i = 0; $i < $limit; $i++) {
    $row = mysqli_fetch_assoc($res);
    $cur_asgname = $row['asgname'] ? $row['asgname'] : "<span style=\"font-style: italic\">niemand</span>";
    $cur_actions = "[&nbsp;<a href=\"showissue.php?id=" . $row['id'] ."\">mehr</a>&nbsp;]";
    if (has_permission(PERMISSION_ISSUE_ASSIGN_SELF) && $row['assignee_id'] == -1) $cur_actions .= " [&nbsp;<a href=\"postissue.php?id=" . $row['id'] ."&assignself\">mir selbst zuweisen</a>&nbsp;]";
    if (has_permission(PERMISSION_ISSUE_EDIT)) $cur_actions .= " [&nbsp;<a href=\"editissue.php?id=" . $row['id'] ."\">bearbeiten</a>&nbsp;]";
    $cur = array($row['id'], ($row['item_id'] == -1) ? $row['rname_alt'] : $row['rname'], ($row['item_id'] == -1) ? "Sonstiges" : $row['iname'], date("Y-m-d H:i:s", $row['time_reported']), $row['repname'], $severity_description[$row['severity']], $cur_asgname, date("Y-m-d H:i:s", $row['last_updated']), $row['status'] . ": " . $row['resolution'], $cur_actions);
    $t_data[] = $cur;
}
$t_header = array("ID:", "Raum:", "Defektes Ger&auml;t:", "gemeldet:", "von:", "Schweregrad:", "zugewiesen:", "letzte Änderung", "Status:", "Aktionen", "th_attr" => array("style" => "font-weight: bold; color: orange; border: 2px solid black; padding: 5px 5px"));
$t_attr = array();
$t_tdattr = array("style" => "text-align: center; border: 1px solid black; padding: 2px 2px");

rh_html_table($t_header, $t_data, $t_attr, $t_tdattr);

rh_html_up(999);

?>
