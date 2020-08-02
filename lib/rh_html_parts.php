<?php

require_once("lib/rh_html.php");

function rh_html_table($header, $data, $tableattr = array(), $tdattr = array(), $trattr = array()) {
    if (isset($header['tr_attr'])) {
        $header_tr_attr = $header['tr_attr'];
        unset($header['tr_attr']);
    }
    else $header_tr_attr = array();
    if (isset($header['th_attr'])) {
        $header_th_attr = $header['th_attr'];
        unset($header['th_attr']);
    }
    else $header_th_attr = array();
    
    rh_html_add("table", true, $tableattr);
    rh_html_down();
    rh_html_add("tr", true, $header_tr_attr);
    rh_html_down();
    foreach ($header as $h) {
        rh_html_add("th", true, $header_th_attr, false);
        rh_html_add_raw($h, false);
    }
    rh_html_close();
    rh_html_up();
    
    if (!count($data)) {
        rh_html_add("tr", true, $trattr);
        rh_html_down();
        $tdattr['colspan'] = "10";
        $tdattr['style'] = "text-align: center; border: 1px solid black; padding: 2px 2px; font-style: italic";
        rh_html_add("td", true, $tdattr, false);
        rh_html_add_text("Es gibt keine anzuzeigenden Daten.");
        rh_html_close();
        rh_html_up();
    }
    else {
        foreach ($data as $d) {
            rh_html_add("tr", true, $trattr);
            rh_html_down();
            foreach ($d as $f) {
                rh_html_add("td", true, $tdattr, false);
                rh_html_add_raw($f, false);
            }
            rh_html_close();
            rh_html_up();
        }
    }
    rh_html_up();
}

function rh_html_table_sql_prepare($query, $order = false) {
    global $mysql;
    $ret = array();
    
    $res = mysqli_query($query, $mysql);
    if ($res === false) return false;
    $rc = mysqli_num_rows($res);
    for ($i = 0; $i < $rc; $i++) {
        if ($order === false) $ret[] = mysqli_fetch_row();
        else if (is_array($order)) {
            $next = array();
            $row = mysqli_fetch_assoc($res);
            foreach ($order as $cur) $next[] = $row[$cur];
            $ret[] = $next;
        }
        else return false;
    }
    return $ret;
}

function rh_loginform($nexturi = false) {
	if ($nexturi === false) $nexturi = $_SERVER['REQUEST_URI'];
	global $session, $mysql;
	if ($session['loggedin'] === true) {
		rh_html_add("div", true);
		rh_html_down();
		rh_html_add_text("Eingeloggt als ", true);
		rh_html_add("span", true, array("style" => "font-weight: bold"), false);
		rh_html_add_text($session['name']);
		rh_html_close();
		rh_html_add_text(" (", true);
		rh_html_add("a", true, array("href" => "logout.php?next=" . urlencode($nexturi)), false);
		rh_html_add_text("Ausloggen");
		rh_html_close(false, false, false, false);
		rh_html_add_text(")", false, true);
		rh_html_up();
    }
	else {
		if ($_GET['error'] == "login") {
            rh_html_add("p", true, array("style" => "color: red; font-weight: bold"), false);
            rh_html_add_text("Login fehlgeschlagen!");
            rh_html_close();
        }
		rh_html_add("form", true, array("action" => "login.php", "method" => "POST"));
		rh_html_down();
		rh_html_add("p", true);
		rh_html_down();
		rh_html_add("input", false, array("name" => "login"), false);
		rh_html_add_text(" Passwort: ");
		rh_html_add("input", false, array("type" => "password", "name" => "pwd"), false);
		rh_html_add("input", false, array("type" => "submit", "value" => "Login"), false);
		rh_html_add("input", false, array("type" => "hidden", "name" => "nexturi", "value" => urlencode($nexturi)));
		rh_html_up(2);
	}
}

function rh_html_box($content, $attr = array()) {
    rh_html_add("div", true, $attr);
    rh_html_down();
    rh_html_add_raw($content, true);
    rh_html_up();
}

function rh_html_head($title, $description = false, $keywords = false, $additional_tags = array()) {
    rh_html_doctype("html", true);
    rh_html_add("html", true, array("lang" => "de"));
    rh_html_down();
    rh_html_add("head", true);
    rh_html_down();
    rh_html_add("meta", false, array("charset" => "utf-8"));
    if ($description !== false) rh_html_add("meta", false, array("name" => "description", "content" => $description));
    if ($keywords !== false) rh_html_add("meta", false, array("name" => "keywords", "content" => $keywords));
    foreach ($additional_tags as $tag) rh_html_add($tag['name'], $tag['needsclosing'], $tag['attributes']);
    rh_html_add("title", true, array(), false);
    rh_html_add_text($title);
    rh_html_close();
    rh_html_up();
}

function rh_html_room_selector($room, $formaction) {
    global $mysql, $session;
    
    rh_html_add_text("Raum: ", true, true);
    if ($room === false) {
        $res = mysqli_query($mysql, "SELECT * FROM rooms ORDER BY name ASC");
        $rc = mysqli_num_rows($res);
        rh_html_add("select", true, array("name" => "roomid"));
        rh_html_down();
        for ($i = 0; $i < $rc; $i++) {
            $row = mysqli_fetch_assoc($res);
            rh_html_add("option", true, array("value" => (int)$row['id']), false);
            if ($row['class'] == "") rh_html_add_text($row['name'], false, false);
            else echo rh_html_add_text($row['name'] . " (Klassenraum " . $row['class']. ")", false, false);
        }
        rh_html_close();
        rh_html_up();
        rh_html_add("input", false, array("type" => "submit", "value" => "Auswählen", "formaction" => $formaction));
        if (isset($_POST['comment'])) rh_html_add("input", false, array("type" => "hidden", "name" => "comment", "value" => htmlentities($_POST['comment'], ENT_QUOTES)));
        if (isset($_POST['severity'])) rh_html_add("input", false, array("type" => "hidden", "name" => "severity", "value" => htmlentities($_POST['severity'], ENT_QUOTES)));
        if (isset($_POST['assignee_id'])) rh_html_add("input", false, array("type" => "hidden", "name" => "assignee_id", "value" => (int) $_POST['assignee_id'], ENT_QUOTES));
        if (isset($_POST['status'])) rh_html_add("input", false, array("type" => "hidden", "name" => "status", "value" => htmlentities($_POST['status'], ENT_QUOTES)));
        if (isset($_POST['resolution'])) rh_html_add("input", false, array("type" => "hidden", "name" => "resolution", "value" => htmlentities($_POST['resolution'], ENT_QUOTES)));
        if (isset($_POST['allow_comments'])) rh_html_add("input", false, array("type" => "hidden", "name" => "allow_comments", "value" => htmlentities($_POST['allow_comments'], ENT_QUOTES)));
    }
    else {
        rh_html_add("span", true, array("style" => "font-weight: bold"), false);
        rh_html_add_text($room['name']);
        if ($room['class'] != "") rh_html_add_text(" (Klassenraum " . $room['class'] . ")");
        rh_html_add("input", false, array("type" => "hidden", "name" => "roomid", "value" => (int)$room['id']));
        rh_html_add("input", false, array("type" => "submit", "formaction" => $formaction, "value" => "Ändern"));
    }
}

?>
