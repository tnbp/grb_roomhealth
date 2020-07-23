<?php

function rh_html_init() {
    global $rh_html;
    $rh_html = array("close_on_next" => false, "current_level" => 0);
}

function rh_html_add($element = "p", $needs_closing = false, $class = false, $style = false, $additional = false, $newline = true) {
    global $rh_html;
    if ($rh_html['close_on_next'] !== false) rh_html_close_all($rh_html['current_level']);
    echo str_repeat("\t", $rh_html['current_level']);
    echo "<" . $element;
    if ($class !== false) echo " class=\"" . $class . "\"";
    if ($style !== false) echo " style=\"" . $style . "\"";
    if ($additional !== false) echo " " . $additional;
    echo ">";
    if ($newline === true) echo "\n";
    if ($needs_closing !== false) $rh_html['open_elements'][$rh_html['current_level']][] = $element;
    $rh_html['close_on_next'] = true;
}

function rh_html_close($element = false, $level = false, $no_warning = false) {
    global $rh_html;
    if ($level === false) $level = $rh_html['current_level'];
    $lastelement = end($rh_html['open_elements'][$level]);
    if ($element === false) $element = array_pop($rh_html['open_elements'][$level]);
    else if ($lastelement != $element && $no_warning === false) {
        trigger_error("rh_html: Closing " . $element . " tag, but " . $lastelement . " is still open--possible nesting error!", E_USER_WARNING);
    }
    echo str_repeat("\t", $level);
    echo "</" . $element . ">\n";
    $rh_html['close_on_next'] = false;
}

function rh_html_up($levels = 1) {
    global $rh_html;
    $rh_html['current_level'] -= $levels;
    if ($rh_html['current_level'] < 0) $rh_html['current_level'] = 0;
    rh_html_close_all($rh_html['current_level']);
}

function rh_html_down() {
    global $rh_html;
    $rh_html['current_level']++;
    $rh_html['close_on_next'] = false;
}

function rh_html_close_all($limit) {
    global $rh_html;
    krsort($rh_html['open_elements']);
    $levels = array_keys($rh_html['open_elements']);
    foreach ($levels as $cur_level) {
        if ($cur_level < $limit) break;
        while (($cur_element = array_pop($rh_html['open_elements'][$cur_level])) != NULL) rh_html_close($cur_element, $cur_level, true);
    }
}

function rh_html_doctype($doctype) {
    echo "<!DOCTYPE " . $doctype . ">\n";
}

function rh_html_add_raw($content, $indent = false) {
    global $rh_html;
    if ($indent !== false) echo str_repeat("\t", $rh_html['current_level']+1);
    echo htmlentities($content, ENT_QUOTES | ENT_HTML5);
    if ($indent !== false) echo "\n";
}

?>
