<?php 

require_once("lib/session.php");
require_once("lib/permissions.php");
require_once("lib/rh_html_parts.php");
require("include/acceptable.php");

rh_session();
rh_html_init();

global $mysql;
is_loggedin() or redirect("index.php");

$goto = false;

$id = (int) $_GET['id'];
if ($id) has_permission(PERMISSION_LEVEL_ADMIN) or redirect("userinfo.php");
else $id = get_session("userid");

$resetuser = isset($_GET['resetuser']);

$res = mysqli_query($mysql, "SELECT * FROM users WHERE users.id = " . $id);
$user = mysqli_fetch_assoc($res);
if ($user === NULL) {
    redirect("index.php?error=nosuchuser");
}

$allclasses = array();
$res = mysqli_query($mysql, "SELECT name,id,teacher_id FROM classes ORDER BY name ASC");
while (($class = mysqli_fetch_assoc($res)) !== NULL) $allclasses[] = $class;

// from stackoverflow.com: https://stackoverflow.com/questions/3466850
$password_regex = "^(?:(?=.*[a-z])(?:(?=.*[A-Z])(?=.*[\d\W])|(?=.*\W)(?=.*\d))|(?=.*\W)(?=.*[A-Z])(?=.*\d)).{8,}$"; // 3 out of 4: lowercase, uppercase, number, symbol; min 8 characters
$password_title = "Mindestens 8 Zeichen, darunter mindestens 3 von 4: Großbuchstaben, Kleinbuchstaben, Ziffern, Sonderzeichen";

rh_html_head("GRB-IT-Defekte: Benutzerdaten bearbeiten");
rh_html_add("body", true);
rh_html_down();
rh_html_add("div", true, array("id" => "allcontainer"));
rh_html_down();
rh_html_add("h1", true, array(), false);
rh_html_add_text("GRB-IT-Defekte: Benutzerdaten bearbeiten");
rh_header();
rh_html_add("fieldset", true, array("style" => "background-color: #f7f7ff; position: relative"));
rh_html_down();
rh_html_add("legend", true, array(), false);
rh_html_add_text("Benutzerdaten bearbeiten");
if (has_permission(PERMISSION_LEVEL_ADMIN)) {
    if ($resetuser) rh_html_add("form", true, array("action" => "userinfo.php", "method" => "GET"));
    else rh_html_add("form", true, array("action" => "userinfo.php?id=". $id . "&resetuser", "method" => "POST"));
    rh_html_down();
    rh_html_add("fieldset", true, array("style" => "float: left; margin-bottom: 1em; background-color: white"));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Benutzerverwaltung: Benutzer auswählen");
    rh_html_add("label", true, array("for" => "useredit_name", "style" => "margin-right: 2em"), false);
    rh_html_add_text("Bearbeite Benutzer:");
    if ($resetuser) {
        rh_html_add("select", true, array("id" => "useredit_name", "style" => "width: 20em", "name" => "id", "disabled" => !$resetuser));
        rh_html_down();
        rh_html_add("option", true, array("value" => get_session("userid")), false);
        rh_html_add_text("-- mich selbst -- ");
        $res = mysqli_query($mysql, "SELECT id,login,name FROM users WHERE id > 0 ORDER BY id ASC");
        $own_id = get_session("userid");
        while (($cur = mysqli_fetch_assoc($res)) !== NULL) {
            rh_html_add("option", true, array("value" => $cur['id'], "selected" => ($cur['id'] == $id && $id != $own_id)), false);
            rh_html_add_text($cur['name'] . " (" . $cur['login'] . ")");
            rh_html_indent_on_next(false);
            rh_html_close();
        }
        rh_html_up();
    }
    else {
        rh_html_add("span", true, array("style" => "font-weight: bold; display: inline-block; width: 20em"), false);
        rh_html_add_text($user['name']);
    }
    rh_html_add("input", false, array("type" => "submit", "value" => "Ändern"));
    rh_html_up(2);
}
rh_errorhandler(array("only" => array("perm", "nosuchuser", "self_disallowed", "nochange")));
display_changed_message(array("active"));
rh_html_add("fieldset", true, array("style" => "text-align: center; background-color: white; width: 50%; margin: 1em auto; clear: both", "class" => ($resetuser ? "rh_disabled rh_useredit" : false)));
rh_html_down();
rh_html_add("legend", true, array(), false);
rh_html_add_text("Passwort ändern");
rh_errorhandler(array("only" => array("wrongpw", "pw_nomatch", "pw_short", "pw_weak")));
display_changed_message(array("password"));
rh_html_add("form", true, array("action" => "useredit.php?user=" . $user['id'], "method" => "POST"));
rh_html_down();
rh_html_add("div", true, array("style" => "margin-bottom: 2em"));
rh_html_down();
rh_html_add("label", true, array("style" => "display: inline-block; min-width: 40%; text-align: left", "for" => "oldpw"), false);
rh_html_add_text("Altes Passwort eingeben:");
rh_html_add("span", true, array("style" => "display: inline-block; min-width: 40%; text-align: right"));
rh_html_down();
rh_html_add("input", false, array("style" => "width: 90%", "type" => "password", "id" => "oldpw", "name" => "oldpw", "disabled" => $resetuser));
rh_html_up(2);
rh_html_add("div", true);
rh_html_down();
rh_html_add("label", true, array("style" => "display: inline-block; min-width: 40%; text-align: left", "for" => "newpw_1"), false);
rh_html_add_text("Neues Passwort eingeben:");
rh_html_add("span", true, array("style" => "display: inline-block; min-width: 40%; text-align: right"));
rh_html_down();
rh_html_add("input", false, array("style" => "width: 90%", "type" => "password", "id" => "newpw_1", "name" => "newpw_1", "pattern" => $password_regex, "title" => $password_title, "disabled" => $resetuser));
rh_html_up(2);
rh_html_add("div", true, array("style" => "margin-top: .5em"));
rh_html_down();
rh_html_add("label", true, array("style" => "display: inline-block; min-width: 40%; text-align: left", "for" => "newpw_2"), false);
rh_html_add_text("Neues Passwort wiederholen:");
rh_html_add("span", true, array("style" => "display: inline-block; min-width: 40%; text-align: right"));
rh_html_down();
rh_html_add("input", false, array("style" => "width: 90%", "type" => "password", "id" => "newpw_2", "name" => "newpw_2", "pattern" => $password_regex, "title" => $password_title, "disabled" => $resetuser));
rh_html_up(2);
rh_html_add("div", true, array("style" => "margin-top: 1em"));
rh_html_down();
rh_html_add("span", true, array("style" => "display: inline-block; width: 40%"), false);
rh_html_indent_on_next(false);
rh_html_add("span", true, array("style" => "display: inline-block; text-align: right; min-width: 40%"));
rh_html_down();
rh_html_add("input", false, array("type" => "submit", "value" => "Passwort ändern", "disabled" => $resetuser));
rh_html_up(4);
rh_html_add("fieldset", true, array("style" => "text-align: center; background-color: white; width: 50%; margin: 2em auto 1em", "class" => ($resetuser ? "rh_disabled rh_useredit" : false)));
rh_html_down();
rh_html_add("legend", true, array(), false);
rh_html_add_text("Stammdaten ändern");
display_changed_message(array("email", "gender", "class"));
rh_html_add("form", true, array("action" => "useredit.php?user=" . $user['id'], "method" => "POST"));
rh_html_down();
rh_html_add("div", true);
rh_html_down();
rh_html_add("label", true, array("style" => "display: inline-block; min-width: 30%; text-align: left", "for" => "emailaddr"), false);
rh_html_add_text("E-Mail-Adresse:");
rh_html_add("span", true, array("style" => "display: inline-block; min-width: 60%; text-align: left"));
rh_html_down();
rh_html_add("input", false, array("style" => "width: 90%; text-align: center", "type" => "email", "id" => "emailaddr", "name" => "emailaddr", "value" => $user['email'], "disabled" => $resetuser));
rh_html_up(2);
rh_html_add("div", true, array("style" => "margin-top: 1em"));
rh_html_down();
rh_html_add("span", true, array("style" => "display: inline-block; width: 30%; text-align: left"), false);
rh_html_add_text("Geschlecht:");
rh_html_add("span", true, array("style" => "display: inline-block; text-align: left; min-width: 60%"));
rh_html_down();
rh_html_add("select", true, array("name" => "gender", "style" => "min-width: 15em", "disabled" => $resetuser));
rh_html_down();
$ga_count = count($gender_acceptable);
for ($i = 0; $i < $ga_count; $i++) {
    rh_html_add("option", true, array("value" => $gender_acceptable[$i], "selected" => ($user['gender'] == $gender_acceptable[$i])), false);
    rh_html_add_text($gender_description[$gender_acceptable[$i]]);
    rh_html_indent_on_next(false);
    rh_html_close();
}
rh_html_up(3);
rh_html_add("div", true, array("style" => "margin-top: 1em"));
rh_html_down();
rh_html_add("label", true, array("style" => "display: inline-block; min-width: 30%; text-align: left", "for" => "classname"), false);
rh_html_add_text("Klassenleitung:");
rh_html_add("span", true, array("style" => "display: inline-block; min-width: 60%; text-align: left"));
rh_html_down();
rh_html_add("select", true, array("id" => "classname", "name" => "classname", "style" => "min-width: 15em", "disabled" => $resetuser));
rh_html_down();
rh_html_add("option", true, array("value" => "-1"), false);
rh_html_add_text("keine");
$cl_count = count($allclasses);
for ($i = 0; $i < $cl_count; $i++) {
    rh_html_add("option", true, array("value" => $allclasses[$i]['id'], "selected" => ($user['id'] == $allclasses[$i]['teacher_id'])), false);
    rh_html_add_text($allclasses[$i]['name']);
    rh_html_indent_on_next(false);
    rh_html_close();
}
rh_html_up(3);
rh_html_add("div", true, array("style" => "margin-top: 1em"));
rh_html_down();
rh_html_add("span", true, array("style" => "display: inline-block; width: 30%"), false);
rh_html_indent_on_next(false);
rh_html_close();
rh_html_add("span", true, array("style" => "display: inline-block; text-align: right; min-width: 60%"));
rh_html_down();
rh_html_add("input", false, array("type" => "submit", "value" => "Stammdaten ändern", "disabled" => $resetuser));
rh_html_up(4);
if (has_permission(PERMISSION_LEVEL_ADMIN)) {
    rh_html_add("fieldset", true, array("style" => "text-align: center; background-color: white; width: 50%; margin: 2em auto 1em", "class" => ($resetuser ? "rh_disabled rh_useredit" : false)));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Benutzerverwaltung");
    display_changed_message(array("name", "login", "initialpw", "permissions"));
    rh_html_add("form", true, array("action" => "useredit.php?user=" . $user['id'], "method" => "POST"));
    rh_html_down();
    rh_html_add("fieldset", true);
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Name und Login");
    rh_errorhandler(array("only" => array("dupl_login")));
    rh_html_add("div", true);
    rh_html_down();
    rh_html_add("label", true, array("style" => "display: inline-block; min-width: 30%; text-align: left", "for" => "username"), false);
    rh_html_add_text("Name:");
    rh_html_add("span", true, array("style" => "display: inline-block; min-width: 60%; text-align: right"));
    rh_html_down();
    rh_html_add("input", false, array("style" => "width: 90%; text-align: center", "id" => "username", "name" => "username", "value" => $user['name'], "disabled" => $resetuser));
    rh_html_up(2);
    rh_html_add("div", true, array("style" => "margin-top: 1em"));
    rh_html_down();
    rh_html_add("label", true, array("style" => "display: inline-block; min-width: 30%; text-align: left", "for" => "login"), false);
    rh_html_add_text("Login:");
    rh_html_add("span", true, array("style" => "display: inline-block; min-width: 60%; text-align: center"));
    rh_html_down();
    rh_html_add("input", false, array("style" => "width: 20%; text-align: center", "id" => "login", "name" => "login", "value" => $user['login'], "disabled" => $resetuser, "pattern" => "[A-Za-z]{2,}", "title" => "Groß-/Kleinbuchstaben, mindestens 2 Zeichen"));
    rh_html_up(2);
    rh_html_add("div", true, array("style" => "margin-top: 1em"));
    rh_html_down();
    rh_html_add("span", true, array("style" => "display: inline-block; width: 30%"), false);
    rh_html_indent_on_next(false);
    rh_html_close();
    rh_html_add("span", true, array("style" => "display: inline-block; text-align: right; min-width: 60%"));
    rh_html_down();
    rh_html_add("input", false, array("type" => "submit", "value" => "Ändern", "disabled" => $resetuser));
    rh_html_up(4);
    rh_html_add("form", true, array("action" => "useredit.php?user=" . $user['id'], "method" => "POST"));
    rh_html_down();
    rh_html_add("fieldset", true);
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Passwort auf Initialpasswort zurücksetzen");
    rh_html_add("div", true, array("style" => "margin-top: 2em"));
    rh_html_down();
    rh_html_add("label", true, array("style" => "display: inline-block; min-width: 30%; text-align: left", "for" => "initialpw"), false);
    rh_html_add_text("Initialpasswort:");
    rh_html_add("span", true, array("style" => "display: inline-block; min-width: 60%; text-align: right"));
    rh_html_down();
    rh_html_add("input", false, array("style" => "width: 50%; text-align: center", "readonly" => true, "id" => "initialpw", "name" => "initialpw", "value" => $user['initial_pw'], "disabled" => $resetuser));
    rh_html_up(2);
    rh_html_add("div", true, array("style" => "margin-top: 1em"));
    rh_html_down();
    rh_html_add("span", true, array("style" => "display: inline-block; width: 30%"));
    rh_html_add("span", true, array("style" => "display: inline-block; text-align: right; min-width: 60%"));
    rh_html_down();
    rh_html_add("input", false, array("type" => "submit", "value" => "Passwort zurücksetzen", "disabled" => $resetuser));
    rh_html_up(4);
    $disable_permissions = (get_session("userid") == $user['id']);
    $disable_permissions_note = "Du kannst deine eigenen Benutzerrechte nicht ändern und deinen eigenen Account nicht deaktivieren.";
    rh_html_add("fieldset", true, array("style" => "margin-top: 1em; text-align: left; font-size: small", "class" => $disable_permissions ? "rh_disabled rh_permissions" : false, "title" => $disable_permissions ? $disable_permissions_note: false));
    rh_html_down();
    rh_html_add("legend", true, array(), false);
    rh_html_add_text("Benutzerrechte");
    rh_html_add("form", true, array("action" => "useredit.php?user=" . $user['id'], "method" => "POST"));
    rh_html_down();
    foreach ($permissions_list as $p => $v) {
        rh_html_add("span", true, array("style" => "white-space: nowrap; display: inline-block; width: 45%", "title" => $permissions_description[$p]));
        rh_html_down();
        rh_html_add("input", false, array("type" => "checkbox", "id" => "perm_" . $p, "name" => "perm_" . $p, "value" => $v, "disabled" => $resetuser || $disable_permissions, "checked" => (($user['permissions'] & $v) == $v)));
        rh_html_add("label", true, array("for" => "perm_" . $p), false);
        rh_html_add_text($p);
        rh_html_indent_on_next(false);
        rh_html_close();
        rh_html_up();
    }
    rh_html_add("span", true, array("style" => "white-space: nowrap; display: inline-block; float: right; margin-top: 1em"));
    rh_html_down();
    rh_html_add("input", false, array("name" => "edit_permissions", "type" => "submit", "value" => "Benutzerrechte ändern", "disabled" => $resetuser || $disable_permissions));
    rh_html_up(3);
    rh_html_add("div", true, array("style" => "margin-top: 1em; text-align: center"));
    rh_html_down();
    rh_html_add("form", true, array("action" => "useredit.php?toggleactive=" . $user['id'], "method" => "POST"));
    rh_html_down();
    if ($user['active']) rh_html_add("input", false, array("type" => "submit", "value" => "Account deaktivieren", "disabled" => $resetuser || $disable_permissions, "style" => ($resetuser || $disable_permissions) ? "background-color: #ffa0a0; color: white; border: #ffa0a0; font-weight: bold" : "background-color: red; color: white; border: red; font-weight: bold", "title" => $disable_permissions ? $disable_permissions_note: false));
    else rh_html_add("input", false, array("type" => "submit", "value" => "Account aktivieren", "disabled" => $resetuser || $disable_permissions, "style" => ($resetuser || $disable_permissions) ? "background-color: 3d643d; color: white; border: 3d643d; font-weight: bold" : "background-color: darkgreen; color: white; border: darkgreen; font-weight: bold"));
}

/*  There is a bunch of redundancy in here which should probably be taken care of at some point.
    For one, there are far too many style attributes in here.
*/

rh_html_add_js(false, "rh_message_fade.js");
rh_html_end();

function display_changed_message($which) {
    $messages = array(
        "active" => "Accountstatus geändert.",
        "password" => "Passwort aktualisiert.",
        "email" => "E-Mailadresse aktualisiert.",
        "gender" => "Geschlecht aktualisiert",
        "class" => "Klassenlehrer-Status aktualisiert.",
        "name" => "Name aktualisiert",
        "login" => "Benutzerlogin geändert.",
        "initialpw" => "Passwort auf Initialpasswort zurückgesetzt.",
        "permissions" => "Benutzerrechte geändert."
    );
    $changed = http_get_array("changed");
    $n = count($changed);
    $gotobox = false;
    for ($i = 0; $i < $n; $i++) {
        if (in_array($changed[$i], $which)) {
            rh_generic_box($messages[$changed[$i]], "message", "Hinweis", ($gotobox ? false : "msgbox"));
            if (!$gotobox) $gotobox = true;
        }
    }
}

?>
