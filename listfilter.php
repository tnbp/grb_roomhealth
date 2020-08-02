<?php

require_once("lib/session.php");

$filter_args = array();

if ($_POST['c_min_sev'] == "ok") $filter_args[] = "min_sev=" . (int) $_POST['min_sev'];
if ($_POST['c_min_time'] == "ok") {
    $min_time = strtotime($_POST['min_date'] . " " . $_POST['min_time']);
    if ($min_time !== false) $filter_args[] = "min_time=" . $min_time;
}
if ($_POST['c_assignee'] == "ok") $filter_args[] = "assignee=" . (int) $_POST['assignee'];
if ($_POST['c_status'] == "ok") $filter_args[] = "status=" . urlencode($_POST['status']);
if ($_POST['c_limit'] == "ok") {
    $limit = (int) $_POST['limit'];
    if ($limit > 0) $filter_args[] = "limit=" . $limit;
}

redirect("listissues.php?" . implode("&", $filter_args));


?>
