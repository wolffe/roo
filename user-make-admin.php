<?php
include('includes/top.php');

$id = $_GET['id'];

$stmt = $db->prepare("UPDATE user SET plan = 9 WHERE userid=$id LIMIT 1");
$stmt->execute();

echo "<meta http-equiv=\"refresh\" content=\"0; url=admin.php\" />";
include('includes/bottom.php');
?>
