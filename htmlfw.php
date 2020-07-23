<?php

require_once("lib/rh_html.php");
rh_html_init();

rh_html_doctype("html", true);
rh_html_add("html", true, false, false, "lang=\"de\"");
rh_html_down();
rh_html_add("head", true);
rh_html_down();
rh_html_add("meta", false, false, false, "charset=\"utf-8\"");
rh_html_add("meta", false, false, false, "name=\"description\" content=\"Test!\"");
rh_html_add("meta", false, false, false, "name=\"keywords\" content=\"test rh_html framework\"");
rh_html_add("title", true, false, false, false, false);
rh_html_add_raw("Test des Frameworks!");
rh_html_up();
rh_html_add("body", true);
rh_html_down();
rh_html_add("h1", true, false, false, false, false);
rh_html_add_raw("Test-Überschrift!");
rh_html_close();
rh_html_add("p", true, false, false, false, false);
rh_html_add_raw("Test-Absatz...");
rh_html_down();
rh_html_add("span", true, false, "font-weight: bold; color: red;", false, false);
rh_html_add_raw(" in rot und fett!");
rh_html_up(99);

?>
