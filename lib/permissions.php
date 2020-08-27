<?php

$permissions_list = array(
                        "PERMISSION_ISSUE_SET_STATUS"       => 1,
                        "PERMISSION_ISSUE_DELETE"           => 2,
                        "PERMISSION_ISSUE_ASSIGN_SELF"      => 4,
                        "PERMISSION_ISSUE_ASSIGN"           => 12,
                        "PERMISSION_ISSUE_SET_SEVERITY"     => 16,
                        "PERMISSION_ISSUE_SET_RESOLUTION"   => 32,
                        "PERMISSION_ISSUE_EDIT"             => 64,
                        "PERMISSION_COMMENT_ALWAYS"         => 128,
                        "PERMISSION_COMMENT_EDIT"           => 256,
                        "PERMISSION_ISSUE_ASSIGNABLE"       => 512
);

$permissions_description  = array(
                        "PERMISSION_ISSUE_SET_STATUS"       => "Benutzer darf Status von Defekten verändern",
                        "PERMISSION_ISSUE_DELETE"           => "Benutzer darf Defektmeldungen löschen",
                        "PERMISSION_ISSUE_ASSIGN_SELF"      => "Benutzer darf sich Defekte selbst zuweisen",
                        "PERMISSION_ISSUE_ASSIGN"           => "Benutzer darf Defekte anderen Nutzern zuweisen",
                        "PERMISSION_ISSUE_SET_SEVERITY"     => "Benutzer darf Schweregrad von Defekten verändern",
                        "PERMISSION_ISSUE_SET_RESOLUTION"   => "Benutzer darf Lösung von Defekten verändern",
                        "PERMISSION_ISSUE_EDIT"             => "Benutzer darf Defektmeldungen bearbeiten",
                        "PERMISSION_COMMENT_ALWAYS"         => "Benutzer darf immer kommentieren",
                        "PERMISSION_COMMENT_EDIT"           => "Benutzer darf Kommentare verändern",
                        "PERMISSION_ISSUE_ASSIGNABLE"       => "Benutzer darf Defektmeldungen übernehmen"
);

foreach ($permissions_list as $p => $v) define($p, $v);

define("PERMISSION_LEVEL_MOD", PERMISSION_ISSUE_ASSIGN_SELF | PERMISSION_ISSUE_SET_SEVERITY | PERMISSION_ISSUE_EDIT | PERMISSION_COMMENT_ALWAYS | PERMISSION_ISSUE_ASSIGNABLE | PERMISSION_COMMENT_EDIT);
define("PERMISSION_LEVEL_ADMIN", PERMISSION_LEVEL_MOD | PERMISSION_ISSUE_SET_STATUS | PERMISSION_ISSUE_DELETE | PERMISSION_ISSUE_ASSIGN | PERMISSION_ISSUE_SET_RESOLUTION);
// to be defined

function require_permission_or_redirect($permission, $target = "index.php") {   // replace this with more readable: has_permission($permission) or redirect()...
    if (has_permission($permission)) return;
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
