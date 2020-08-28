<?php

require_once("lib/rh_html_parts.php");

function rh_errorhandler($errorlist = false) {
    if (!isset($_GET['error'])) return; // there is no error
    if (is_array($errorlist)) {     
        /*  if $errorlist is an array, it should have one of two keys: 'only' and 'not'
            always respond to errors in $errorlist['only'], never respond to errors in $errorlist['not']
        */
        if (isset($errorlist['only'])) {
            if (!in_array($_GET['error'], $errorlist['only'])) return;
        }
        if (isset($errorlist['not'])) {
            if (in_array($_GET['error'], $errorlist['not'])) return;
        }
    }
    $message = "Unbekannter Fehler.";
    switch ($_GET['error']) {
        case "dupl_login":
        $message = "Dieser Login existiert schon!";
        break;
        
        case "wrongpw":
        $message = "Altes Passwort stimmt nicht!";
        break;
        
        case "pw_nomatch":
        $message = "Passwörter stimmen nicht überein!";
        break;
        
        case "pw_short":
        $message = "Neues Passwort ist zu kurz!";
        break;
        
        case "pw_weak":
        $message = "Neues Passwort ist nicht komplex genug!";
        break;
        
        case "perm":
        $message = "Unzureichende Berechtigung für diesen Vorgang.";
        break;
        
        case "nosuchuser":
        $message = "Benutzer nicht gefunden.";
        break;
        
        case "self_disallowed":
        $message = "Die eigenen Berechtigungen können nicht verändert oder der eigene Account deaktiviert werden.";
        break;
        
        case "nochange":
        $message = "Keine Änderungen vorgenommen.";
        break;
    }
    rh_generic_box($message, "error", "Fehler", "errorbox");
    return;
}

function rh_generic_box($content, $type, $title, $box_id = false) {
    $box_classes = array("error" => "rh_errorbox", "message" => "rh_messagebox");
    $text_classes = array("error" => "rh_errortext", "message" => "rh_messagetext");
    rh_html_add("div", true, array("class" => "rh_fadebox " . (isset($box_classes[$type]) ? $box_classes[$type] : $box_classes['message']), "id" => $box_id));
    rh_html_down();
    rh_html_add_text($title . ":");
    rh_html_add("span", true, array("class" => (isset($text_classes[$type]) ? $text_classes[$type] : $text_classes['message'])));
    rh_html_add_text($content);
    rh_html_up();
}

?>
