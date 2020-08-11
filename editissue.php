<?php 

require_once("lib/session.php");
require_once("lib/permissions.php");
require_once("lib/rh_html_parts.php");
require("include/acceptable.php");

rh_session();
rh_html_init();

global $mysql, $session;
require_permission_or_redirect(PERMISSION_ISSUE_SET_STATUS | PERMISSION_ISSUE_ASSIGN | PERMISSION_ISSUE_SET_SEVERITY | PERMISSION_ISSUE_SET_RESOLUTION);

$id = (int) $_GET['id'];
$res = mysqli_query($mysql, "SELECT * FROM issues WHERE id = " . $id);

rh_html_head("Bearbeiten: Fehler #" . $id);
rh_html_add("body", true);
rh_html_down();
rh_html_add("script", true, array("src" => "rh_buttons_align.js", "type" => "application/javascript"));
rh_html_add("div", true, array("id" => "allcontainer"));
rh_html_down();
rh_header();

$issue = mysqli_fetch_assoc($res);
if ($issue !== NULL) {
    $disableother = false;
    if (isset($_GET['resetroom'])) $disableother = true;
    $item_id = $issue['item_id'];
    $res = mysqli_query($mysql, "SELECT * FROM items WHERE id = " . $item_id);
    $item = mysqli_fetch_assoc($res);
    if ($item == NULL) $item = array("name" => "Sonstiges");
    $room_id = $issue['room_id'];
    if ($room_id == -1) $room_id = $item['room_id'];
    if (isset($_GET['newroom'])) {
        if (isset($_POST['by_room'])) $room_id = (int) $_POST['roomid'];
        if (isset($_POST['by_classroom'])) {    // BLERGH!
            $room = mysqli_query($mysql, "SELECT * FROM rooms WHERE class = '" . mysqli_real_escape_string($mysql, $_POST['classroom']) . "'");
            $room = mysqli_fetch_assoc($room);
            if ($room !== false) $room_id = $room['id'];
        }
    }
    if (isset($_POST['comment'])) $issue['comment'] = htmlentities($_POST['comment']);
    if (isset($_POST['severity'])) $issue['severity'] = htmlentities($_POST['severity']);
    if (isset($_POST['assignee_id'])) $issue['assignee_id'] = (int) $_POST['assignee_id'];
    if (isset($_POST['status'])) $issue['status'] = htmlentities($_POST['status']);
    if (isset($_POST['resolution'])) $issue['resolution'] = htmlentities($_POST['resolution']);
    if (isset($_POST['allow_comments'])) $issue['allow_comments'] = htmlentities($_POST['allow_comments']);
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE id = " . $room_id);
    $room = mysqli_fetch_assoc($res);
    $res = mysqli_query($mysql, "SELECT name,id FROM users WHERE id = " . $issue['reporter_id'] . " OR id = " . $issue['assignee_id']);
    $reporter = $assignee = "niemand";
    $un = mysqli_num_rows($res);
    for ($i = 0; $i < $un; $i++) {
        $user = mysqli_fetch_assoc($res);
        if ($user['id'] == $issue['reporter_id']) $reporter = $user['name'];
        if ($user['id'] == $issue['assignee_id']) $assignee = $user['name'];
    }
    rh_html_add("h1", true, array(), false);
    rh_html_add_text("Bearbeiten: Defekt #" . $id . "...");
    rh_html_add("h2", true, array(), false);
    rh_html_down();
    rh_html_add_text("Problem mit Gerät:");
    $style_string = "margin-left: 1em; margin-right: 3em; display: inline-block; font-weight: normal; text-align: center; font-size: 1em";
    rh_html_add("input", false, array("style" => ($style_string . "; min-width: 300px; width: " . (strlen($item['name']) / 1.66) . "em"), "value" => $item['name'], "readonly" => true), false); // not great; TODO: better width accommodation
    rh_html_add_text("in Raum:");
    rh_html_add("input", false, array("style" => ($style_string . "; min-width: 100px; width: " . (strlen($room['name']) / 1.66) . "em"), "value" => $room['name'], "readonly" => true), false); // not great either; see above
    rh_html_up();
    if (isset($_GET['error'])) {
        $errorbox_style = array("style" => "border: 2px solid red; background-color: #ffa0a0");
        if ($_GET['error'] == "nochange") rh_html_box("<span style=\"font-weight: bold\">FEHLER: </span>Keine &Auml;nderungen vorgenommen.", $errorbox_style);
    }
    rh_html_add("form", true, array("method" => "POST", "action" => "postissue.php?update"));
    rh_html_down(); 
    rh_html_add("fieldset", true, array("style" => "position: relative"));
    rh_html_down(); 
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Problemdetails");
    rh_html_add("input", false, array("name" => "issueid", "value" => $id, "type" => "hidden"));
    rh_html_add("div", true, array("style" => "width: max-content"));
    rh_html_down();
    rh_html_add("fieldset", true, array("style" => "text-align: right", "class" => ($disableother ? "rh_disabled" : false)));
    rh_html_down(); 
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Meldung und Bearbeitung");
    rh_html_add("label", true, array("for" => "_reported", "style" => "min-width: 150px; display: inline-block"), false);
    rh_html_add_text("gemeldet:");
    rh_html_add("input", false, array("name" => "_reported", "value" => date("Y-m-d H:i:s", $issue['time_reported']), "readonly" => true, "id" => "_reported", "style" => "text-align: right; margin-bottom: .5em"));
    rh_html_add("br");
    rh_html_add("label", true, array("for" => "reporter", "style" => "min-width: 150px; display: inline-block"), false);
    rh_html_add_text("von:");
    rh_html_add("input", false, array("name" => "reporter", "value" => $reporter, "readonly" => true, "style" => "text-align: right", "id" => "reporter"));
    rh_html_add("br");
    rh_html_add("label", true, array("for" => "_updated", "style" => "min-width: 150px; display: inline-block; margin-top: 2.5em"), false);
    rh_html_add_text("zuletzt bearbeitet:");
    rh_html_add("input", false, array("name" => "_updated", "value" => date("Y-m-d H:i:s", $issue['last_updated']), "readonly" => true, "id" => "_updated", "style" => "text-align: right"));
    rh_html_up(); 
    if (isset($_GET['resetroom'])) {
        rh_html_room_selector(false, "editissue.php?id=" . $id . "&newroom", false);
    }
    else rh_html_room_selector($room, "editissue.php?id=" . $id . "&resetroom");
    rh_html_up();
    rh_html_add("fieldset", true, array("class" => ($disableother ? "rh_disabled" : false)));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Problembeschreibung");
    rh_html_add("textarea", true, array("name" => "comment", "readonly" => !has_permission(PERMISSION_ISSUE_EDIT), "disabled" => $disableother, "style" => "width: 100%; min-height: 400px", "class" => !has_permission(PERMISSION_ISSUE_EDIT) ? "rh_disabled" : false), false);
    rh_html_add_text($issue['comment'], false, false);
    rh_html_close();
    rh_html_up(); 
    rh_html_add("fieldset", true, array("style" => "width: max-content", "class" => ($disableother ? "rh_disabled" : false)));
    rh_html_down(); 
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Nähere Angaben");
    rh_html_add("label", true, array("for" => "itemid", "style" => "min-width: 150px; display: inline-block"));
    rh_html_add_text("Defekter Gegenstand:", true, true);
    rh_html_add("select", true, array("name" => "itemid", "readonly" => !has_permission(PERMISSION_ISSUE_EDIT), "disabled" => $disableother, "id" => "itemid", "style" => "margin-right: 15em", "class" => !has_permission(PERMISSION_ISSUE_EDIT) ? "rh_disabled" : false));
    rh_html_down(); 
    $res = mysqli_query($mysql, "SELECT * FROM items WHERE room_id = " . $room_id);
    while (($row = mysqli_fetch_assoc($res)) !== NULL) {
        rh_html_add("option", true, array("value" => $row['id'], "selected" => ($row['id'] == $item_id)), false);
        rh_html_add_text($row['name']);
    }
    rh_html_add("option", true, array("value" => -1, "selected" => ($item_id == -1)), false);
    rh_html_add_text("Sonstiges");
    rh_html_close();
    rh_html_up();
    rh_html_add("label", true, array("for" => "severity", "style" => "margin-right: 2em; display: inline-block"));
    rh_html_add_text("Dringlichkeit:", true, true);
    rh_html_add("select", true, array("name" => "severity", "readonly" => !has_permission(PERMISSION_ISSUE_SET_SEVERITY), "disabled" => $disableother, "id" => "severity", "class" => !has_permission(PERMISSION_ISSUE_SET_SEVERITY) ? "rh_disabled" : false));
    rh_html_down(); 
    foreach ($severity_acceptable as $sev) {
        rh_html_add("option", true, array("value" => $sev, "selected" => ($issue['severity'] == $sev)), false);
        rh_html_add_text($severity_description[$sev]);
    }
    rh_html_close();
    rh_html_up(2);
    rh_html_add("fieldset", true, array("class" => ($disableother ? "rh_disabled" : false), "style" => "width: max-content; display: inline-block", "id" => "align_a"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Organisation");
    rh_html_add("label", true, array("for" => "assignee_id", "style" => "min-width: 150px; display: inline-block; margin-bottom: 1em"));
    rh_html_add_text("zugewiesen:", true, true);
    rh_html_add("select", true, array("name" => "assignee_id", "readonly" => !has_permission(PERMISSION_ISSUE_ASSIGN_SELF | PERMISSION_ISSUE_ASSIGN), "disabled" => $disableother, "id" => "assignee_id", "class" => !has_permission(PERMISSION_ISSUE_ASSIGN_SELF | PERMISSION_ISSUE_ASSIGN) ? "rh_disabled" : false));
    rh_html_down(); 
    $res = mysqli_query($mysql, "SELECT * FROM users WHERE permissions & " . PERMISSION_ISSUE_ASSIGNABLE);
    while (($row = mysqli_fetch_assoc($res)) !== NULL) {
        rh_html_add("option", true, array("value" => $row['id'], "selected" => ($row['id'] == $issue['assignee_id'])), false);
        rh_html_add_text($row['name']);
    }
    rh_html_add("option", true, array("value" => -1, "selected" => ($issue['assignee_id'] == -1)), false);
    rh_html_add_text("niemand");
    rh_html_close();
    rh_html_up();
    rh_html_add("br");
    rh_html_add("label", true, array("for" => "status_open", "style" => "min-width: 150px; display: inline-block; margin-bottom: 1em"), false);
    rh_html_add_text("Status:");
    rh_html_add("input", false, array("type" => "radio", "name" => "status", "value" => "OPEN", "checked" => $issue['status'] == "OPEN", "id" => "status_open"));
    rh_html_add("label", true, array("for" => "status_open", "style" => "margin-right: 2em"), false);
    rh_html_add_text("offen");
    rh_html_add("input", false, array("type" => "radio", "name" => "status", "value" => "CLOSED", "checked" => $issue['status'] == "CLOSED", "id" => "status_closed"));
    rh_html_add("label", true, array("for" => "status_closed"), false);
    rh_html_add_text("geschlossen");
    rh_html_add("br");
    rh_html_add("label", true, array("for" => "resolution", "style" => "min-width: 150px; display: inline-block; margin-bottom: 1em"));
    rh_html_add_text("Unterstatus:", true, true);
    rh_html_add("select", true, array("name" => "resolution", "readonly" => !has_permission(PERMISSION_ISSUE_SET_RESOLUTION), "disabled" => $disableother, "id" => "resolution", "class" => !has_permission(PERMISSION_ISSUE_SET_RESOLUTION) ? "rh_disabled" : false));
    rh_html_down(); 
    foreach ($resolution_acceptable as $resolution) {
        rh_html_add("option", true, array("value" => $resolution, "selected" => ($issue['resolution'] == $resolution)), false);
        rh_html_add_text($resolution);
    }
    rh_html_close();
    rh_html_up();
    rh_html_add("br");
    rh_html_add("label", true, array("for" => "allow_comments", "style" => "min-width: 150px; display: inline-block; margin-bottom: 1em"));
    rh_html_add_text("Kommentarfunktion:", true, true);
    rh_html_add("select", true, array("name" => "allow_comments", "readonly" => !has_permission(PERMISSION_ISSUE_EDIT), "disabled" => $disableother, "id" => "allow_comments", "class" => !has_permission(PERMISSION_ISSUE_EDIT) ? "rh_disabled" : false));
    rh_html_down(); 
    foreach ($allcom_acceptable as $allcom) {
        rh_html_add("option", true, array("value" => $allcom, "selected" => ($issue['allow_comments'] == $allcom)), false);
        rh_html_add_text($allcom_description[$allcom]);
    }
    rh_html_close();
    rh_html_up(2);
    rh_html_add("div", true, array("style" => "text-align: right; width: max-content; margin-left: auto; margin-top: .5em; bottom: .7em; right: .7em", "id" => "align_b"));
    rh_html_down();
    rh_html_add("fieldset", true, array("style" => "display: inline-block", "class" => "rh_delete"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Meldung löschen");
    rh_html_add("input", false, array("type" => "checkbox", "value" => "ok", "name" => "del_ok"));
    rh_html_add("input", false, array("type" => "submit", "formaction" => "postissue.php?id=" . $id . "&delete", "value" => "Löschen"));
    rh_html_up();
    rh_html_add("fieldset", true, array("style" => "display: inline-block"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Änderungen verwerfen");
    rh_html_add("input", false, array("type" => "submit", "formaction" => "redirect.php?next=listissues.php", "value" => "zurück zur Liste"));
    rh_html_up();
    rh_html_add("fieldset", true, array("style" => "display: inline-block"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Änderungen übernehmen");
    rh_html_add("input", false, array("type" => "submit", "value" => "Speichern", "disabled" => $disableother));
    rh_html_add("input", false, array("type" => "submit", "name" => "backtolist", "value" => "Speichern und zurück zur Liste", "disabled" => $disableother));
    rh_html_up(3);
}
else {
    redirect("index.php?error=invalid_issue_edit&part=issueid");
}

rh_html_end();

?>
