<?php

require_once("lib/session.php");
require_once("lib/permissions.php");
require("include/acceptable.php");

rh_session();
global $mysql;

is_loggedin() || redirect("index.php?error=notloggedin");

if (isset($_GET['toggleactive'])) {
    // toggle a user's active status
    has_permission(PERMISSION_LEVEL_ADMIN) || redirect("userinfo.php?error=perm");
    $userid = (int) $_GET['toggleactive'];
    if ($userid != $_GET['toggleactive']) {
        // $_GET['toggleactive'] was not an integer; this should never happen and we definitely cannot proceed
        redirect("userinfo.php?error=nosuchuser");
    }
    if ($userid == get_session("userid")) {
        // you cannot deactivate your own account
        redirect("userinfo.php?error=self_disallowed");
    }
    // toggle active status
    mysqli_query($mysql, "UPDATE users SET active = (1 - active) WHERE id = " . $userid);
    if (mysqli_affected_rows($mysql) < 1) {
        // we couldn't find that user; error out!
        redirect("userinfo.php?error=nosuchuser");
    }
    // also close all active sessions
    mysqli_query($mysql, "DELETE FROM sessions WHERE user_id = " . $userid);
    redirect("userinfo.php?id=" . $userid ."&changed=active");
}
else {
    if (isset($_GET['user'])) {
        $userid = (int) $_GET['user'];
        if ($userid != $_GET['user']) {
            // $_GET['user'] was not an integer; this should never happen and we definitely cannot proceed
            redirect("userinfo.php?error=nosuchuser");
        }
    }
    else $userid = get_session("userid");
    
    $res = mysqli_query($mysql, "SELECT users.*, classes.id AS class_id FROM users LEFT JOIN classes ON classes.teacher_id = users.id WHERE users.id = " . $userid);
    if (($user = mysqli_fetch_assoc($res)) == NULL) {
        // there seems to be no such user; error out!
        redirect("userinfo.php?error=nosuchuser");
    }
    
    if (isset($_POST['oldpw'])) {
        $oldpw = $_POST['oldpw'];
        $newpw_1 = $_POST['newpw_1'];
        $newpw_2 = $_POST['newpw_2'];
        
        // TODO: re-factor verify_login and use it here
        if (password_verify($oldpw, $user['pwhash']) !== true) {
            // old password was wrong; error out!
            redirect("userinfo.php?error=wrongpw");
        }
        if ($newpw_1 != $newpw_2) {
            // new passwords don't match; error out!
            redirect("userinfo.php?error=pw_nomatch");
        }
        if (strlen($newpw_1) < 8) {
            // new password too short; error out!
            redirect("userinfo.php?error=pw_short");
        }
        $pw_score = preg_match("/[A-Z]/", $newpw_1) + preg_match("/[a-z]/", $newpw_1) + preg_match("/\d/", $newpw_1) + preg_match("/[^a-zA-Z\d\s]/", $newpw_1);
        if ($pw_score < 3) {
            // password not good enough; error out!
            redirect("userinfo.php?error=pw_weak");
        }
        
        // all good--update the password!
        mysqli_query($mysql, "UPDATE users SET pwhash = '" . password_hash($newpw_1, PASSWORD_BCRYPT) . "' WHERE id = " . $userid);
        if (mysqli_affected_rows($mysql) < 1) {
            // we couldn't find that user; error out!
            redirect("userinfo.php?error=nosuchuser");
        }
        redirect("userinfo.php?changed=password");
    }
    else if (isset($_POST['emailaddr'])) {
        // check permissions; everyone can change their own, admins can change everyone's, others can't change anything.
        has_permission(PERMISSION_LEVEL_ADMIN) || $user['id'] == get_session("userid") || redirect("index.php?error=perm");
        
        $newemail = filter_var($_POST['emailaddr'], FILTER_VALIDATE_EMAIL);
        $newgender = (in_array($_POST['gender'], $gender_acceptable) ? $_POST['gender'] : false);
        $newclass = (int) $_POST['classname'];
        
        $changes = array();
        if ($newemail !== false && $newemail != $user['email']) $changes['email'] = mysqli_real_escape_string($mysql, $newemail);
        if ($newgender !== false && $newgender != $user['gender']) $changes['gender'] = mysqli_real_escape_string($mysql, $newgender);
        
        $class_changed = false;
        
        if ($newclass != $user['class_id']) {
            mysqli_query($mysql, "UPDATE classes SET teacher_id = NULL WHERE teacher_id = " . $userid);         // remove teacher from old class(es)
            mysqli_query($mysql, "UPDATE classes SET teacher_id = " . $userid . " WHERE id = " . $newclass);    // add teacher to new class
            $changes['class'] = true;
        }
        
        if (!count($changes)) {
            // data did not change; error out!
            redirect("userinfo.php?" . (($userid != get_session("userid")) ? ("id=" . $userid . "&") : "") . "error=nochange");
        }
        $updates = array();
        foreach ($changes as $c => $v) {
            if ($c == "class") continue;    // different table
            else $updates[] = $c . " = '" . $v . "'";
        }
        $all_changes = array_keys($changes);
        mysqli_query($mysql, "UPDATE users SET " . implode($updates, ", ") . " WHERE id = " . $userid);
        redirect("userinfo.php?" . (($userid != get_session("userid")) ? ("id=" . $userid . "&changed=") : "changed=") . implode("&changed=", $all_changes));
        // TODO: add PERMISSION_... descriptions
    }
    else if (isset($_POST['username'])) {
        // admins only
        has_permission(PERMISSION_LEVEL_ADMIN) || redirect("index.php?error=perm");
        
        $changes = array();        
        $newname = $_POST['username'];
        $newlogin = preg_match("/^[A-Za-z]{2,}\$/", $_POST['login']) ? $_POST['login'] : false;
        
        if ($newname != $user['name']) $changes['name'] = mysqli_real_escape_string($mysql, $newname);
        if ($newlogin !== false && $newlogin != $user['login']) {
            $changes['login'] = mysqli_real_escape_string($mysql, $newlogin);
            $exists = mysqli_query($mysql, "SELECT id FROM users WHERE login = '" . $changes['login'] ."'");
            if (mysqli_num_rows($exists)) {
                // duplicate login - error out!
                redirect("userinfo.php?" . (($userid != get_session("userid")) ? ("id=" . $userid . "&") : "") . "error=dupl_login");
            }
        }
        if (!count($changes)) {
            // data did not change; error out!
            redirect("userinfo.php?" . (($userid != get_session("userid")) ? ("id=" . $userid . "&") : "") . "error=nochange");
        }
        $updates = array();
        foreach ($changes as $c => $v) $updates[] = $c . " = '" . $v . "'";
        $all_changes = array_keys($changes);
        mysqli_query($mysql, "UPDATE users SET " . implode($updates, ", ") . " WHERE id = " . $userid);
        redirect("userinfo.php?" . (($userid != get_session("userid")) ? ("id=" . $userid . "&changed=") : "changed=") . implode("&changed=", $all_changes));
    }
    else if (isset($_POST['initialpw'])) {
        // admins only
        has_permission(PERMISSION_LEVEL_ADMIN) || redirect("index.php?error=perm");
        mysqli_query($mysql, "UPDATE users SET pwhash = '" . password_hash($user['initial_pw'], PASSWORD_BCRYPT) . "' WHERE id = " . $userid);
        redirect("userinfo.php?" . (($userid != get_session("userid")) ? ("id=" . $userid . "&changed=initialpw") : "changed=initialpw"));
    }
    else if (isset($_POST['edit_permissions'])) {
        // admins only
        has_permission(PERMISSION_LEVEL_ADMIN) || redirect("index.php?error=perm");
        // you cannot change your own permissions
        if ($userid == get_session("userid")) redirect("userinfo.php?error=self_disallowed");
        $newpermissions = 0;
        foreach ($_POST as $k => $v) {
            if (strpos($k, "perm_PERMISSION") !== 0) continue;   // ignore all POST data that does not start with "perm_PERMISSION"
            else $newpermissions |= (int) $v;
        }
        if ($newpermissions != $user['permissions']) mysqli_query($mysql, "UPDATE users SET permissions = " . $newpermissions . " WHERE id = " . $userid);
        else redirect("userinfo.php?" . (($userid != get_session("userid")) ? ("id=" . $userid . "&") : "") . "error=nochange");
        redirect("userinfo.php?" . (($userid != get_session("userid")) ? ("id=" . $userid . "&changed=permissions") : "changed=permissions"));
    }
}

redirect("index.php?error=general");

?>
