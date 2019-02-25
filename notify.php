<?php
// catch emails
$stmt = $db->prepare("SELECT * FROM data WHERE id='".$projectid."'");
$stmt->execute();
$myrow1 = $stmt->fetch();

$projectname = $myrow1['name'];
$uid = explode(',', $myrow1['uids']);
$users = count($uid);
$emails = '';
for($i=0; $i<$users; $i++) {
    $stmt = $db->prepare("SELECT * FROM user WHERE userid='".$uid[$i]."'");
    $stmt->execute();
    $myrow2 = $stmt->fetch();
	$emails .= $myrow2['email'].',';
}

$emails = trim($emails);
$icon = explode(',', $emails);
$test = count($icon)-1;
$notifications = 0;

$notification_reason = stripslashes($notification_reason);

//echo $emails; //DEBUG
for($n=0;$n<$test;$n++) {
	// send emails
	$to = $icon[$n];
	$subject = 'Project ' . $projectname . ' has been updated on ' . $org_title . '';
	$body = '<p>Hello! Project <strong>'.$projectname.'</strong> has just been updated on <b>' . $org_title . '</b>.</p>';
	$body .= '<p>What happened? '.$notification_reason.'</p>';
	$body .= '<p><small>You received this notification because you are a member of <b>' . $org_title . '</b> and you opted in for email notifications. If you wish not to receive notifications anymore, visit your <b>' . $org_title . '</b> control panel and select "Do not allow notifications".</small></p>';
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $headers .= 'From: roo! Notification <notify@roo.ie>' . "\r\n";

	if($to != '')
		if(mail($to, $subject, $body, $headers))
			$notifications++;
	else {}
	// end send emails
}
echo '<div class="confirm">' . $notifications . ' notification(s) sent!</div>';
// end catch emails
?>
