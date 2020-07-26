<?php

require_once("lib/rh_html.php");
rh_html_init();

rh_html_doctype("html", true);
rh_html_add("html", true, array("lang" => "de"));
rh_html_down();
rh_html_add("head", true);
rh_html_down();
rh_html_add("meta", false, array("charset" => "utf-8"));
rh_html_add("meta", false, array("name" => "description", "content" =>"Test!"));
rh_html_add("meta", false, array("name" => "keywords", "content" => "test rh_html framework"));
rh_html_add("title", true, array(), false);
rh_html_add_raw("Test des Frameworks!");
rh_html_up();
rh_html_add("body", true);
rh_html_down();
rh_html_add("h1", true, array(), false);
rh_html_add_raw("Test-Überschrift!");
rh_html_close();
rh_html_add("p", true, array(), false);
rh_html_add_raw("Test-Absatz...");
rh_html_down();
rh_html_add("span", true, array("style" => "font-weight: bold; color: red;"), false);
rh_html_add_raw(" in rot und fett!");
rh_html_up();

require_once("lib/rh_html_table.php");
$data = array(array("Eenie", "meenie", "miney", "moe"), array("Inky", "Blinky", "Dinky", "Sue"), array("Mr. Apostro'phe", "links|rechts", "<b>fat tire! (:</b>", "no"));
$header = array("Name", "Dame", "Shame", "Lame", "th_attr" => array("style" => "font-weight: bold; color: red; border: 2px solid black; padding: 5px 5px"));
$tableattr = array("style" => "margin-left: auto; margin-right: auto");
$tdattr = array("style" => "text-align: center; border: 1px solid black; padding: 2px 2px");

rh_html_table($header, $data, $tableattr, $tdattr);

rh_html_up(99);

?>
