<?php
include('includes/top.php');

echo '<h2>Add member</h2>';

$userid = $_GET['userid'];
$pid = $_GET['pid'];

// needs lots of tweaking // very buggy
$stmt = $db->prepare("SELECT * FROM data WHERE `id` = $pid");
$stmt->execute();
$row = $stmt->fetch();

$uids = $row['uids'];
$uids = str_replace($userid,"",$uids);

$stmt = $db->prepare("UPDATE `data` SET `uids` = '$uids' WHERE `id` = $pid LIMIT 1");
$stmt->execute();
//

echo '<div class="confirm">Member removed!</div>';


include('includes/bottom.php');
?>
