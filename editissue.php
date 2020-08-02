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

$issue = mysqli_fetch_assoc($res);
if ($issue !== NULL) {
    $disableother = false;
    if (isset($_GET['resetroom'])) $disableother = true;
    $item_id = $issue['item_id'];
    $res = mysqli_query($mysql, "SELECT * FROM items WHERE id = " . $item_id);
    $item = mysqli_fetch_assoc($res);
    $room_id = $issue['room_id'];
    if ($room_id == -1) $room_id = $item['room_id'];
    if (isset($_GET['newroom'])) {
        $room_id = (int) $_POST['roomid'];
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
    rh_html_add_text("Problem mit Gerät:  " . $item['name'] . " in Raum: " . $room['name']);
    if (isset($_GET['error'])) {
        $errorbox_style = array("style" => "border: 2px solid red; background-color: #ffa0a0");
        if ($_GET['error'] == "nochange") rh_html_box("<span style=\"font-weight: bold\">FEHLER: </span>Keine &Auml;nderungen vorgenommen.", $errorbox_style);
    }
    rh_html_add("hr");
    rh_html_add("div", true);
    rh_html_down(); // in div
    rh_html_add("form", true, array("method" => "POST", "action" => "postissue.php?update"));
    rh_html_down(); // in form
    rh_html_add("input", false, array("name" => "issueid", "value" => $id, "type" => "hidden"));
    rh_html_add("p", true);
    rh_html_down(); // in p
    rh_html_add_text("gemeldet: ", true);
    rh_html_add("input", false, array("name" => "_reported", "value" => date("Y-m-d H:i:s", $issue['time_reported']), "readonly" => true));
    rh_html_up(); // leaving p
    rh_html_add("p", true);
    rh_html_down(); // in p
    rh_html_add_text("von: ", true);
    rh_html_add("input", false, array("name" => "reporter", "value" => $reporter, "readonly" => true));
    rh_html_up(); // leaving p
    rh_html_add("p", true);
    rh_html_down(); // in p
    rh_html_add_text("Beschreibung: ", true, true);
    rh_html_add("textarea", true, array("name" => "comment", "readonly" => !has_permission(PERMISSION_ISSUE_EDIT), "disabled" => $disableother), false);
    rh_html_add_text($issue['comment'], false, false);
    rh_html_close();
    rh_html_up(); // leaving textarea, p
    rh_html_add("p", true);
    rh_html_down(); // in p
    if (isset($_GET['resetroom'])) {
        rh_html_room_selector(false, "editissue.php?id=" . $id . "&newroom");
    }
    else rh_html_room_selector($room, "editissue.php?id=" . $id . "&resetroom");
    rh_html_up(); // leaving p
    rh_html_add("p", true);
    rh_html_down(); // in p
    rh_html_add_text("Gegenstand: ", true, true);
    rh_html_add("select", true, array("name" => "itemid", "readonly" => !has_permission(PERMISSION_ISSUE_EDIT), "disabled" => $disableother));
    rh_html_down(); // in select
    $res = mysqli_query($mysql, "SELECT * FROM items WHERE room_id = " . $room_id);
    while (($row = mysqli_fetch_assoc($res)) !== NULL) {
        rh_html_add("option", true, array("value" => $row['id'], "selected" => ($row['id'] == $item_id)), false);
        rh_html_add_text($row['name']);
    }
    rh_html_add("option", true, array("value" => -1, "selected" => ($item_id == -1)), false);
    rh_html_add_text("Sonstiges");
    rh_html_close();
    rh_html_up(2); // leaving select, p
    rh_html_add("p", true);
    rh_html_down(); // in p
    rh_html_add_text("Dringlichkeit: ", true, true);
    rh_html_add("select", true, array("name" => "severity", "readonly" => !has_permission(PERMISSION_ISSUE_SET_SEVERITY), "disabled" => $disableother));
    rh_html_down(); // in select
    foreach ($severity_acceptable as $sev) {
        rh_html_add("option", true, array("value" => $sev, "selected" => ($issue['severity'] == $sev)), false);
        rh_html_add_text($sev);
    }
    rh_html_close();
    rh_html_up(2); // leaving select, p
    rh_html_add("p", true);
    rh_html_down(); // in p
    rh_html_add_text("zugewiesen: ", true, true);
    rh_html_add("select", true, array("name" => "assignee_id", "readonly" => !has_permission(PERMISSION_ISSUE_ASSIGN_SELF | PERMISSION_ISSUE_ASSIGN), "disabled" => $disableother));
    rh_html_down(); // in select
    $res = mysqli_query($mysql, "SELECT * FROM users WHERE permissions & " . PERMISSION_ISSUE_ASSIGNABLE);
    while (($row = mysqli_fetch_assoc($res)) !== NULL) {
        rh_html_add("option", true, array("value" => $row['id'], "selected" => ($row['id'] == $issue['assignee_id'])), false);
        rh_html_add_text($row['name']);
    }
    rh_html_add("option", true, array("value" => -1, "selected" => ($issue['assignee_id'] == -1)), false);
    rh_html_add_text("niemand");
    rh_html_close();
    rh_html_up(2); // leaving select, p
    rh_html_add("p", true);
    rh_html_down(); // in p
    rh_html_add_text("Status: ", true, true);
    rh_html_add("input", false, array("type" => "radio", "name" => "status", "value" => "OPEN", "checked" => $issue['status'] == "OPEN"), false);
    rh_html_add_text("offen");
    rh_html_add("input", false, array("type" => "radio", "name" => "status", "value" => "CLOSED", "checked" => $issue['status'] == "CLOSED"), false);
    rh_html_add_text("geschlossen", false, true);
    rh_html_up(); // leaving p
    rh_html_add("p", true);
    rh_html_down(); // in p
    rh_html_add_text("Lösung: ", true, true);
    rh_html_add("select", true, array("name" => "resolution", "readonly" => !has_permission(PERMISSION_ISSUE_SET_RESOLUTION), "disabled" => $disableother));
    rh_html_down(); // in select
    foreach ($resolution_acceptable as $resolution) {
        rh_html_add("option", true, array("value" => $resolution, "selected" => ($issue['resolution'] == $resolution)), false);
        rh_html_add_text($resolution);
    }
    rh_html_close();
    rh_html_up(2); // leaving select, p
    rh_html_add("p", true);
    rh_html_down(); // in p
    rh_html_add_text("Kommentarfunktion: ", true, true);
    rh_html_add("select", true, array("name" => "allow_comments", "readonly" => !has_permission(PERMISSION_ISSUE_EDIT), "disabled" => $disableother));
    rh_html_down(); // in select
    foreach ($allcom_acceptable as $allcom) {
        rh_html_add("option", true, array("value" => $allcom, "selected" => ($issue['allow_comments'] == $allcom)), false);
        rh_html_add_text($allcom_description[$allcom]);
    }
    rh_html_close();
    rh_html_up(2); // leaving select, p
    rh_html_add("p", true, array("style" => "text-align: right"));
    rh_html_down(); // in p
    rh_html_add("input", false, array("type" => "submit", "value" => "Ändern", "disabled" => $disableother));
    rh_html_add("span", true, array("style" => "margin-left: 10em;"));
    rh_html_down();
    rh_html_add("input", false, array("type" => "checkbox", "value" => "ok", "name" => "del_ok"));
    rh_html_add("input", false, array("type" => "submit", "formaction" => "postissue.php?id=" . $id . "&delete", "value" => "Defekt löschen"));
    rh_html_up();
}
else {
    redirect("index.php?error=invalid_issue_edit&part=issueid");
}

rh_html_up(999);

?>
