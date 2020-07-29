<?php

require_once("lib/rh_html_parts.php");
require_once("lib/session.php");
require_once("lib/permissions.php");

rh_session();
rh_html_init();

require_loggedin_or_redirect();

global $mysql, $session;

rh_html_doctype("html", true);
rh_html_add("html", true, array("lang" => "de"));
rh_html_down();
rh_html_add("head", true);
rh_html_down();
rh_html_add("meta", false, array("charset" => "utf-8"));
rh_html_add("meta", false, array("name" => "description", "content" => "GRB Room Health"));
rh_html_add("meta", false, array("name" => "keywords", "content" => "Fehlermeldung, IT-Defekte"));
rh_html_add("title", true, array(), false);
rh_html_add_text("GRB Room Health");
rh_html_close();
rh_html_up();
rh_html_add("body", true);
rh_html_down();
rh_html_add("h1", true, array(), false);
rh_html_add_text("GRB Raumstatus");
rh_html_close();

rh_loginform();

rh_html_add("h2", true, array(), false);
rh_html_add_text("Neuen Defekt melden");
rh_html_add("div", true);
rh_html_down(); // in div

if (isset($_GET['resetroom'])) $rc = 0;
else if (isset($_GET['roomid'])) {
    $room_id = (int) $_GET['roomid'];
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE id = " . $room_id);
    $rc = mysqli_num_rows($res);
}
else if (isset($_POST['roomid'])) {
    $room_id = (int) $_POST['roomid'];
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE id = " . $room_id);
    $rc = mysqli_num_rows($res);
}
else if (isset($_GET['classroom'])) {
    $classroom = $_GET['classroom'];
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE class = '" . mysqli_real_escape_string($mysql, $classroom) . "'");
    $rc = mysqli_num_rows($res);
}
else $rc = 0;

rh_html_add("form", true, array("action" => "newissue.php", "method" => "POST"));
rh_html_down(); // in form

if ($rc == 0) rh_html_room_selector(false, "newissue.php");
else {
    $row = mysqli_fetch_assoc($res);
    rh_html_room_selector($row, "newissue.php?resetroom");
    //rh_html_add("input", false, array("type" => "hidden", "name" => "roomid", "value" => (int)$row['id']));
    rh_html_add("p", true);
    rh_html_down(); // in p
    rh_html_add_text("Defekter Gegenstand: ", true, true);
    rh_html_add("select", true, array("name" => "itemid"));
    rh_html_down(); // in select
    $res = mysqli_query($mysql, "SELECT * FROM items WHERE room_id = " . (int) $row['id']);
    $rc = mysqli_num_rows($res);
    for ($i = 0; $i < $rc; $i++) {
        $row = mysqli_fetch_assoc($res);
        rh_html_add("option", true, array("value" => (int) $row['id']), false);
        rh_html_add_text($row['name']);
    }
    rh_html_add("option", true, array("value" => -1), false);
    rh_html_add_text("Sonstiges");
    rh_html_close();
    rh_html_up(2); // leaving select, p
    rh_html_add("p", true);
    rh_html_down(); // in p
    rh_html_add_text("Nähere Angaben: ", true, true);
    rh_html_add("textarea", true, array("name" => "comment"), false);
    rh_html_add_text($_POST['comment']);
    rh_html_close();
    rh_html_up(); // leaving p
    rh_html_add("div", true);
    rh_html_down(); // in div
    rh_html_add_text("Schweregrad: ", true, true);
    rh_html_add("ul", true);
    rh_html_down(); // in ul
    rh_html_add("li", true);
    rh_html_down(); // in li
    rh_html_add("input", false, array("type" => "radio", "name" => "severity", "value" => "critical", "checked" => ($_POST['severity'] == "critical")), false);
    rh_html_add_text("schwerwiegend - Unterricht ist durch den Defekt praktisch nicht möglich", false, true);
    rh_html_up(); // leaving li
    rh_html_add("li", true);
    rh_html_down(); // in li
    rh_html_add("input", false, array("type" => "radio", "name" => "severity", "value" => "high", "checked" => ($_POST['severity'] == "high")), false);
    rh_html_add_text("hoch - Unterricht wird durch den Defekt stark beeinflusst", false, true);
    rh_html_up(); // leaving li
    rh_html_add("li", true);
    rh_html_down(); // in li
    rh_html_add("input", false, array("type" => "radio", "name" => "severity", "value" => "normal", "checked" => ($_POST['severity'] == "normal")), false);
    rh_html_add_text("mittel - einige Unterrichtsmethoden werden beeinträchtigt", false, true);
    rh_html_up(); // leaving li
    rh_html_add("li", true);
    rh_html_down(); // in li
    rh_html_add("input", false, array("type" => "radio", "name" => "severity", "value" => "low", "checked" => ($_POST['severity'] == "low")), false, true);
    rh_html_add_text("niedrig - Unterricht wird kaum beeinflusst", false, true);
    rh_html_up(3); // leaving li, ul, div
    rh_html_add("input", false, array("type" => "submit", "formaction" => "postissue.php", "value" => "Defekt melden"));
}
rh_html_up(999);

?>
