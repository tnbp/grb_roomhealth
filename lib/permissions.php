<?php

define("PERMISSION_ISSUE_SET_STATUS",     1);
define("PERMISSION_ISSUE_DELETE",         2);
define("PERMISSION_ISSUE_ASSIGN_SELF",    4);
define("PERMISSION_ISSUE_ASSIGN",         12);    // actual value: 8; but implies PERMISSION_ISSUE_ASSIGN_SELF
define("PERMISSION_ISSUE_SET_SEVERITY",   16);
define("PERMISSION_ISSUE_SET_RESOLUTION", 32);
define("PERMISSION_ISSUE_EDIT",           64);
define("PERMISSION_COMMENT_ALWAYS",       128);
define("PERMISSION_COMMENT_EDIT",         256);
define("PERMISSION_ISSUE_ASSIGNABLE",     512);

define("PERMISSION_LEVEL_MOD", PERMISSION_ISSUE_ASSIGN_SELF | PERMISSION_ISSUE_SET_SEVERITY | PERMISSION_ISSUE_EDIT | PERMISSION_COMMENT_ALWAYS | PERMISSION_ISSUE_ASSIGNABLE | PERMISSION_COMMENT_EDIT);
define("PERMISSION_LEVEL_ADMIN", PERMISSION_LEVEL_MOD | PERMISSION_ISSUE_SET_STATUS | PERMISSION_ISSUE_DELETE | PERMISSION_ISSUE_ASSIGN | PERMISSION_ISSUE_SET_RESOLUTION);
// to be defined

function require_permission_or_redirect($permission, $target = "index.php") {
    global $session;
    if ($session['permissions'] & $permission) return;
    redirect($target);
}

function require_loggedin_or_redirect($target = "index.php") {
    global $session;
    if ($session['loggedin'] !== true) redirect($target);
}

function is_loggedin() {
    global $session;
    return ($session['loggedin'] === true);
}

function has_permission($permission) {
    global $session;
    return (($permission & $session['permissions']) == $permission) ? true : false;
}

?>
