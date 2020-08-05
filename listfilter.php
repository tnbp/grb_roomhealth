<?php

require_once("lib/session.php");

$filter_args = array();

if ($_POST['c_min_sev'] == "ok") $filter_args[] = "min_sev=" . (int) $_POST['min_sev'];
else if ($_GET['c_min_sev'] == "ok") $filter_args[] = "min_sev=" . (int) $_GET['min_sev'];
if ($_POST['c_min_time'] == "ok") {
    $min_time = strtotime($_POST['min_date'] . " " . $_POST['min_time']);
    if ($min_time !== false) $filter_args[] = "min_time=" . $min_time;
}
else if ($_GET['c_min_time'] == "ok") {
    $min_time = strtotime($_GET['min_date'] . " " . $_GET['min_time']);
    if ($min_time !== false) $filter_args[] = "min_time=" . $min_time;
}
if ($_POST['c_assignee'] == "ok") $filter_args[] = "assignee=" . (int) $_POST['assignee'];
else if ($_GET['c_assignee'] == "ok") $filter_args[] = "assignee=" . (int) $_GET['assignee'];
if ($_POST['c_status'] == "ok") $filter_args[] = "status=" . urlencode($_POST['status']);
else if ($_GET['c_status'] == "ok") $filter_args[] = "status=" . urlencode($_GET['status']);
if (($limit = (int) $_POST['limit']) > 0) {
    $filter_args[] = "limit=" . $limit;
}
else if (($limit = (int) $_GET['limit']) > 0) {
    $filter_args[] = "limit=" . $limit;
}
if (is_array($_GET['order_by'])) {
    foreach ($_GET['order_by'] as $order) $filter_args[] = "order_by[]=" . $order;
}

redirect("listissues.php?" . implode("&", $filter_args));


?>
