<?php

require_once("lib/rh_html_parts.php");
require_once("lib/session.php");
include("include/acceptable.php");

rh_session();
rh_html_init();

global $mysql, $session;

rh_html_head("GRB: Raumplan IT-Defekte");
rh_html_add("body", true);
rh_html_down();
rh_html_add("div", true, array("id" => "allcontainer"));
rh_html_down();
rh_html_add("h1", true, array(), false);
rh_html_add_text("GRB: Raumplan IT-Defekte");
rh_html_close();

rh_header();

rh_html_add("h2", true, array("style" => "font-style: italic;"), false);
rh_html_add_text("Raumplan kommt bald...");

rh_html_end();

?>
