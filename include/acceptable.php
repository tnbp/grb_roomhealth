<?php

$severity_acceptable = array(
                        "critical",
                        "high",
                        "normal",
                        "low"
                        );

$severity_description = array(
                        "critical" => "schwerwiegend",
                        "high" => "hoch",
                        "normal" => "mittel",
                        "low" => "niedrig"
                        );

$resolution_acceptable = array(
                        "REPORTED",
                        "CONFIRMED",
                        "NEEDSINFO",
                        "WORKSFORME",
                        "DUPLICATE",
                        "WONTFIX",
                        "RESOLVED"
                        );

$status_acceptable = array(
                        "OPEN",
                        "CLOSED"
                        );

$allcom_acceptable = array(
                        "all",
                        "author",
                        "mod",
                        "admin"
                        );
                        
$allcom_description = array(
                        "all" => "Kommentare offen",
                        "author" => "Melder und Moderatoren",
                        "mod" => "nur Moderatoren",
                        "admin" => "nur Administratoren"
                        );
                        
$comvis_acceptable = array(
                        "all",
                        "loggedin",
                        "author",
                        "mods",
                        "none"
                        );
                        
$comvis_description = array(
                        "all" => "alle",
                        "loggedin" => "nur eingeloggte Benutzer",
                        "author" => "nur Autor und Moderatoren",
                        "mods" => "nur Moderatoren",
                        "none" => "niemanden (ausgeblendet)"
                        );

$order_acceptable = array(
                        "id_a",     // issues.id, ASC
                        "id_d",     // issues.id, DESC
                        "tr_a",     // time_reported
                        "tr_d",
                        "sev_a",    // severity+0
                        "sev_d",
                        "asg_a",    // assignee_id
                        "asg_d",
                        "st_a",     // status+0
                        "st_d",
                        "up_a",     // last_updated
                        "up_d"
                        );

$order_description = array(
                        "id" => "issues.id",
                        "tr" => "issues.time_reported",
                        "sev" => "issues.severity+0",
                        "asg" => "issues.assignee_id",
                        "st" => "issues.status+0",
                        "up" => "issues.last_updated"
                        );

$gender_acceptable = array(
                        "unspecified",
                        "male",
                        "female",
                        "other"
                        );
                        
$gender_description = array(
                        "unspecified" => "nicht angegeben",
                        "male" => "männlich",
                        "female" => "weiblich",
                        "other" => "Sonstiges"
                        );
                        
$notification_triggers = array(
                        "NOTIFICATION_TRIGGER_STATUS" => 1,
                        "NOTIFICATION_TRIGGER_COMMENT" => 2
                        );

?>
