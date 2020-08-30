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
rh_html_add_text("Am Raumplan wird noch gearbeitet...");

rh_html_add("div", true, array("id" => "jsnotice", "style" => "margin: 5em auto; border: 1px solid black; padding: 2em 4em; width: max-content; background-color: white"));
rh_html_add_text("Lade Raumplan... JavaScript ist erforderlich!");
rh_html_add("div", true, array("id" => "rh_map_nav", "style" => "margin: 2em auto 0px; border: 1px solid black; padding: 2em 4em; width: max-content; background-color: white"));
rh_html_down();
rh_html_add("div", true, array("style" => "display: inline-block; text-align: center; width: 100%"));
rh_html_down();
rh_html_add("img", false, array("src" => "img/a_upb.png", "id" => "rh_map_nav_u", "alt" => "Zeige höheres Stockwerk", "title" => "Zeige höheres Stockwerk"));
rh_html_up();
rh_html_add("div", true);
rh_html_down();
rh_html_add("span", true, array("style" => "display: inline-block; vertical-align: middle; min-height: 3em"));
rh_html_down();
rh_html_add("img", false, array("src" => "img/a_left.png", "id" => "rh_map_nav_l", "alt" => "Zeige vorheriges Gebäude", "title" => "Zeige vorheriges Gebäude"));
rh_html_up();
rh_html_add("span", true, array("style" => "display: inline-block; text-align: center; vertical-align: middle; min-height: 3em"));
rh_html_down();
rh_html_add("select", true, array("id" => "rh_map_nav_bldg"));
rh_html_down();
rh_html_add("option", true);
rh_html_add_text("A-Trakt");
rh_html_up();
rh_html_add("select", true, array("id" => "rh_map_nav_floor"));
rh_html_down();
rh_html_add("option", true);
rh_html_add_text("Erdgeschoss");
rh_html_up();
rh_html_up();
rh_html_add("span", true, array("style" => "display: inline-block; vertical-align: middle; min-height: 3em"));
rh_html_down();
rh_html_add("img", false, array("src" => "img/a_right.png", "id" => "rh_map_nav_r", "alt" => "Zeige nächstes Gebäude", "title" => "Zeige nächstes Gebäude"));
rh_html_up();
rh_html_up();
rh_html_add("div", true, array("style" => "text-align: center"));
rh_html_down();
rh_html_add("img", false, array("src" => "img/a_downb.png", "id" => "rh_map_nav_d", "alt" => "Zeige tieferes Stockwerk", "title" => "Zeige tieferes Stockwerk"));
rh_html_up(2);
rh_html_add("div", true, array("id" => "rh_map_container", "style" => "display: none; margin: auto auto; border: 1px solid white; text-align: center"));
rh_html_close();
rh_html_add_js(false, "include/ajax_request.js");
rh_html_add_js(false, "include/rh_map.js");

rh_html_end();

?>
