<?php
include('includes/functions.php');

if(isset($_SESSION['username'])) {
    $stmt = $db->prepare("SELECT * FROM user WHERE username='".$_SESSION['username']."'");
    $stmt->execute();
    $row = $stmt->fetch();
	$userid = $row['userid'];
}

$id = $_GET['id'];
$id = str_replace('bookmark_', '', $id);

// Mark current project as read
$stmt = $db->prepare("UPDATE flags SET flag = '1' WHERE user_id = '$userid' AND project_id = '$id'");
$stmt->execute();
?>
