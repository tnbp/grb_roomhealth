<?php

require_once("lib/rh_html_parts.php");
require_once("lib/permissions.php");

function rh_comment_section(&$issue) {
    global $mysql, $session;
    $res = mysqli_query($mysql, "SELECT comments.*,users.name FROM comments LEFT JOIN users ON comments.user_id = users.id WHERE issue_id = " . $issue['id'] . " ORDER BY id DESC");
    rh_html_add_js(false, "rh_collapsible_commentmod.js");
    rh_html_add("div", true, array("style" => "background-color: #f7f7ff; margin-left: 2em; position: relative; margin-top: 3.5em"));
    rh_html_down();
    //rh_html_add("hr");
    rh_html_add("h3", true, array(), false);
    rh_html_down();
    rh_html_add("img", false, array("src" => "img/comments.png", "alt" => "Kommentare", "style" => "vertical-align: middle"));
    rh_html_add_text("Kommentare:");
    rh_html_up();
    if (!mysqli_num_rows($res)) {
        rh_html_add("p", true, array("style" => "font-style: italic; padding-left: 1em"), false);
        rh_html_down();
        rh_html_add("img", false, array("src" => "img/info.png", "alt" => "Information", "style" => "vertical-align: middle"));
        rh_html_add_text("keine Kommentare...");
        rh_html_up();
    }
    else {
        while (($comment = mysqli_fetch_assoc($res)) !== NULL) {
            rh_display_comment($comment);
        }
    }
    if (can_post_comment($issue)) rh_comment_form($issue);
    rh_html_up();
}

function rh_display_comment($comment) {
    global $mysql, $session;
    rh_html_add("fieldset", true, array("class" => $comment['user_id'] == 0 ? "rh_systemnotice" : "rh_comment"));
    rh_html_down();
    if (isset($_GET['error'])) {
        $errorbox_style = array("style" => "border: 2px solid red; background-color: #ffa0a0");
        if ($_GET['error'] == "checkbox" && $_GET['cid'] == $comment['id']) rh_html_box("<span style=\"font-weight: bold\">FEHLER: </span>Zum Bestätigung der Löschung bitte Checkbox anklicken!", $errorbox_style);
    }
    if (can_see_comment($comment)) {
        rh_html_add("legend", true);
        rh_html_down();
        if ($comment['user_id'] == 0) {
            rh_html_add("span", false, array("style" => "font-weight: bold; font-variant: small-caps"), false);
            rh_html_add_text("Systemnachricht");
            rh_html_close("span", false, false, false);  // this should not be necessary, but alas...
            rh_html_indent_on_next(false);
            rh_html_add("span", false, array("style" => "font-weight: bold"), false);
            rh_html_add_text(", ");
            rh_html_close("span", false, false, false);
            rh_html_indent_on_next(false);
            rh_html_add("span", false, array("style" => "font-weight: bold"), false);
            rh_html_add_text("Datum ");
            rh_html_close("span", false, false, false);
            rh_html_indent_on_next(false);
            rh_html_add("span", false, array("style" => "font-weight: normal"), false);
            rh_html_add_text(date("Y-m-d H:i:s", $comment['timestamp']));
            rh_html_close("span", false, false, false);
            rh_html_indent_on_next(false);
        }
        else {
            rh_html_add("span", false, array("style" => "font-weight: bold"), false);
            rh_html_add_text("Kommentar #");
            rh_html_close("span", false, false, false);  // this should not be necessary, but alas...
            rh_html_indent_on_next(false);
            rh_html_add("span", false, array("style" => "font-weight: normal"), false);
            rh_html_add_text($comment['id']);
            rh_html_close("span", false, false, false);
            rh_html_indent_on_next(false);
            rh_html_add("span", false, array("style" => "font-weight: bold;"), false);
            rh_html_add_text(" von ");
            rh_html_close("span", false, false, false);
            rh_html_indent_on_next(false);
            rh_html_add("span", false, array("style" => "font-weight: normal"), false);
            rh_html_add_text($comment['name']);
            rh_html_close("span", false, false, false);
            rh_html_indent_on_next(false);
            rh_html_add("span", false, array("style" => "font-weight: bold;"), false);
            rh_html_add_text(", Datum ");
            rh_html_close("span", false, false, false);
            rh_html_indent_on_next(false);
            rh_html_add("span", false, array("style" => "font-weight: normal"), false);
            rh_html_add_text(date("Y-m-d H:i:s", $comment['timestamp']), false, false);
            rh_html_close("span", false, false, true);
        }
        rh_html_up();
        rh_comment_mod_form($comment);
        $comment['body'] = nl2br(htmlentities($comment['body'], ENT_QUOTES | ENT_HTML5));
	// markdown for all, but special markdown only for system messages
        $comment['body'] = rh_markdown($comment['body'], ($comment['user_id'] == 0));
        rh_html_box($comment['body']);
    }
    else {
        rh_html_add("legend", true, array("style" => "font-style: italic; margin-left: auto; margin-right: auto"), false);
        rh_html_add_text("Kommentar ausgeblendet");
    }
    rh_html_up();
}

function rh_comment_form(&$issue) {
    require("include/acceptable.php");
    if (!can_post_comment($issue)) return;
    rh_html_add("fieldset", true, array("style" => "position: relative"));
    rh_html_down();
    rh_html_add("legend", true, array("style" => "font-size: 16px; font-weight: bold"), false);
    rh_html_down();
    rh_html_add("img", false, array("src" => "img/comment_new.png", "alt" => "Kommentar hinzufügen", "style" => "vertical-align: middle"));
    rh_html_add_text("Kommentar hinzufügen:");
    rh_html_up();
    rh_html_add("form", true, array("action" => "postcomment.php?issue=" . $issue['id'], "method" => "POST", "id" => "comments_form"));
    rh_html_down();
    rh_html_add("fieldset", true, array("style" => "background-color: white"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Kommentartext");
    rh_html_add("textarea", false, array("name" => "body", "style" => "width: 100%; min-height: 200px", "id" => "comments_textarea"), false); // this is shit!
    rh_html_add_text("", false, false);
    rh_html_close("textarea");
    rh_html_up();
    rh_html_add("fieldset", true, array("style" => "width: max-content; background-color: white", "class" => "align_a"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Kommentarsichtbarkeit");
    rh_html_add("img", false, array("src" => "img/visibility.png", "alt" => "Sichtbarkeit", "style" => "vertical-align: middle"));
    rh_html_add("label", true, array("for" => "visible", "style" => "margin-right: 3em; display: inline-block"), false);
    rh_html_add_text("sichtbar machen für:");
    rh_html_add("select", true, array("name" => "visible", "id" => "visible"));
    rh_html_down();
    foreach ($comvis_acceptable as $vis) {
        if ($vis == "mods" && !has_permission(PERMISSION_LEVEL_MOD)) continue;
        if ($vis == "none" && !has_permission(PERMISSION_LEVEL_ADMIN)) continue; // do not allow these for users; that makes no sense
        rh_html_add("option", true, array("value" => $vis, "selected" => ($comment['visible'] == $vis)), false);
        rh_html_add_text($comvis_description[$vis]);
        rh_html_close();
    }
    rh_html_up(2);
    rh_html_add("fieldset", true, array("style" => "text-align: right; width: max-content; margin-left: auto; bottom: .64em; right: .64em; background-color: white", "class" => "align_b"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Kommentar hinzufügen");
    rh_html_add("input", false, array("value" => "Abschicken", "type" => "submit"));
    rh_html_up(2);
    $form_verify_js = <<<EOD
document.getElementById("comments_form").onsubmit = rh_verify_comments_form;

function rh_verify_comments_form() {    
    var submit = true;
    
    if (document.getElementById("comments_textarea").value == "") {
        document.getElementById("comments_textarea").parentNode.style['background-color'] = "#ff4a1d";
        document.getElementById("comments_textarea").oninput = function() { document.getElementById("comments_textarea").parentNode.style['background-color'] = ""; };
        submit = false;
    }
    if (!submit) alert("Bitte geben Sie einen Kommentartext ein!");

    return submit;
}
EOD;
    rh_html_add_js($form_verify_js);
    rh_html_up();
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
    rh_html_add("fieldset", true, array("class" => "rh_commentmod", "id" => "commentmod_" . $comment['id']));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Kommentarmoderation");
    rh_html_add("fieldset", true, array("style" => "display: inline-block"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Sichtbarkeit");
    rh_html_add("label", true, array("for" => "visible_" . $comment['id']));
    rh_html_add_text("sichtbar für: ", true, true);
    rh_html_add("select", true, array("name" => "visible", "id" => "visible_" . $comment['id']));
    rh_html_down();
    foreach ($comvis_acceptable as $vis) {
        rh_html_add("option", true, array("value" => $vis, "selected" => ($comment['visible'] == $vis)), false);
        rh_html_add_text($comvis_description[$vis]);
        rh_html_close();
    }
    rh_html_up();
    rh_html_add("input", false, array("type" => "submit", "value" => "Sichtbarkeit ändern"));
    rh_html_up();
    rh_html_add("fieldset", true, array("style" => "display: inline-block", "class" => "rh_delete"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Kommentar löschen");
    rh_html_add("input", false, array("name" => "del_ok", "value" => "ok", "type" => "checkbox"));
    rh_html_add("input", false, array("value" => "Löschen", "formaction" => "postcomment.php?id=" . $comment['id'] . "&del", "type" => "submit"));
    rh_html_up(3);
}

function rh_markdown(&$body, $special = false) {
    if ($special) {
        $body = preg_replace('/&ast;&ast;\s?(.*)\s?&ast;&ast;/U', '<span style="font-weight: bold">$1</span>', $body);
    }
    $body = preg_replace('/issue&num;(\d+)&num;/U', '<a href="showissue.php?id=$1">Defekt &num;$1</a>', $body);
    return $body;
}

?>
