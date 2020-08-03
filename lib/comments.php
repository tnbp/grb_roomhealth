<?php

require_once("lib/rh_html_parts.php");
require_once("lib/permissions.php");

function rh_comment_section(&$issue) {
    global $mysql, $session;
    $res = mysqli_query($mysql, "SELECT comments.*,users.name FROM comments LEFT JOIN users ON comments.user_id = users.id WHERE issue_id = " . $issue['id'] . " ORDER BY id DESC");
    rh_html_add("div", true, array("style" => "background-color: white; margin-left: 2em;"));
    rh_html_down();
    rh_html_add("hr");
    rh_html_add("h3", true, array(), false);
    rh_html_add_text("Kommentare:");
    if (!mysqli_num_rows($res)) {
        rh_html_add("p", true, array("style" => "font-style: italic"), false);
        rh_html_add_text("keine Kommentare...");
    }
    else {
        while (($comment = mysqli_fetch_assoc($res)) !== NULL) {
            rh_display_comment($comment);
        }
    }
    if (can_post_comment($issue)) {
        rh_html_add("hr");
        rh_comment_form($issue);
    }
    rh_html_add("hr");
    rh_html_up();
}

function rh_display_comment($comment) {
    global $mysql, $session;
    rh_html_add("div", true, array("style" => "border: 1px solid black"));
    rh_html_down();
    if (isset($_GET['error'])) {
        $errorbox_style = array("style" => "border: 2px solid red; background-color: #ffa0a0");
        if ($_GET['error'] == "checkbox" && $_GET['cid'] == $comment['id']) rh_html_box("<span style=\"font-weight: bold\">FEHLER: </span>Zum Bestätigung der Löschung bitte Checkbox anklicken!", $errorbox_style);
    }
    rh_comment_mod_form($comment);
    if (can_see_comment($comment)) {
        rh_html_add("p", true);
        rh_html_down();
        rh_html_add("span", true, array("style" => "font-weight: bold;"), false);
        rh_html_add_text("von: ");
        rh_html_close(false, false, false, false);
        rh_html_add_text($comment['name'] . " | ");
        rh_html_add("span", true, array("style" => "font-weight: bold;"), false);
        rh_html_add_text("Datum: ");
        rh_html_close(false, false, false, false);
        rh_html_add_text(date("Y-m-d H:i:s", $comment['timestamp']), false, true);
        rh_html_up();
        rh_html_box(htmlentities($comment['body'], ENT_QUOTES | ENT_HTML5));
    }
    else {
        rh_html_add("p", true, array("style" => "text-align: center; font-style: italic"));
        rh_html_down();
        rh_html_add_text("Kommentar ausgeblendet");
        rh_html_up();
    }
    rh_html_up();
}

function rh_comment_form(&$issue) {
    require("include/acceptable.php");
    if (!can_post_comment($issue)) return;
    rh_html_add("h3", true, array(), false);
    rh_html_add_text("Kommentar hinzufügen:");
    rh_html_add("div", true);
    rh_html_down();
    rh_html_add("form", true, array("action" => "postcomment.php?issue=" . $issue['id'], "method" => "POST"));
    rh_html_down();
    rh_html_add("textarea", true, array("name" => "body"), false);
    rh_html_add_text("", false, false);
    rh_html_add("p", true);
    rh_html_down();
    rh_html_add_text("sichtbar machen für: ", true, true);
    rh_html_add("select", true, array("name" => "visible"));
    rh_html_down();
    foreach ($comvis_acceptable as $vis) {
        rh_html_add("option", true, array("value" => $vis, "selected" => ($comment['visible'] == $vis)), false);
        rh_html_add_text($comvis_description[$vis]);
        rh_html_close();
    }
    rh_html_up();
    rh_html_add("input", false, array("value" => "Kommentar hinzufügen", "type" => "submit"));
    rh_html_up(3);
}

function can_see_comment(&$comment) {
    global $session;
    if (has_permission(PERMISSION_LEVEL_ADMIN)) return true;
    if ($comment['visible'] == "all") return true;
    if ($comment['visible'] == "loggedin") return is_loggedin();
    if ($comment['visible'] == "author") return ($session['userid'] == $comment['user_id'] || has_permission(PERMISSION_LEVEL_MOD));
    if ($comment['visible'] == "mods") return has_permission(PERMISSION_LEVEL_MOD);
    return false;
}

function can_post_comment(&$issue) {
    global $session;
    if (!is_loggedin()) return false;
    if ($issue['allow_comments'] == "all") return true;
    if ($issue['allow_comments'] == "author") return ($session['userid'] == $issue['reporter_id'] || has_permission(PERMISSION_COMMENT_ALWAYS));
    if ($issue['allow_comments'] == "mod") return has_permission(PERMISSION_LEVEL_MOD);
    if ($issue['allow_comments'] == "admin") return has_permission(PERMISSION_LEVEL_ADMIN);
    return false;
}

function rh_comment_mod_form(&$comment) {
    require("include/acceptable.php");
    if ($comment['visibile'] == "none" && !has_permission(PERMISSION_LEVEL_ADMIN)) return;
    if (!has_permission(PERMISSION_COMMENT_EDIT)) return;
    rh_html_add("form", true, array("action" => "postcomment.php?id=" . $comment['id'], "method" => "POST"));
    rh_html_down();
    rh_html_add("p", true);
    rh_html_down();
    rh_html_add_text("sichtbar für: ", true, true);
    rh_html_add("select", true, array("name" => "visible"));
    rh_html_down();
    foreach ($comvis_acceptable as $vis) {
        rh_html_add("option", true, array("value" => $vis, "selected" => ($comment['visible'] == $vis)), false);
        rh_html_add_text($comvis_description[$vis]);
        rh_html_close();
    }
    rh_html_up();
    rh_html_add("input", false, array("type" => "submit", "value" => "Sichtbarkeit ändern"));
    rh_html_add_text("Kommentar löschen: ", true, true);
    rh_html_add("input", false, array("name" => "del_ok", "value" => "ok", "type" => "checkbox"));
    rh_html_add("input", false, array("value" => "Kommentar löschen", "formaction" => "postcomment.php?id=" . $comment['id'] . "&del", "type" => "submit"));
    rh_html_up(2);
}

?>
