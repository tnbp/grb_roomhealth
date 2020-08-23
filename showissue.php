<?php

require_once("lib/session.php");
require_once("lib/rh_html_parts.php");
require_once("lib/permissions.php");
require_once("lib/comments.php");
require("include/acceptable.php");

rh_session();

if (!isset($_GET['id'])) redirect("index.php?error=invalid_issue_show");
$id = (int) $_GET['id'];
$res = mysqli_query($mysql, "SELECT issues.*,users.name FROM issues LEFT JOIN users ON issues.reporter_id = users.id WHERE issues.id = " . $id);
$issue = mysqli_fetch_assoc($res);

rh_html_init();
rh_html_head("GRB: IT-Defekt #" . $id);
rh_html_add("body", true);
rh_html_down();
if (can_post_comment($issue)) rh_html_add_js(false, "rh_buttons_align.js");
rh_html_add("div", true, array("id" => "allcontainer"));
rh_html_down();
rh_html_add("h1", true, array(), false);
rh_html_add_text("GRB: Zeige Defekt #" . $id . "...");
rh_html_close();
rh_header();

if ($issue !== NULL) {
    $item_id = $issue['item_id'];
    $res = mysqli_query($mysql, "SELECT * FROM items WHERE id = " . $item_id);
    $item = mysqli_fetch_assoc($res);
    if ($item === NULL) $item = array("name" => "Sonstiges");
    $room_id = $issue['room_id'];
    if ($room_id == -1) $room_id = $item['room_id'];
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE id = " . $room_id);
    $room = mysqli_fetch_assoc($res);
    if ($issue['assignee_id'] != -1) {
        $res = mysqli_query($mysql, "SELECT * FROM users WHERE id = " . $issue['assignee_id']);
        $assignee = mysqli_fetch_assoc($res);
        $assignee = $assignee['name'];
    }
    else {
        $assignee = "niemandem";
    }
    rh_html_add("h2", true, array());
    rh_html_down();
    rh_html_add_text("Problem mit Gerät:", true, true);
    $style_string = "margin-left: 1em; margin-right: 3em; display: inline-block; font-weight: normal; text-align: center; font-size: 1em";
    rh_html_add("input", false, array("style" => ($style_string . "; min-width: 300px; width: " . (strlen($item['name']) / 1.66) . "em"), "value" => $item['name'], "readonly" => true)); // not great; TODO: better width accommodation
    rh_html_add_text("in Raum:");
    rh_html_add("input", false, array("style" => ($style_string . "; min-width: 100px; width: " . (strlen($room['name']) / 1.66) . "em"), "value" => $room['name'], "readonly" => true)); // not great either; see above
    rh_html_up();
    rh_html_add("fieldset", true, array("style" => "background-color: #f7f7ff"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Problemdetails");
    rh_html_add("fieldset", true, array("style" => "text-align: right; width: max-content; background-color: white"));
    rh_html_down(); 
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Meldung und Bearbeitung");
    rh_html_add("label", true, array("for" => "_reported", "style" => "min-width: 150px; display: inline-block"), false);
    rh_html_add_text("gemeldet:");
    rh_html_add("input", false, array("name" => "_reported", "value" => date("Y-m-d H:i:s", $issue['time_reported']), "readonly" => true, "id" => "_reported", "style" => "text-align: right; margin-bottom: .5em"));
    rh_html_add("br");
    rh_html_add("label", true, array("for" => "reporter", "style" => "min-width: 150px; display: inline-block"), false);
    rh_html_add_text("von:");
    rh_html_add("input", false, array("name" => "reporter", "value" => $issue['name'], "readonly" => true, "style" => "text-align: right", "id" => "reporter"));
    rh_html_add("br");
    rh_html_add("label", true, array("for" => "assignee", "style" => "min-width: 150px; display: inline-block; margin-top: 2.5em"), false);
    rh_html_add_text("zugewiesen:");
    rh_html_add("input", false, array("id" => "assignee", "style" => "text-align: right; margin-bottom: .5em", "value" => $assignee, "readonly" => true));
    rh_html_add("br");
    rh_html_add("label", true, array("for" => "_updated", "style" => "min-width: 150px; display: inline-block"), false);
    rh_html_add_text("zuletzt bearbeitet:");
    rh_html_add("input", false, array("name" => "_updated", "value" => date("Y-m-d H:i:s", $issue['last_updated']), "readonly" => true, "id" => "_updated", "style" => "text-align: right"));
    rh_html_up();
    rh_html_add("fieldset", true, array("style" => "background: white"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Problembeschreibung");
    rh_html_close();
    rh_html_add("pre", true, array(), false);
    rh_html_add_text(str_replace("\n", "", $issue['comment']), false, false);
    rh_html_close();
    rh_html_up(2);
    $show_delete_button = has_permission(PERMISSION_ISSUE_DELETE);
    $show_edit_button = has_permission(PERMISSION_ISSUE_SET_STATUS | PERMISSION_ISSUE_SET_SEVERITY | PERMISSION_ISSUE_SET_RESOLUTION | PERMISSION_ISSUE_EDIT);
    $show_selfassign_button = (has_permission(PERMISSION_ISSUE_ASSIGN_SELF) && $issue['assignee_id'] == -1);
    if ($show_delete_button || $show_edit_button || $show_selfassign_button) {
        rh_html_add("fieldset", true, array("style" => "text-align: right; width: max-content; margin-left: auto; background-color: #f7f7ff"), true);
        rh_html_down();
        rh_html_add("legend", true, array(), false);
        rh_html_add_text("Problembehandlung");
        rh_html_add("form", true, array("action" => "#", "method" => "POST"));
        rh_html_down();
        if ($show_delete_button) {
            rh_html_add("fieldset", true, array("style" => "display: inline-block", "class" => "rh_delete"));
            rh_html_down();
            rh_html_add("legend", true, array(), false);
            rh_html_add_text("Meldung löschen");
            rh_html_add("input", false, array("value" => "ok", "name" => "del_ok", "type" => "checkbox"));
            rh_html_add("input", false, array("value" => "Löschen", "formaction" => "postissue.php?id=" . $id . "&delete", "type" => "submit"));
            rh_html_up();
        }
        if ($show_selfassign_button) {
            rh_html_add("fieldset", true, array("style" => "display: inline-block; background-color: white"));
            rh_html_down();
            rh_html_add("legend", true, array(), false);
            rh_html_add_text("Meldung zuweisen");
            rh_html_add("input", false, array("value" => "Mir selbst zuweisen", "formaction" => "postissue.php?id=" . $id . "&assignself", "type" => "submit"));
            rh_html_up();
        }
        if ($show_edit_button) {
            rh_html_add("fieldset", true, array("style" => "display: inline-block; background-color: white"));
            rh_html_down();
            rh_html_add("legend", true, array(), false);
            rh_html_add_text("Meldung bearbeiten");
            rh_html_add("input", false, array("value" => "Bearbeiten", "formaction" => "editissue.php?id=" . $id, "type" => "submit"));
            rh_html_up();
        }
        rh_html_up(2);
    }
    rh_comment_section($issue);
    rh_html_end();
}
else {
    redirect("index.php?error=invalid_issue_show");
}

?>
