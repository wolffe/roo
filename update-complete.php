<?php
include('includes/config.php');
$updateid = $_POST['uid'];

$stmt = $db->prepare("UPDATE updates SET type='3' WHERE updateid='$updateid' LIMIT 1");
$stmt->execute();
?>
