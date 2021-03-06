<?php

require_once("lib/rh_html.php");
require_once("lib/permissions.php");

function rh_html_table($header, $data, $tableattr = array(), $tdattr = array(), $trattr = array(), $return = false, $current_filters = array()) { // UNHOLY!
    $colspan = 0;

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
    
    if (isset($tableattr['class'])) $tableattr['class'] .= " rh_table";
    else $tableattr['class'] = "rh_table";
    rh_html_add("table", true, $tableattr);
    rh_html_down();
    if (!isset($header_tr_attr['class'])) $header_tr_attr['class'] = "rh_head";
    rh_html_add("tr", true, $header_tr_attr);
    rh_html_down();
    foreach ($header as $k => $h) {
        $header_th_attr_current = $header_th_attr;
        $k_is_string = is_string($k);
        if ($k_is_string) {
            if ($header_th_attr_current['class'] != "") $header_th_attr_current['class'] .= " ";
            $header_th_attr_current['class'] .= "rh_html_table_order";
        }
        rh_html_add("th", true, $header_th_attr_current, $k_is_string);
        $colspan++;
        if ($k_is_string) {
            if ($k == "th_attr") continue;
            rh_html_down();
            $filters_arr = array();
            $order_by_arr = array();
            foreach ($current_filters as $f => $v) {
                if ($f != "order_by") $filters_arr[] = $f . "=" . $v;
                else $order_by_arr = $v;
            }
            /*  The following part is pure, unadulterated bullshit.
                If you've come here to fix it, don't. Just remove the ordering feature altogether,
                or do something simpler.
                
                You have been warned.
            */
            $class_now = "order_none";
            if (count($order_by_arr) > 0) {
                if (($key = array_search($k . "_d", $order_by_arr)) !== false) {
                    unset($order_by_arr[$key]);
                    array_unshift($order_by_arr, $k . "_a");
                    $class_now = "order_desc";
                }
                else if (($key = array_search($k . "_a", $order_by_arr)) !== false) {
                    unset($order_by_arr[$key]);
                    array_unshift($order_by_arr, $k . "_d");
                    $class_now = "order_asc";
                }
                else array_unshift($order_by_arr, $k . "_d");
            }
            else $order_by_arr[] = $k . "_d";
            rh_html_add("a", false, array("href" => $return . "?" . implode("&", $filters_arr) . "&order_by=" . implode("&order_by=", $order_by_arr), "class" => $class_now), false);
        }
        rh_html_add_raw($h, false);
        if ($k_is_string) {
            rh_html_close("a", false, false);
            rh_html_up();
        }
    }
    rh_html_close();
    rh_html_up();
    
    if (!count($data)) {
        if (!isset($trattr['class'])) $trattr['class'] = "rh_odd";
        rh_html_add("tr", true, $trattr);
        rh_html_down();
        $tdattr['colspan'] = $colspan;
        $tdattr['style'] = "text-align: center; border: 1px solid black; padding: 2px 2px; font-style: italic";
        rh_html_add("td", true, $tdattr, false);
        rh_html_add_text("Es gibt keine anzuzeigenden Daten.");
        rh_html_close();
        rh_html_up();
    }
    else {
        $i = 0;
        foreach ($data as $d) {
            $i++;
            if (!isset($trattr['class'])) $class = ($i % 2) ? "rh_odd" : "rh_even";
            else $class = $trattr['class'];
            $trattr_tmp = $trattr;      // EEEEWW!
            $trattr_tmp['class'] = $class;
            rh_html_add("tr", true, $trattr_tmp);
            rh_html_down();
            foreach ($d as $f) {
                if (is_array($f)) continue;
                rh_html_add("td", true, $tdattr, false);
                rh_html_add_raw($f, false);
            }
            rh_html_close();
            rh_html_up();
            if (is_array($d['linetext'])) {
                foreach ($d['linetext'] as $l) {
                    $trattr_tmp['class'] .= " linetext";
                    rh_html_add("tr", true, $trattr_tmp);
                    rh_html_down();
                    rh_html_add("td", true, array("class" => "shortdesc", "colspan" => $colspan));
                    rh_html_add_text($l);
                    rh_html_up();
                }
            }
            rh_html_add("tr", true, array("class" => "rh_spacer"));
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

function rh_html_box($content, $attr = array()) {
    rh_html_add("div", true, $attr);
    rh_html_down();
    rh_html_add_raw($content, true);
    rh_html_up();
}

function rh_html_head($title, $description = false, $keywords = false, $include_js = true, $additional_tags = array()) {
    rh_html_doctype("html", true);
    rh_html_add("html", true, array("lang" => "de"));
    rh_html_down();
    rh_html_add("head", true);
    rh_html_down();
    rh_html_add("meta", false, array("charset" => "utf-8"));
    if ($description !== false) rh_html_add("meta", false, array("name" => "description", "content" => $description));
    if ($keywords !== false) rh_html_add("meta", false, array("name" => "keywords", "content" => $keywords));
    if ($include_js) {
        rh_html_add_js(false, "rh_common.js");
        if (file_exists(DATE_INPUT_POLYFILL_SCRIPT)) rh_html_add_js(false, DATE_INPUT_POLYFILL_SCRIPT);
    }
    foreach ($additional_tags as $tag) rh_html_add($tag[0], $tag[1], $tag[2], $tag[3]);
    rh_html_add("title", true, array(), false);
    rh_html_add_text($title);
    rh_html_close();
    rh_html_add("link", false, array("rel" => "stylesheet", "href" => "roomhealth.css"));
    rh_html_up();
}

function rh_html_room_selector($room, $formaction, $newform = true) {
    global $mysql, $session;

    if ($room === false) {
        // we are not displaying a room, but the actual selector;
        // however, if we're changing a room, $_POST['roomid'] will be set!
        rh_html_add("fieldset", true, array("style" => "width: max-content; background-color: white"));
        rh_html_down();
        rh_html_add("legend", true, array(), false);
        rh_html_add_text("Defekt melden...");
        if ($newform) {
            rh_html_add("form", true, array("action" => "newissue.php", "method" => "POST"), true);
            rh_html_down();
        }
        rh_html_add("label", true, array("for" => "select_roomid", "style" => "min-width: 200px; display: inline-block"));
        rh_html_add_text("... in Raum: ", true, true);
        rh_html_add("select", true, array("name" => "roomid", "id" => "select_roomid", "style" => "min-width: 300px"));
        rh_html_down();
        $res = mysqli_query($mysql, "SELECT rooms.*, classes.name AS cname FROM rooms LEFT JOIN classes ON rooms.id = classes.room_id ORDER BY name ASC");
        $rc = mysqli_num_rows($res);
        $classrooms = array();
        for ($i = 0; $i < $rc; $i++) {
            $row = mysqli_fetch_assoc($res);
            $additional_info = array();
            if ($row['cname'] != NULL) $additional_info[] = "Klassenraum " . $row['cname'];
            if ($row['description'] != "") $additional_info[] = $row['description'];
            rh_html_add("option", true, array("value" => (int)$row['id'], "selected" => ($_POST['roomid'] == $row['id'])), false);
            if (!count($additional_info)) rh_html_add_text($row['name'], false, false);
            else echo rh_html_add_text($row['name'] . " (" . implode(", ", $additional_info) . ")", false, false);
            if ($row['cname'] != NULL && !in_array($row['cname'], $classrooms)) $classrooms[] = $row['cname'];
        }
        sort($classrooms);
        rh_html_close();
        rh_html_up();
        rh_html_add("input", false, array("type" => "submit", "value" => "Auswählen", "formaction" => $formaction, "name" => "by_room"));
        if (isset($_POST['comment'])) rh_html_add("input", false, array("type" => "hidden", "name" => "comment", "value" => str_replace("\n", "", $_POST['comment'])));
        if (isset($_POST['severity'])) rh_html_add("input", false, array("type" => "hidden", "name" => "severity", "value" => $_POST['severity']));
        if (isset($_POST['assignee_id'])) rh_html_add("input", false, array("type" => "hidden", "name" => "assignee_id", "value" => (int) $_POST['assignee_id']));
        if (isset($_POST['status'])) rh_html_add("input", false, array("type" => "hidden", "name" => "status", "value" => $_POST['status']));
        if (isset($_POST['resolution'])) rh_html_add("input", false, array("type" => "hidden", "name" => "resolution", "value" =>$_POST['resolution']));
        if (isset($_POST['allow_comments'])) rh_html_add("input", false, array("type" => "hidden", "name" => "allow_comments", "value" => $_POST['allow_comments']));
        if ($newform) {
            rh_html_up();
            rh_html_add("form", true, array("action" => "newissue.php", "method" => "POST"), true);
            rh_html_down();
        }
        else rh_html_add("br");
        if (isset($_POST['roomid'])) {
            $class = mysqli_query($mysql, "SELECT name FROM classes WHERE room_id = " . (int)$_POST['roomid']);
            $class = mysqli_fetch_assoc($class);
            if ($class !== false) $class = $class['name'];
        }
        rh_html_add("label", true, array("for" => "select_classroom", "style" => "min-width: 200px; display: inline-block"), true);
        rh_html_add_text("... im Klassenraum: ", true, true);
        rh_html_add("span", true, array("style" => "display: inline-block; min-width: 300px; text-align: right; margin-top: 0.5em"));
        rh_html_down();
        rh_html_add("select", true, array("id" => "select_classroom", "name" => "classroom"), true);
        rh_html_down();
        for ($i = 0; $i < sizeof($classrooms); $i++) {
            rh_html_add("option", true, array("value" => $classrooms[$i], "selected" => ($class == $classrooms[$i])), false);
            rh_html_add_text($classrooms[$i]);
        }
        rh_html_indent_on_next(false);
        rh_html_close();
        rh_html_up(2);
        rh_html_add("input", false, array("type" => "submit", "value" => "Auswählen", "formaction" => $formaction, "name" => "by_classroom"));
        if ($newform) {
            if (isset($_POST['comment'])) rh_html_add("input", false, array("type" => "hidden", "name" => "comment", "value" => $_POST['comment']));
            if (isset($_POST['severity'])) rh_html_add("input", false, array("type" => "hidden", "name" => "severity", "value" => $_POST['severity']));
            if (isset($_POST['assignee_id'])) rh_html_add("input", false, array("type" => "hidden", "name" => "assignee_id", "value" => (int) $_POST['assignee_id']));
            if (isset($_POST['status'])) rh_html_add("input", false, array("type" => "hidden", "name" => "status", "value" => $_POST['status']));
            if (isset($_POST['resolution'])) rh_html_add("input", false, array("type" => "hidden", "name" => "resolution", "value" => $_POST['resolution']));
            if (isset($_POST['allow_comments'])) rh_html_add("input", false, array("type" => "hidden", "name" => "allow_comments", "value" => $_POST['allow_comments']));
            rh_html_up();
        }
        rh_html_up();
    }
    else {
        $additional_info = array();
        if ($room['cname'] != NULL) $additional_info[] = "Klassenraum " . $room['cname'];
        if ($room['description'] != "") $additional_info[] = $room['description'];
        
        rh_html_add("fieldset", true, array("style" => "width: max-content; background-color: white"));
        rh_html_down();
        rh_html_add("legend", true, array(), false);
        rh_html_add_text("Raum");
        rh_html_add("label", true, array("for" => "submit_room", "style" => "min-width: 300px; display: inline-block; font-weight: bold"), false);
        rh_html_add_text($room['name']);
        if (count($additional_info)) rh_html_add_text(" (" . implode(", ", $additional_info) . ")");
        rh_html_add("span", true, array("style" => "display: inline-block; min-width: 200px; text-align: right"));
        rh_html_down();
        rh_html_add("input", false, array("type" => "hidden", "name" => "roomid", "value" => (int)$room['id']));
        rh_html_add("input", false, array("type" => "submit", "formaction" => $formaction, "value" => "Ändern", "id" => "submit_room"));
        rh_html_up(2);
    }
}

function rh_header($nexturi = false) {
    rh_html_add("div", true, array("class" => "rh_header"));
    rh_html_down();
    rh_navigation($nexturi);
    rh_html_up();
}

function rh_navigation($nexturi) {
    rh_html_add("ul", true, array("class" => "rh_navigation", "style" => "position: relative"));
    rh_html_down();
    rh_html_add("li", true, array("class" => "rh_navigation"));
    rh_html_down();
    rh_html_add("a", true, array("href" => "."), false);
    rh_html_add_text("Start");
    rh_html_close();
    rh_html_up();
    if (is_loggedin()) {
        rh_html_add("li", true, array("class" => "rh_navigation"));
        rh_html_down();
        rh_html_add("a", true, array("href" => "newissue.php"), false);
        rh_html_add_text("Neuen Defekt melden");
        rh_html_close();
        rh_html_up();
    }
    rh_html_add("li", true, array("class" => "rh_navigation"));
    rh_html_down();
    rh_html_add("a", true, array("href" => "listissues.php"), false);
    rh_html_add_text("Alle Defekte");
    rh_html_close();
    rh_html_up();
    rh_html_add("li", true, array("class" => "rh_navigation"));
    rh_html_down();
    rh_html_add("a", true, array("href" => "map.php"), false);
    rh_html_add_text("Raumplan");
    rh_html_close();
    rh_html_up();
    if (is_loggedin()) {
        rh_html_add("li", true, array("class" => "rh_navigation_dropdown"));
        rh_html_down();
        rh_html_add("a", true, array(), false);
        rh_html_add_text("Meine Defekte");
        rh_html_add("div", true);
        rh_html_down();
        if (has_permission(PERMISSION_ISSUE_ASSIGNABLE)) {
            rh_html_add("a", true, array("href" => "listissues.php?assignee=" . get_session("userid") . "&status=OPEN&nolist"), false);
            rh_html_add_text("mir zugewiesene, offene Defekte");
        }
        if (has_permission(PERMISSION_ISSUE_ASSIGN_SELF)) {
            rh_html_add("a", true, array("href" => "listissues.php?assignee=-1&status=OPEN&nolist"), false);
            rh_html_add_text("noch nicht zugewiesene Defekte");
        }
        if (get_session("classroom") !== false) {
            rh_html_add("a", true, array("href" => "listissues.php?room=" . get_session("classroom") . "&nolist"), false);
            rh_html_add_text("Defekte in meinem Klassenraum");
        }
        rh_html_add("a", true, array("href" => "listissues.php?reported_by=" . get_session("userid") . "&nolist"), false);
        rh_html_add_text("von mir gemeldet");
        rh_html_close();
        rh_html_up();
        rh_html_up();
    }
    if ($_GET['error'] == "login") {
        rh_html_add("li", true, array("class" => "rh_loginerror_ul", "style" => "color: red; font-weight: bold; padding: 16px; vertical-align: bottom"), false);
        rh_html_add_text("Login fehlgeschlagen!");
        rh_html_close();
        unset($_GET['error']); // do not display it twice
    }
    rh_loginform_ul($nexturi);
    rh_html_up();
}

function rh_loginform_ul($nexturi) {
	if ($nexturi === false) $nexturi = $_SERVER['REQUEST_URI'];
	global $mysql;
	if (is_loggedin()) {
		rh_html_add("li", true, array("class" => "rh_loginform_ul"));
        rh_html_down();
        rh_html_add("span", true, array("style" => "margin-top: 8px"));
        rh_html_down();
        rh_html_add("span", true, array("style" => "display: inline-block; clear: right; float: none; padding-right: 2em"), false);
        rh_html_add_text("Eingeloggt als:");
        rh_html_add("span", true, array("style" => "display: inline-block; clear: right; float: none; font-weight: bold; padding-right: 4px; position: relative", "class" => "rh_navigation_dropdown"));
        rh_html_down();
        rh_html_add("div", true);
        rh_html_down();
        rh_html_add("a", true, array("href" => "userinfo.php", "title" => "E-Mailadresse anpassen, Passwort ändern, etc."));
        rh_html_down();
        rh_html_add("img", false, array("src" => "img/settings.png", "alt" => "Einstellungen"));
        rh_html_add_text("Einstellungen");
        rh_html_up(2);
        rh_html_add_text(get_session("name"), true, true);
		rh_html_add("img", false, array("src" => get_session("gender") == "female" ? "img/user_fmle.png" : "img/user_male.png", "alt" => "Benutzersymbol", "style" => "vertical-align: text-bottom; z-index: 20; position: relative"));
		if (has_permission(PERMISSION_LEVEL_ADMIN)) rh_html_add("img", false, array("src" => "img/user_adm.png", "alt" => "Administrator", "title" => "Administrator", "style" => "vertical-align: text-bottom; position: relative; z-index: 10; margin-left: -1.2em"));
		else if (has_permission(PERMISSION_LEVEL_MOD)) rh_html_add("img", false, array("src" => "img/user_mod.png", "alt" => "Moderator", "title" => "Moderator", "style" => "vertical-align: text-bottom; position: relative; z-index: 10"));
		else if (has_permission(PERMISSION_ISSUE_ASSIGNABLE)) rh_html_add("img", false, array("src" => "img/user_hlpr.png", "alt" => "Technik-Team", "title" => "Technik-Team", "style" => "vertical-align: text-bottom; position: relative; z-index: 10"));
		if (($classname = get_session("classname")) !== false) rh_html_add("img", false, array("src" => "img/user_ctch.png", "alt" => "Klassenlehrer" . (get_session("gender") == "female" ? "in " : " ") . $classname, "title" => "Klassenlehrer" . (get_session("gender") == "female" ? "in " : " ") . $classname, "style" => "vertical-align: text-bottom; margin-left: -1.2em"));
        rh_html_up();
        rh_html_add("a", true, array("href" => "logout.php?next=" . urlencode($nexturi), "style" => "display: inline-block; clear: right; float: none"), false);
        rh_html_add_text("Ausloggen");
        rh_html_close();
        rh_html_up(2);
    }
	else {
        rh_html_add("li", true, array("class" => "rh_loginform_ul"));
        rh_html_down();
		rh_html_add("form", true, array("action" => "login.php", "method" => "POST"));
		rh_html_down();
        rh_html_add("span", true, array("class" => "rh_loginform_ul", "style" => "padding: 1em 1em"));
		rh_html_down();
		rh_html_add_text("Benutzer:", true, false);
		rh_html_add("input", false, array("name" => "login", "style" => "margin-left: 4px; margin-right: 12px"));
		rh_html_add_text("Passwort:", true, false);
		rh_html_add("input", false, array("type" => "password", "name" => "pwd", "style" => "margin-left: 4px; margin-right: 12px"));
		rh_html_add_text("", true); // this is stupid, why do I need this?
		rh_html_add("input", false, array("type" => "submit", "value" => "Login"));
		rh_html_up();
		rh_html_add("input", false, array("type" => "hidden", "name" => "nexturi", "value" => urlencode($nexturi)));
		rh_html_up(2);
	}
}

function rh_htmlentities_array($in, $mode = (ENT_QUOTES | ENT_HTML5)) {    // ugly!
    if (!is_array($in)) return array();
    foreach ($in as $key => $val) {
        if (is_string($val)) $in[$key] = htmlentities($val, $mode);
    }
    return $in;
}

function rh_html_end() {
    global $mysql;
    if ($mysql) mysqli_close($mysql);
    rh_html_up(RH_HTML_MAX_DEPTH);
}

function rh_resolution_img($status, $resolution) {
    // $status is not used for now...
    $imgtag = "<img src=\"img/%s.png\" alt=\"%s\" style=\"vertical-align: middle; padding-right: 4px; float: right\">";
    switch ($resolution) {
            case "REPORTED":
            $src = "s_reported";
            $alt = "REPORTED";
            break;
            
            case "CONFIRMED":
            $src = "s_confirmed";
            $alt = "CONFIRMED";
            break;
            
            case "NEEDSINFO":
            $src = "s_needsinfo";
            $alt = "NEEDSINFO";
            break;
            
            case "WORKSFORME":
            $src = "s_worksforme";
            $alt = "WORKSFORME";
            break;
            
            case "DUPLICATE":
            $src = "s_duplicate";
            $alt = "DUPLICATE";
            break;
            
            case "WONTFIX":
            $src = "s_wontfix";
            $alt = "WONTFIX";
            break;
            
            case "RESOLVED":
            $src = "s_resolved";
            $alt = "RESOLVED";
            break;
            
            default:
            return "";
            break;
    }
    return sprintf($imgtag, $src, $alt);
}

?>
