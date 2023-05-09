<?php

require_once("lib/rh_html_parts.php");
require_once("lib/session.php");
require_once("lib/permissions.php");
include("include/acceptable.php");

rh_session();
rh_html_init();

global $mysql, $session;

rh_html_head("GRB: Alle IT-Defekte");
rh_html_add("body", true);
rh_html_down();
rh_html_add("div", true, array("id" => "allcontainer"));
rh_html_down();
rh_html_add("h1", true, array(), false);
rh_html_add_text("GRB: Alle IT-Defekte");
rh_html_close();

rh_header();

if (isset($_GET['error'])) {
    $errorbox_style = array("style" => "border: 2px solid red; background-color: #ffa0a0");
    // ...
}

if ($session['loggedin'] === true) {
    rh_html_add("div", true, array("class" => "rh_no_print"));
    rh_html_down();
    rh_html_add("h2", true, array(), false);
    rh_html_down();
    rh_html_add("img", false, array("src" => "img/newissue.png", "alt" => "Neuen Defekt melden", "style" => "vertical-align: bottom"));
    rh_html_add_text("Neuen Defekt melden...");
    rh_html_up();
    rh_html_room_selector(false, "newissue.php");
    rh_html_up();
}

$order_by_raw = http_get_array("order_by");
$order_by = array();
if ($order_by_raw !== false) {
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
$reported_by = isset($_GET['reported_by']) ? (int) $_GET['reported_by'] : false;
$room = isset($_GET['room']) ? (int) $_GET['room'] : false;

/*  reported_by and room cannot be set as filters manually, but are handled here anyway.
    listfilter.php will remove them.
*/

$sql_where = " (issues.item_id = items.id OR (issues.item_id = -1 AND issues.room_id = r1.id))";
if ($status !== false) $sql_where .= " AND issues.status = '" . mysqli_real_escape_string($mysql, $status) . "'";
if ($assignee !== false) {
    if ($assignee == -1) $sql_where .= " AND (issues.assignee_id = -1 OR issues.assignee_id = NULL)";
    else $sql_where .= " AND issues.assignee_id = " . $assignee;
}
if ($min_severity !== false) $sql_where .= " AND issues.severity+0 <= " . $min_severity;
if ($reported_since !== false) $sql_where .= " AND issues.time_reported >= " . $reported_since;
if ($reported_by !== false) $sql_where .= " AND issues.reporter_id = " . $reported_by;
if ($room !== false) $sql_where .= " AND issues.room_id = " . $room;

$sql_query = "SELECT issues.*, items.name AS iname, r2.name AS rname, r2.id AS rid, r1.name AS rname_alt, r1.id AS rid_alt, u1.name AS repname, u2.name AS asgname 
             FROM issues LEFT JOIN users AS u1 ON u1.id = issues.reporter_id 
             LEFT JOIN users AS u2 ON u2.id = issues.assignee_id 
             LEFT JOIN rooms AS r1 ON issues.room_id = r1.id, items 
             LEFT JOIN rooms AS r2 ON items.room_id = r2.id 
             WHERE" . $sql_where . " 
             GROUP BY issues.id "
             . $sql_order;                      // I ACCEPT SATAN AS MY LORD AND SAVIOR
$res = mysqli_query($mysql, $sql_query);
$total = mysqli_num_rows($res);
$sql_query .= ($limit ? " LIMIT " . ($limit + 1) : "") .
             ($page ? " OFFSET " . ($page * $limit) : ""); 

$res = mysqli_query($mysql, $sql_query);
$rn = mysqli_num_rows($res);

$showlist = !isset($_GET['nolist']);

if ($showlist && (isset($_GET['min_sev']) || $reported_since !== false || $assignee !== false || $status !== false || $limit !== false)) rh_html_add_js("var filter_form_status = true;");
else rh_html_add_js("var filter_form_status = false;");

rh_html_add("h2", true, array(), false);
rh_html_down();
rh_html_add("img", false, array("src" => "img/listissues.png", "alt" => "Momentan bestehende Defekte", "style" => "vertical-align: bottom"));
rh_html_add_text("Momentan bestehende Defekte:");
rh_html_up();

rh_html_add("fieldset", true, array("id" => "listissues_filter", "style" => "margin-bottom: 2em; width: max-content", "class" => "rh_no_print"));
rh_html_down();
rh_html_add("legend", true, array("style" => "font-weight: bold; font-size: 14px", "id" => "listissues_filter_legend"), false);
rh_html_add_text("Filter");
rh_html_add("form", true, array("action" => "listfilter.php", "method" => "POST"));
rh_html_down();
rh_html_add("fieldset", true);
rh_html_down();
rh_html_add("legend", true);
rh_html_down();
rh_html_add_text("Schweregrad");
rh_html_add("input", false, array("name" => "c_min_sev", "value" => "ok", "type" => "checkbox", "checked" => (isset($_GET['min_sev'])), "id" => "c_min_sev"));
rh_html_up();
rh_html_add("label", true, array("for" => "min_sev", "style" => "min-width: 180px; display: inline-block"), false);
rh_html_add_text("Schweregrad mindestens: ", false, true);
rh_html_add("select", true, array("name" => "min_sev", "id" => "min_sev"));
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
rh_html_add("fieldset", true);
rh_html_down();
rh_html_add("legend", true);
rh_html_down();
rh_html_add_text("Datumsbereich");
rh_html_add("input", false, array("name" => "c_min_time", "value" => "ok", "type" => "checkbox", "checked" => ($reported_since !== false), "id" => "c_min_time"));
rh_html_up();
rh_html_add("label", true, array("for" => "min_date", "style" => "min-width: 180px; display: inline-block"), false);
rh_html_add_text("gemeldet seit:");
rh_html_add("input", false, array("type" => "date", "name" => "min_date", "value" => ($reported_since !== false ? date("Y-m-d", $reported_since) : false), "id" => "min_date"));
rh_html_add("input", false, array("type" => "time", "name" => "min_time", "value" => ($reported_since !== false ? date("H:i", $reported_since) : false)));
rh_html_up();
rh_html_add("fieldset", true);
rh_html_down();
rh_html_add("legend", true);
rh_html_down();
rh_html_add_text("Zuweisung");
rh_html_add("input", false, array("name" => "c_assignee", "value" => "ok", "type" => "checkbox", "checked" => ($assignee !== false), "id" => "c_assignee"));
rh_html_up();
rh_html_add("label", true, array("for" => "assignee", "style" => "min-width: 180px; display: inline-block"), false);
rh_html_add_text("zugewiesen: ", false, true);
rh_html_add("select", true, array("name" => "assignee", "id" => "assignee"));
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
rh_html_add("fieldset", true);
rh_html_down();
rh_html_add("legend", true, array());
rh_html_down();
rh_html_add_text("Status", false, true);
rh_html_add("input", false, array("name" => "c_status", "value" => "ok", "type" => "checkbox", "checked" => ($status !== false), "id" => "c_status"), false);
rh_html_indent_on_next(false);
rh_html_up();
rh_html_add("label", true, array("for" => "status", "style" => "min-width: 180px; display: inline-block"), false);
rh_html_add_text("nur mit Status:");
rh_html_add("select", true, array("name" => "status", "id" => "status"));
rh_html_down();
rh_html_add("option", true, array("value" => "OPEN", "selected" => ($status == "OPEN")), false);
rh_html_add_text("OPEN");
rh_html_close();
rh_html_add("option", true, array("value" => "CLOSED", "selected" => ($status == "CLOSED")), false);
rh_html_add_text("CLOSED");
rh_html_close();
rh_html_add("option", true, array("value" => "FIXED", "selected" => ($status == "FIXED")), false);
rh_html_add_text("FIXED");
rh_html_close();
rh_html_up(2);
rh_html_add("fieldset", true, array("style" => "text-align: right"));
rh_html_down();
rh_html_add("legend", true, array(), false);
rh_html_add_text("Anzahl");
rh_html_add("label", true, array("for" => "limit", "style" => "display: inline-block"), false);
rh_html_add_text("zeige höchstens", false, false);
rh_html_add("input", false, array("name" => "limit", "value" => $limit, "size" => "2", "style" => "text-align: center", "id" => "limit"), false);
rh_html_add_text(" Fehler", false, true);
rh_html_up();
rh_html_add("p", true, array("style" => "margin-left: 2em; text-align: right;"));
rh_html_down();
rh_html_add("input", false, array("value" => "Filter anwenden", "type" => "submit"));
rh_html_up(3);

if ($rn > $limit && $limit !== false || $page > 0) {
    // pagination
    $query_str = $_SERVER['QUERY_STRING'];
    $query_str = preg_replace('/&?page=[0-9]+/', '', $query_str);
    rh_html_add("h3", true, array("style" => "text-align: center; max-width: 50%"));
    rh_html_down();
    rh_html_add_text("Seite ");
    if ($page > 0) {
        rh_html_add("a", true, array("href" => "listissues.php?" . $query_str . (strlen($query_str) ? "&" : "") . "page=" . ($page - 1)));
        rh_html_add_text("↢");
        rh_html_close();
    }
    rh_html_add_text($page + 1);
    if ($rn > $limit) {
        rh_html_add("a", true, array("href" => "listissues.php?" . $query_str . (strlen($query_str) ? "&" : "") . "page=" . ($page + 1)));
        rh_html_add_text("↣");
        rh_html_close();
    }
    rh_html_add("br");
    $upper_limit = min($total, (($page + 1) * $limit));
    rh_html_add_text("Ergebnisse " . ($page * $limit + 1) . " bis " . $upper_limit . " (insgesamt " . $total . ")");
    rh_html_up();
}

if ($limit === false || $limit > $rn) $limit = $rn;

$t_data = array();
for ($i = 0; $i < $limit; $i++) {
    $row = mysqli_fetch_assoc($res);
    $cur_asgname = $row['asgname'] ? $row['asgname'] : "<span style=\"font-style: italic\">niemand</span>";
    $cur_actions = "<a href=\"showissue.php?id=" . $row['id'] ."\" class=\"showissue\"><img src=\"img/moreinfo.png\" alt=\"mehr Informationen\" title=\"mehr Informationen\"></a>";
    if (has_permission(PERMISSION_ISSUE_ASSIGN_SELF) && $row['assignee_id'] == -1) $cur_actions .= "&nbsp;<a href=\"postissue.php?id=" . $row['id'] ."&assignself\"><img src=\"img/assignself.png\" alt=\"mir selbst zuweisen\" title=\"mir selbst zuweisen\"></a>";
    if (has_permission(PERMISSION_ISSUE_EDIT)) $cur_actions .= "&nbsp;<a href=\"editissue.php?id=" . $row['id'] ."\"><img src=\"img/edit.png\" alt=\"bearbeiten\" title=\"bearbeiten\"></a>";
    $cur = array($row['id'], ($row['item_id'] == -1) ? $row['rname_alt'] : $row['rname'], ($row['item_id'] == -1) ? "Sonstiges" : $row['iname'], date("Y-m-d H:i:s", $row['time_reported']), $row['repname'], "<span class=\"sev_" . $row['severity'] . "\"><img src=\"img/sev_" . $row['severity'] . ".png\" alt=\"" . $row['severity'] . "\">" . $severity_description[$row['severity']] . "</span>", $cur_asgname, date("Y-m-d H:i:s", $row['last_updated']), rh_resolution_img($row['status'], $row['resolution']) . "<span style=\"float: left; display: inline-block; text-align: left\">" . $row['status'] . "<br>" . $row['resolution'] . "</span>", $cur_actions, "linetext" => array($row['title'] == NULL ? "ohne Titel" : $row['title']));
    $t_data[] = $cur;
}

$current_filters = array();
if (isset($_GET['min_sev'])) $current_filters['min_sev'] = (int) $_GET['min_sev'];
if ($reported_since !== false) $current_filters['min_time'] = $reported_since;
if ($assignee !== false) $current_filters['assignee'] = $assignee;
if ($status !== false) $current_filters['status'] = $status;
if (isset($_GET['limit'])) $current_filters['limit'] = (int) $_GET['limit'];
if ($page !== false) $current_filters['page'] = $page;
if ($order_by_raw !== false) $current_filters['order_by'] = $order_by_raw;
else $current_filters['order_by'] = array("id_d");

$t_header = rh_htmlentities_array(array("id" => "ID", "Raum", "Defektes Gerät", "tr" => "gemeldet", "von", "sev" => "Schweregrad", "asg" => "zugewiesen", "up" => "letzte Änderung", "st" => "Status", "Aktionen", "th_attr" => array("style" => "font-weight: bold; border: 2px solid black; padding: 1em 1em")));
$t_attr = array("style" => "width: 100%");
$t_tdattr = array("style" => "text-align: center; border: 1px solid black; padding: .5em .5em");

rh_html_table($t_header, $t_data, $t_attr, $t_tdattr, array(), "listfilter.php", $current_filters);
rh_html_add_js(false, "rh_collapsible_filterlist.js");
rh_html_end();

?>
