<?php
include('includes/config.php');
$updateid = $_POST['uid'];

$stmt = $db->prepare("UPDATE updates SET hidden='0' WHERE updateid='$updateid' LIMIT 1");
$stmt->execute();
?>
