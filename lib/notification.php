<?php

include("include/acceptable.php");
foreach ($notification_triggers as $trigger => $level) define($trigger, $level);

function send_notification($to, $subject, $body, $additional = array()) {
	$subject = filter_var($subject, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	$body = wordwrap(filter_var($body, FILTER_SANITIZE_FULL_SPECIAL_CHARS), 78, "\r\n");

    $php_version = explode(".", phpversion());
	if (($php_version[0] * 10 + $php_version[1]) < 72) {	// PHP < 7.2
		$header = "";
		foreach ($additional as $k => $v) {
			$header .= $k . ": " . $v . "\r\n";
		}
	}
	else $header = $additional;
	
	global $mysql;
	if (!is_array($to)) {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) return false;
        $db_check = mysqli_query($mysql, "SELECT email FROM users WHERE email = '" . mysqli_real_escape_string($mysql, $to) . "'");
        if (($row = mysqli_fetch_assoc($db_check)) === NULL) return false;	// only send mail to addresses in our database!
        $header['Bcc'] = $row['email'];
    }
	else {
        $bcc = array();
        foreach ($to as &$v) {
            if (!filter_var($v, FILTER_VALIDATE_EMAIL)) return false;
            $v = mysqli_real_escape_string($mysql, $v);
        }
        $db_check = mysqli_query($mysql, "SELECT email FROM users WHERE email IN ('" . implode("', '", $to) . "')");
        while (($row = mysqli_fetch_assoc($db_check)) !== NULL) $bcc[] = $row['email'];
        if (!count($bcc)) return false;
        $header['Bcc'] = implode(", ", $bcc);
    }

	if (!isset($header['From'])) $header['From'] = RH_MAIL_FROM;

    $mailto = "";
	return mail($mailto, $subject, $body, $header);
}

function rh_trigger_notification($issue, $level, $body, $subject = "GRB IT-Defekte: Update") {
    global $mysql;
    include("include/acceptable.php");
    
    if (!in_array($level, $notification_triggers)) return false;
    
    $recipients = array();
    
    $query = "SELECT users.email FROM users, notifications WHERE users.id = notifications.user_id AND notifications.issue_id = " . (int) $issue ." AND notifications.min_level >= " . (int) $level . (is_loggedin() ? (" AND users.id != " . get_session("userid")) : "");
    $res = mysqli_query($mysql, $query);
    while (($cur_recip = mysqli_fetch_assoc($res)) !== NULL) $recipients[] = $cur_recip['email'];
    
    if (count($recipients) > 0) return send_notification($recipients, $subject, $body);
    return false;
}

?>
