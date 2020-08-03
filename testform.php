<?php

require_once("lib/rh_html_parts.php");
require_once("lib/session.php");
require_once("lib/permissions.php");

rh_session();
rh_html_init();

global $mysql, $session;

rh_html_head("Neuen Defekt melden" . $id);
rh_html_add("body", true);
rh_html_down();
rh_html_add("div", true, array("id" => "allcontainer"));
rh_html_down();
rh_html_add("h1", true, array(), false);
rh_html_add_text("GRB Raumstatus");
rh_html_close();

rh_header();

rh_html_add("h2", true, array(), false);
rh_html_add_text("Testformular");
rh_html_add("form", true, array("action" => "#", "method" => "POST"));
rh_html_down();
rh_html_add("fieldset", true);
rh_html_down();
rh_html_add("legend", true, array(), false);
rh_html_add_text("Defekter Gegenstand");
rh_html_add("label", true, array("for" => "item", "style" => "min-width: 200px; display: inline-block"), false);
rh_html_add_text("Bitte auswählen...");
rh_html_add("select", true, array("id" => "item"));
rh_html_down();
rh_html_add("option", true, array("value" => "Linz"), false);
rh_html_add_text("Linz");
rh_html_add("option", true, array("value" => "Brunk"), false);
rh_html_add_text("Brunk");
rh_html_close();
rh_html_up(3);

rh_html_end();

?>
