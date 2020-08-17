<?php

require_once("lib/session.php");

// redirect.php: go to page, even from a <form>, but drop POST data etc.

$next = isset($_GET['next']) ? $_GET['next'] : "index.php";
redirect(urldecode($next));

?>
