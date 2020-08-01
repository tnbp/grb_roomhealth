<?php

require_once("lib/session.php");
require_once("lib/rh_html_parts.php");
require_once("lib/permissions.php");
require_once("lib/comments.php");
require("include/acceptable.php");

rh_session();

if (!isset($_GET['id'])) redirect("index.php?error=invalid_issue_show");
$id = (int) $_GET['id'];

rh_html_init();
rh_html_head("Fehler #" . $id);
rh_html_add("body", true);
rh_html_down();

$res = mysqli_query($mysql, "SELECT issues.*,users.name FROM issues LEFT JOIN users ON issues.reporter_id = users.id WHERE issues.id = " . $id);
$issue = mysqli_fetch_assoc($res);

if ($issue !== NULL) {
    $item_id = $issue['item_id'];
    $res = mysqli_query($mysql, "SELECT * FROM items WHERE id = " . $item_id);
    $item = mysqli_fetch_assoc($res);
    if ($item === NULL) $item = array("name" => "Sonstiges");
    $room_id = $issue['room_id'];
    if ($room_id == -1) $room_id = $item['room_id'];
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE id = " . $room_id);
    $room = mysqli_fetch_assoc($res);
    rh_html_add("h1", true, array(), false);
    rh_html_add_text("Zeige Defekt #" . $id . "...");
    rh_html_add("h2", true, array(), false);
    rh_html_add_text("Problem mit Gerät: " . $item['name'] . " in Raum: " . $room['name']);
    rh_html_add("hr");
    rh_html_add("p", true);
    rh_html_down();
    rh_html_add("span", true, array("style" => "font-weight: bold"), false);
    rh_html_add_text("Beschreibung: ");
    rh_html_close(false, false, false, false);
    rh_html_add_text($issue['comment'], false, true);
    rh_html_up();
    rh_html_add("p", true);
    rh_html_down();
    rh_html_add("span", true, array("style" => "font-weight: bold"), false);
    rh_html_add_text("gemeldet: ");
    rh_html_close(false, false, false, false);
    rh_html_add_text(date("Y-m-d H:i:s", $issue['time_reported']), false, true);
    rh_html_add("span", true, array("style" => "font-weight: bold"), false);
    rh_html_add_text(" von ");
    rh_html_close(false, false, false, false);
    rh_html_add_text($issue['name'], false, true);
    rh_html_up();
    rh_html_add("p", true);
    rh_html_down();
    rh_html_add("span", true, array("style" => "font-weight: bold"), false);
    rh_html_add_text("Schweregrad: ");
    rh_html_close(false, false, false, false);
    rh_html_add_text($severity_description[$issue['severity']], false, true);
    rh_html_up();
    rh_html_add("p", true);
    rh_html_down();
    rh_html_add("span", true, array("style" => "font-weight: bold"), false);
    rh_html_add_text("Status: ");
    rh_html_close(false, false, false, false);
    rh_html_add_text($issue['resolution'] . " " . $issue['status'], false, true);
    rh_html_up();
    rh_html_add("div", true, array(), true);
    rh_html_down();
    if ($issue['assignee_id'] != -1) {
        $res = mysqli_query($mysql, "SELECT * FROM users WHERE id = " . $issue['assignee_id']);
        $assignee = mysqli_fetch_assoc($res);
        rh_html_add_text("Zugewiesen: " . $assignee['name'], true);
    }
    else {
        rh_html_add("span", true, array("style" => "font-style: italic"), false);
        rh_html_add_text("noch niemand zugewiesen");
        rh_html_close();
    }
    rh_html_add("form", true, array("action" => "#", "method" => "POST"));
    rh_html_down();
    if (has_permission(PERMISSION_ISSUE_SET_STATUS | PERMISSION_ISSUE_SET_SEVERITY | PERMISSION_ISSUE_SET_RESOLUTION | PERMISSION_ISSUE_EDIT)) {
        /*rh_html_add_text(" [ ");
        rh_html_add("a", true, array("href" => "editissue.php?id=" . $id), false);
        rh_html_add_text("Problem bearbeiten");
        rh_html_close(false, false, false, false);
        rh_html_add_text(" ]", false, false);*/
        rh_html_add("input", false, array("value" => "Problem bearbeiten", "formaction" => "editissue.php?id=" . $id, "type" => "submit"));
    }
    if (has_permission(PERMISSION_ISSUE_ASSIGN_SELF) && $issue['assignee_id'] == -1) {
        /*rh_html_add_text(" [ ");
        rh_html_add("a", true, array("href" => "editissue.php?id=" . $id . "&assignself"), false);
        rh_html_add_text("mir selbst zuweisen");
        rh_html_close(false, false, false, false);
        rh_html_add_text(" ]", false, false);*/
        rh_html_add("input", false, array("value" => "mir selbst zuweisen", "formaction" => "postissue.php?id=" . $id . "&assignself", "type" => "submit"));
    }
    if (has_permission(PERMISSION_ISSUE_DELETE)) {
        /*rh_html_add_text(" [ ");
        rh_html_add("a", true, array("href" => "postissue.php?id=" . $id . "&delete"), false);
        rh_html_add_text("Problem löschen");
        rh_html_close(false, false, false, false);
        rh_html_add_text(" ]", false, true);*/
        rh_html_add("span", true, array("style" => "margin-left: 10em"));
        rh_html_down();
        rh_html_add("input", false, array("value" => "ok", "name" => "del_ok", "type" => "checkbox"));
        rh_html_add("input", false, array("value" => "Problem löschen", "formaction" => "postissue.php?id=" . $id . "&delete", "type" => "submit"));
    }
    rh_html_up(3);
    rh_comment_section($issue);
    rh_html_up(999);
}
else {
    redirect("index.php?error=invalid_issue_show");
}

?>
