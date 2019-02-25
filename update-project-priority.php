<?php
include('includes/config.php');
$id = $_POST['id'];
$priority = $_POST['priority'];

$stmt = $db->prepare("UPDATE data SET priority = '$priority' WHERE id = '$id' LIMIT 1");
$stmt->execute();
?>
