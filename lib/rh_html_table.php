<?php

require_once("lib/rh_html.php");

function rh_html_table($header, $data, $tableattr = array(), $tdattr = array(), $trattr = array()) {
    if (isset($header['tr_attr'])) {
        $header_tr_attr = $header['tr_attr'];
        unset($header['tr_attr']);
    }
    else $header_tr_attr = array();
    if (isset($header['th_attr'])) {
        $header_th_attr = $header['th_attr'];
        unset($header['th_attr']);
    }
    else $header_th_attr = array();
    
    rh_html_add("table", true, $tableattr);
    rh_html_down();
    rh_html_add("tr", true, $header_tr_attr);
    rh_html_down();
    foreach ($header as $h) {
        rh_html_add("th", true, $header_th_attr, false);
        rh_html_add_raw($h, false);
    }
    rh_html_up();
    
    foreach ($data as $d) {
        rh_html_add("tr", true, $trattr);
        rh_html_down();
        foreach ($d as $f) {
            rh_html_add("td", true, $tdattr, false);
            rh_html_add_raw($f, false);
        }
        rh_html_up();
    }
    rh_html_up();
}

?>
