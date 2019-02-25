<?php
include('includes/config.php');
$id = $_POST['aid'];

$stmt = $db->prepare("DELETE FROM `upload` WHERE `id` = $id LIMIT 1");
$stmt->execute();
?>
