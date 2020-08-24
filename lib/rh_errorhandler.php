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
    switch ($_GET['error']) {
        default:
        rh_html_add("div", true, array("class" => "rh_errorbox"));
        rh_html_down();
        rh_html_add("span", true, array("class" => "rh_errortext"));
        rh_html_add_text("Fehler:");
        rh_html_close();
        rh_html_add_text("Unbekannter Fehler.");
        rh_html_up();
        break;
    }
    return;
}

?>
