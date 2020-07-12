<?php

require_once("generic_html.php");
require_once("lib/session.php");
rh_session();

generic_header();
echo "\t<body>\n";

rh_loginform();

echo "</body></html>";

?>