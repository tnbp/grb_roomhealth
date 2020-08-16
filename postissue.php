<?php

require_once("lib/session.php");
require_once("lib/permissions.php");
require("include/acceptable.php");

rh_session();
global $mysql, $session;

require_loggedin_or_redirect();

$roomid = (int)$_POST['roomid'];
$itemid = (int)$_POST['itemid'];
$comment = $_POST['comment'];
$severity = $_POST['severity'];

if ($roomid != -1) {
    $res = mysqli_query($mysql, "SELECT * FROM rooms WHERE id = " . $roomid);
    if (!mysqli_num_rows($res)) $error = "room";
}
if ($itemid != -1) {
    $res = mysqli_query($mysql, "SELECT * FROM items WHERE id = " . $itemid);
    if (!mysqli_num_rows($res)) $error = "item";
}
if (!in_array($_POST['severity'], $severity_acceptable)) $error = "severity";
if (isset($_GET['update'])) {
    // if we're *updating* an issue report, we have a lot more options and need to run more checks
    $issueid = (int) $_POST['issueid'];
    $assigneeid = (int) $_POST['assignee_id'];
    $resolution = $_POST['resolution'];
    $status = $_POST['status'];
    //$severity = $_POST['severity'];
    $allow_comments = $_POST['allow_comments'];
    
    $res = mysqli_query($mysql, "SELECT * FROM issues WHERE id = " . $issueid);
    $row = mysqli_fetch_assoc($res);
    if ($row === NULL) redirect("index.php?error=invalid_issue_update&part=issueid");
    
    $update = array();

    if (!in_array($resolution, $resolution_acceptable)) $error = "resolution";
    if (!in_array($status, $status_acceptable)) $error = "status";
    if (!in_array($allow_comments, $allcom_acceptable)) $error = "allcom";
    
    if ($resolution != $row['resolution']) {
        // resolution needs to be updated
        $update['resolution'] = mysqli_real_escape_string($mysql, $resolution);
        if (!has_permission(PERMISSION_ISSUE_SET_RESOLUTION)) $error = "permission";
    }
    if ($assigneeid != $row['assignee_id']) {
        // assignee_id needs to be updated
        $update['assignee_id'] = $assigneeid;
        if ($assigneeid != $session['userid'] && !has_permission(PERMISSION_ISSUE_ASSIGN)) $error = "permission";
        else if ($assigneeid == $session['userid'] && !has_permission(PERMISSION_ISSUE_ASSIGN_SELF)) $error = "permission";
    }
    if ($status != $row['status']) {
        // status needs to be updated
        $update['status'] = mysqli_real_escape_string($mysql, $status);
        if (!has_permission(PERMISSION_ISSUE_SET_STATUS)) $error = "permission";
    }
    if ($severity != $row['severity']) {
        // severity needs to be updated
        $update['severity'] = mysqli_real_escape_string($mysql, $severity);
        if (!has_permission(PERMISSION_ISSUE_SET_SEVERITY)) $error = "permission";
    }
    if ($comment != $row['comment']) {
        // update comment...
        $update['comment'] = mysqli_real_escape_string($mysql, $comment);
        if (!has_permission(PERMISSION_ISSUE_EDIT)) $error = "permission";
    }
    if ($roomid != $row['room_id'] && $itemid == -1) {
        /*  if the room has changed, automatically set iten_id = -1, UNLESS item_id is given
            in this case, set room_id = -1
        */
        $update['room_id'] = $roomid;
        $update['item_id'] = -1;
        if (!has_permission(PERMISSION_ISSUE_EDIT)) $error = "permission";
    }
    if ($itemid != $row['item_id'] && $itemid != -1) {
        $update['item_id'] = $itemid;
        $update['room_id'] = -1;
        if (!has_permission(PERMISSION_ISSUE_EDIT)) $error = "permission";
    }
    if ($allow_comments != $row['allow_comments']) {
        $update['allow_comments'] = $allow_comments;
        if (!has_permission(PERMISSION_ISSUE_EDIT)) $error = "permission";
    }
    
    if (!isset($error)) {
        if (!count($update)) redirect("editissue.php?id=" . $issueid . "&error=nochange");
        $update['last_updated'] = time();
        $update_arr = array();
        foreach ($update as $field => $val) {
            $update_arr[] = $field . " = '" . $val . "'";
        }
        $updatequery = "UPDATE issues SET " . implode(", ", $update_arr) . " WHERE id = " . $issueid;
        mysqli_query($mysql, $updatequery);
        if (isset($update['status']) || isset($update['resolution']) || isset($update['assignee_id']) || isset($update['severity'])) {
            // post a comment!
            $body = $session['name'] . " hat die Fehlerbeschreibung geändert:\r\n";
            if (isset($update['status'])) $body .= "STATUS: **" . $update['status'] . "**\r\n";
            if (isset($update['resolution'])) $body .= "LÖSUNG: **" . $update['resolution'] . "**\r\n";
            if (isset($update['assignee_id'])) {
                if ($update['assignee_id'] == -1) $name = "niemandem";
                else {
                    $name = mysqli_query($mysql, "SELECT name FROM users WHERE id = " . $update['assignee_id']);
                    $name = mysqli_fetch_assoc($name);
                    if ($name === false) $name = "niemandem";
                    else $name = $name['name'];
                }
                $body .= "ZUGEWIESEN: **" . $name . "**\r\n";
            }
            if (isset($update['severity'])) $body .= "SCHWEREGRAD: **" . $severity_description[$update['severity']] . "**\r\n";
            mysqli_query($mysql, "INSERT INTO comments SET user_id = 0, issue_id = " . $issueid . ", timestamp = " . time() . ", body = '" . $body . "', visible = 'all'");
        }
        if (isset($_POST['backtolist'])) redirect("listissues.php");
        else redirect("showissue.php?id=" . $issueid);
    }
    else redirect("editissue.php?id=" . $issueid . "&error=invalid_issue_update&part=" . $error);
}
else if (isset($_GET['delete'])) {
    // if we're *deleting* the issue, we just need to check for permission...
    require_permission_or_redirect(PERMISSION_ISSUE_DELETE, "listissues.php?error=invalid_issue_del&part=permission");
    if (!isset($_GET['id'])) redirect("listissues.php?error=invalid_issue_del&part=issueid");
    $issueid = (int) $_GET['id'];
    if ($_POST['del_ok'] != "ok") redirect("showissue.php?id=" . $issueid . "&error=checkbox");
    mysqli_query($mysql, "DELETE FROM issues WHERE id = " . $issueid);
    // also delete all associated allow_comments!
    mysqli_query($mysql, "DELETE FROM comments WHERE issue_id = " . $issueid);
    redirect("listissues.php");
}
else if (isset($_GET['assignself'])) {
    require_permission_or_redirect(PERMISSION_ISSUE_ASSIGN_SELF, "listissues.php?error=invalid_issue_post&part=permissions");
    if (!isset($_GET['id'])) redirect("listissues.php?error=invalid_issue_post&part=issueid");
    $issueid = (int) $_GET['id'];
    mysqli_query($mysql, "UPDATE issues SET assignee_id = " . $session['userid'] . " WHERE id = " . $issueid);
    redirect("showissue.php?id=" . $issueid);
}
else {
    // if we're only posting a new issue, there's not much that can go wrong, right?
    if (!isset($error)) {
        mysqli_query($mysql, "INSERT INTO issues SET time_reported = " . time() . ", reporter_id = " . $session['userid'] . ", comment = '" . mysqli_real_escape_string($mysql, $comment) . "', item_id = " . $itemid . ", room_id = " . $roomid . ", severity = '" . $severity . "', assignee_id = -1, status = 'OPEN', resolution = 'REPORTED', last_updated = " . time());
        $res = mysqli_query($mysql, "SELECT LAST_INSERT_ID() AS id");
        $newissue = mysqli_fetch_assoc($res);
        if ($newissue !== false) redirect("showissue.php?id= " . $newissue['id']);
        else redirect("listissues.php");
    }
    else redirect("listissues.php?error=invalid_issue_post&part=" . $error);
}

?>
