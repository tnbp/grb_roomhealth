<?php

require_once("lib/session.php");
require_once("lib/permissions.php");
require_once("lib/comments.php");
require_once("lib/notification.php");
require("include/acceptable.php");

rh_session();
global $mysql, $session;

require_loggedin_or_redirect();

if (isset($_GET['id'])) {
    // if we have an id, we're modifying an already existing comment
    $id = (int) $_GET['id'];
    $res = mysqli_query($mysql, "SELECT * FROM issues WHERE id = (SELECT issue_id FROM comments WHERE id = " . $id . ")");
    $issue = mysqli_fetch_assoc($res);
    if ($issue === NULL) redirect("index.php?error=invalid_comment_edit&part=noissue");
    $res = mysqli_query($mysql, "SELECT * FROM comments WHERE id = " . $id);
    $comment = mysqli_fetch_assoc($res);
    if ($comment === NULL) redirect("index.php?error=invalid_comment_edit&part=nocomment");
    if (isset($_GET['del'])) {
        // we're deleting the comment!
        require_permission_or_redirect(PERMISSION_COMMENT_EDIT, "showissue.php?id=" . $issue['id'] . "&error=invalid_comment_del&part=permission");
        if ($_POST['del_ok'] != "ok") redirect("showissue.php?id=" . $issue['id'] . "&error=checkbox&cid=" . $id . "#commentmod_" . $id);
        mysqli_query($mysql, "DELETE FROM comments WHERE id = " . $id);
        redirect("showissue.php?id=" . $issue['id']);
    }
    else {
        // we're updating the comment!
        require_permission_or_redirect(PERMISSION_COMMENT_EDIT, "showissue.php?id=" . $issue['id'] . "&error=invalid_comment_edit&part=permission");
        $vis = $_POST['visible'];
        if (!in_array($vis, $comvis_acceptable)) redirect("showissue.php?id=" . $issue['id'] . "&error=invalid_comment_edit&part=visible");
        if ($_POST['visible'] == "none") require_permission_or_redirect(PERMISSION_LEVEL_ADMIN, "showissue.php?id=" . $issue['id'] . "&error=invalid_comment_edit&part=exceed");
        mysqli_query($mysql, "UPDATE comments SET visible = '" . mysqli_real_escape_string($mysql, $vis) ."' WHERE id = " . $id);
        redirect("showissue.php?id=" . $issue['id'] . "#commentmod_" . $id);
    }
}
else {
    // we're posting a new comment
    $issue_id = (int) $_GET['issue'];
    $res = mysqli_query($mysql, "SELECT * FROM issues WHERE id = " . $issue_id);
    if (($issue = mysqli_fetch_assoc($res)) === NULL) redirect("showissue.php?id=" . $issue_id . "&error=invalid_comment_post&part=noissue");
    if (!can_post_comment($issue)) redirect("showissue.php?id=" . $issue_id . "&error=invalid_comment_post&part=permission");
    $vis = $_POST['visible'];
    if (!in_array($vis, $comvis_acceptable)) redirect("showissue.php?id=" . $issue['id'] . "&error=invalid_comment_post&part=visible");
    $body = trim($_POST['body']);
    if (!strlen($body)) redirect("showissue.php?id=" . $issue['id'] . "&error=invalid_comment_post&part=body");
    mysqli_query($mysql, "INSERT INTO comments SET user_id = " . $session['userid'] . ", issue_id = " . $issue['id'] . ", timestamp = " . time() . ", body = '" . mysqli_real_escape_string($mysql, $_POST['body']) . "', visible = '" . mysqli_real_escape_string($mysql, $vis) . "'");
    $res = mysqli_query($mysql, "SELECT LAST_INSERT_ID() AS id");
    $commentid = mysqli_fetch_assoc($res);
    if ($commentid !== false) {
        rh_trigger_notification($issueid, NOTIFICATION_TRIGGER_COMMENT, "Der Defektbeschreibung #" . $issue_id . " wurde ein neuer Kommentar hinzugefügt:\r\n\r\nvon: " . get_session("name") . "\r\n" . $body, "GRB IT-Defekte: Update zu Defekt #" . $issueid);
        redirect("showissue.php?id=" . $issue['id'] . "#commentmod_" . $commentid['id']);
    }
    else redirect("showissue.php?id=" . $issue['id']);
}

?>
