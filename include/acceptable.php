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

?>
