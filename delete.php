<?php
include('includes/top.php');

$id = $_GET['id'];

$stmt = $db->prepare("DELETE FROM data WHERE id=$id LIMIT 1");
$stmt->execute();

echo "<meta http-equiv=\"refresh\" content=\"0; url=index.php\" />";
include('includes/bottom.php');
?>
