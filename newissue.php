<?php

require_once("lib/rh_html_parts.php");
require_once("lib/session.php");
require_once("lib/permissions.php");

rh_session();
rh_html_init();

require_loggedin_or_redirect();

global $mysql, $session;

rh_html_head("GRB: Neuen IT-Defekt melden");
rh_html_add("body", true);
rh_html_down();
rh_html_add_js(false, "rh_buttons_align.js");
rh_html_add("div", true, array("id" => "allcontainer"));
rh_html_down();
rh_html_add("h1", true, array(), false);
rh_html_add_text("GRB: Neuen IT-Defekt melden");
rh_html_close();

rh_header();

rh_html_add("h2", true, array(), false);
rh_html_down();
rh_html_add("img", false, array("src" => "img/newissue.png", "alt" => "Neuen Defekt melden", "style" => "vertical-align: bottom"));
rh_html_add_text("Neuen Defekt melden...");
rh_html_up();
rh_html_add("div", true, array("style" => "position: relative"));
rh_html_down(); // in div

if (isset($_GET['resetroom'])) $rc = 0;
else if (isset($_GET['roomid'])) {
    $room_id = (int) $_GET['roomid'];
    $res = mysqli_query($mysql, "SELECT rooms.*, classes.name AS cname FROM rooms LEFT JOIN classes ON classes.room_id = rooms.id WHERE rooms.id = " . $room_id);
    $rc = mysqli_num_rows($res);
}
else if (isset($_POST['roomid']) && isset($_POST['by_room'])) {
    $room_id = (int) $_POST['roomid'];
    $res = mysqli_query($mysql, "SELECT rooms.*, classes.name AS cname FROM rooms LEFT JOIN classes ON classes.room_id = rooms.id WHERE rooms.id = " . $room_id);
    $rc = mysqli_num_rows($res);
}
else if (isset($_POST['classroom']) && isset($_POST['by_classroom'])) {
    $classroom = $_POST['classroom'];
    $res = mysqli_query($mysql, "SELECT rooms.*, classes.name AS cname FROM classes LEFT JOIN rooms on classes.room_id = rooms.id WHERE classes.name = '" . mysqli_real_escape_string($mysql, $classroom) . "'");
    $rc = mysqli_num_rows($res);
}
else $rc = 0;
    
rh_html_add("form", true, array("action" => "newissue.php", "method" => "POST", "id" => "newissue_form"));
rh_html_down(); // in form
rh_html_add("div", true, array("style" => "width: max-content"));
rh_html_down();
if ($rc > 0) {
    $row = mysqli_fetch_assoc($res);
    rh_html_room_selector($row, "newissue.php?resetroom");
}
else rh_html_room_selector(false, "newissue.php", false);
rh_html_add("fieldset", true, array("style" => "background-color: white", "class" => ($rc == 0) ? "rh_disabled rh_issueedit" : false));
rh_html_down(); // in p
rh_html_add("legend", true);
rh_html_add_text("Nähere Angaben", true, true);
rh_html_add("label", true, array("for" => "itemid", "style" => "min-width: 300px; display: inline-block"), false);
rh_html_add_text("Defekter Gegenstand:");
rh_html_add("select", true, array("name" => "itemid", "style" => "min-width: 200px", "disabled" => ($rc == 0), "id" => "itemid")); // ech!
rh_html_down(); // in select
rh_html_add("option", true, array("value" => -1), false);
rh_html_add_text("Sonstiges");
$res = mysqli_query($mysql, "SELECT * FROM items WHERE room_id = " . (int) $row['id']);
$rc_i = mysqli_num_rows($res);
for ($i = 0; $i < $rc_i; $i++) {
    $row = mysqli_fetch_assoc($res);
    rh_html_add("option", true, array("value" => (int) $row['id'], "selected" => ($_POST['itemid'] == $row['id'])), false);
    rh_html_add_text($row['name']);
}
rh_html_close();
rh_html_up(3); // leaving select, p
rh_html_add("fieldset", true, array("style" => "background-color: white", "class" => ($rc == 0) ? "rh_disabled rh_issueedit" : false));
rh_html_down(); // in p
rh_html_add("legend", true);
rh_html_add_text("Problembeschreibung", true, true);
rh_html_add("textarea", true, array("name" => "comment", "style" => "width: 100%; min-height: 400px", "disabled" => ($rc == 0), "id" => "newissue_textarea"), false);
rh_html_add_text(isset($_POST['comment']) ? $_POST['comment'] : "");
rh_html_close();
rh_html_up(); // leaving p
rh_html_add("fieldset", true, array("class" => ($rc == 0) ? "rh_disabled rh_issueedit" : false, "style" => "width: max-content; background-color: white", "id" => "align_a"));
rh_html_down(); // in div
rh_html_add("legend", true);
rh_html_add_text("Schweregrad", true, true);
rh_html_add("ul", true, array("class" => "bare"));
rh_html_down(); // in ul
rh_html_add("li", true, array("class" => "bare"));
rh_html_down(); // in li
rh_html_add("input", false, array("type" => "radio", "name" => "severity", "value" => "critical", "checked" => ($_POST['severity'] == "critical"), "disabled" => ($rc == 0), "id" => "radio_crit"));
rh_html_add("label", true, array("for" => "radio_crit"));
rh_html_down();
rh_html_add("img", false, array("src" => "img/sev_critical.png", "alt" => "schwerwiegend", "style" => "vertical-align: middle"));
rh_html_add("span", true, array("style" => "font-style: italic; font-weight: bold"), false);
rh_html_add_text("schwerwiegend");
rh_html_close(false, false, false, false);
rh_html_add_text(" - Unterricht ist durch den Defekt praktisch nicht möglich", false, true);
rh_html_up(2); // leaving li
rh_html_add("li", true, array("class" => "bare"));
rh_html_down(); // in li
rh_html_add("input", false, array("type" => "radio", "name" => "severity", "value" => "high", "checked" => ($_POST['severity'] == "high"), "disabled" => ($rc == 0), "id" => "radio_high"));
rh_html_add("label", true, array("for" => "radio_high"));
rh_html_down();
rh_html_add("img", false, array("src" => "img/sev_high.png", "alt" => "hoch", "style" => "vertical-align: middle"));
rh_html_add("span", true, array("style" => "font-style: italic; font-weight: bold"), false);
rh_html_add_text("hoch");
rh_html_close(false, false, false, false);
rh_html_add_text(" - Unterricht wird durch den Defekt stark beeinflusst", false, true);
rh_html_up(2); // leaving li
rh_html_add("li", true, array("class" => "bare"));
rh_html_down(); // in li
rh_html_add("input", false, array("type" => "radio", "name" => "severity", "value" => "normal", "checked" => ($_POST['severity'] == "normal"), "disabled" => ($rc == 0), "id" => "radio_normal"));
rh_html_add("label", true, array("for" => "radio_normal"));
rh_html_down();
rh_html_add("img", false, array("src" => "img/sev_normal.png", "alt" => "mittel", "style" => "vertical-align: middle"));
rh_html_add("span", true, array("style" => "font-style: italic; font-weight: bold"), false);
rh_html_add_text("mittel");
rh_html_close(false, false, false, false);
rh_html_add_text(" - einige Unterrichtsmethoden werden beeinträchtigt", false, true);
rh_html_up(2); // leaving li
rh_html_add("li", true, array("class" => "bare"));
rh_html_down(); // in li
rh_html_add("input", false, array("type" => "radio", "name" => "severity", "value" => "low", "checked" => ($_POST['severity'] == "low"), "disabled" => ($rc == 0), "id" => "radio_low"));
rh_html_add("label", true, array("for" => "radio_low"));
rh_html_down();
rh_html_add("img", false, array("src" => "img/sev_low.png", "alt" => "niedrig", "style" => "vertical-align: middle"));
rh_html_add("span", true, array("style" => "font-style: italic; font-weight: bold"), false);
rh_html_add_text("niedrig");
rh_html_close(false, false, false, false);
rh_html_add_text(" - Unterricht wird kaum beeinflusst", false, true);
rh_html_up(4); // leaving li, ul, div
rh_html_add("fieldset", true, array("style" => "text-align: right; width: max-content; margin-left: auto; margin-top: .5em; bottom: 0px; right: 0px; background-color: white", "class" => ($rc == 0) ? "rh_disabled" : false, "id" => "align_b"));
rh_html_down();
rh_html_add("legend", true, array(), false);
rh_html_add_text("Abschicken");
rh_html_add("input", false, array("type" => "submit", "formaction" => "postissue.php", "value" => "Defekt melden", "style" => "margin-left: 2px; margin-top: 1em", "disabled" => ($rc == 0)));
rh_html_up();

// This is not very good. But it works...

$form_verify_js = <<<EOD
document.getElementById("newissue_form").onsubmit = rh_verify_newissue_form;

function rh_verify_newissue_form() {
    if (document.activeElement.getAttribute("formaction").indexOf("resetroom") > -1) return true;
    
    var submit = true;
    
    if (document.getElementById("newissue_textarea").value == "") {
        document.getElementById("newissue_textarea").parentNode.style['background-color'] = "#ff4a1d";
        document.getElementById("newissue_textarea").oninput = function() { document.getElementById("newissue_textarea").parentNode.style['background-color'] = ""; };
        submit = false;
    }
    if (!document.getElementById("radio_crit").checked && !document.getElementById("radio_high").checked && !document.getElementById("radio_normal").checked && !document.getElementById("radio_low").checked) {
        document.getElementById("align_a").style['background-color'] = "#ff4a1d";
        document.getElementById("radio_crit").onchange = document.getElementById("radio_high").onchange = document.getElementById("radio_normal").onchange = document.getElementById("radio_low").onchange = function() {
            document.getElementById("align_a").style['background-color'] = "";
        }
        submit = false;
    }
    if (!submit) alert("Bitte alle benötigten Felder ausfüllen!");

    return submit;
}
EOD;

if ($rc != 0) rh_html_add_js($form_verify_js);

rh_html_end();

?>
