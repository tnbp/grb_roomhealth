<?php

function send_notification($to, $subject, $body, $additional = array()) {
	if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;
	
	$subject = filter_var($subject, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$body = filter_var($body, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

	global $mysql;
	$db_check = mysqli_query($mysql, "SELECT id FROM users WHERE email = '" . mysqli_real_escape_string($mysql, $to) . "'");
	if (!mysqli_num_rows($db_check)) return false;	// only send mail to addresses in our database!

	if (!isset($additional['From'])) $additional['From'] = RH_MAIL_FROM;

	$php_version = explode(".", phpversion());
	if (($php_version[0] * 10 + $php_version[1]) < 72) {	// PHP < 7.2
		$header = "";
		foreach ($additional as $k => $v) {
			$header .= $k . ": " . $v . "\r\n";
		}
	}
	else $header = $additional;

	return mail($to, $subject, $body, $header);
}
