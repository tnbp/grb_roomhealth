<?php

define("PERMISSION_ISSUE_CLOSE",          1);
define("PERMISSION_ISSUE_DELETE",         2);
define("PERMISSION_ISSUE_ASSIGN_SELF",    4);
define("PERMISSION_ISSUE_ASSIGN",         12);    // actual value: 8; but implies PERMISSION_ISSUE_ASSIGN_SELF
define("PERMISSION_ISSUE_SET_SEVERITY",   16);
// to be defined

function require_permission_or_redirect($permission, $target = "") {
    global $session;
    if ($session['permissions'] & $permission) return;
    header("Location: " . $target);
}

function require_loggedin_or_redirect($target = "index.php") {
    global $session;
    if ($session['loggedin'] !== true) header("Location: " . $target);
}

function has_permission($permission) {
    global $session;
    return ($permission & $session['permissions']) ? true : false;
}

?>
