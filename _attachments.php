<?php
include('includes/functions.php');

$pid = $_POST['pid'];
$res = $db->query("SELECT * FROM upload WHERE project_id_fk = '$pid' ORDER BY id DESC");
foreach($res as $myrowu) {
	$icon = explode('.', $myrowu['name']);

    $link = 'temporary/full/' . $myrowu['name'];

	echo '<div class="ratt" id="a' . $myrowu['id'] . '"><i class="fa fa-file"></i> <a href="#" onclick="attachmentDelete(' . $myrowu['id'] . '); return false;"><i class="fa fa-trash-o" title="Delete attachment"></i></a> <a href="' . $link . '" class="attachment" rel="facebox"><small>' . $myrowu['name'] . '</small></a></div>';
}
?>
