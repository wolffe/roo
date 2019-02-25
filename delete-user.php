<?php
include('includes/top.php');

$id = $_GET['id'];

$stmt = $db->prepare("DELETE FROM user WHERE userid=$id LIMIT 1");
$stmt->execute();

echo "<meta http-equiv=\"refresh\" content=\"0; url=admin.php\" />";
include('includes/bottom.php');
?>
