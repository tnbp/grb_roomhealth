<?php

/*  json_issues.php
    Used in the interactive floor plan.
    Takes a building and floor as $_GET parameters and outputs a JSON file
    which detals all open issues in that building on that floor.
    No authentification is necessary or intended, since this file only returns
    publicly available information in a manner easily accessible to the map frontend.
*/

require_once("lib/session.php");
rh_session();

$issues = array();

if (!isset($_GET['building']) || !isset($_GET['floor'])) {
    echo json_encode($issues);
    die();
}

$building = $_GET['building'];
if (strlen($building) != 1 || !ctype_alpha($building)) {
    echo json_encode($issues);
    die();
}
$floor = (int) $_GET['floor'];

global $mysql;

$res = mysqli_query($mysql, "SELECT issues.id, issues.severity+0 AS sev, rooms.name AS rname, rooms.id AS room_id, rooms.description, rooms.rgroup, items.name AS iname FROM rooms, issues LEFT JOIN items ON items.id = issues.item_id WHERE issues.room_id = rooms.id AND rooms.rgroup LIKE '%/" . $building . "/%' AND rooms.rgroup LIKE '%/L" . $floor . "/%' AND issues.status = 'OPEN'");
while (($cur_issue = mysqli_fetch_assoc($res)) !== NULL) {
    // for some stupid reason, room_name cannot contain spaces
    $issues[] = array("id" => $cur_issue['id'], "item_name" => $cur_issue['iname'], "room_id" => $cur_issue['room_id'], "room_name" => str_replace(" ", "", $cur_issue['rname']), "room_desc" => $cur_issue['description'], "sev" => $cur_issue['sev']);
}

echo json_encode($issues);

?>
