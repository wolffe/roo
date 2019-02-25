<?php include('includes/top.php');?>

<div>
	<?php
	if(isset($_POST['priority_add'])) {
		$priority_name = $_POST['priority_name'];

        $stmt = $db->prepare("INSERT INTO roo_priorities (priority_name) VALUES ('$priority_name')");
        $stmt->execute();
		echo '<div><a href="#" class="m-btn mini green-stripe"">Priority added successfully!</a></div><br>';
	}
	if(isset($_POST['priority_update'])) {
		$priority_id = $_POST['priority_id'];
		$priority_name = $_POST['priority_name'];

        $stmt = $db->prepare("UPDATE roo_priorities SET priority_name='$priority_name' WHERE priority_id='$priority_id' LIMIT 1");
        $stmt->execute();
		echo '<div><a href="#" class="m-btn mini green-stripe"">Priority updated successfully!</a></div><br>';
	}
	if(isset($_POST['priority_delete'])) {
		$priority_id = $_POST['priority_id'];

        $stmt = $db->prepare("DELETE FROM roo_priorities WHERE priority_id='$priority_id' LIMIT 1");
        $stmt->execute();
		header('Location: ' . $_SERVER['PHP_SELF']);
	}
	if(isset($_GET['pid'])) {
		$pid = mysql_real_escape_string($_GET['pid']);

        $stmt = $db->prepare("SELECT * FROM roo_priorities WHERE priority_id=$pid");
        $stmt->execute();
        $myrow = $stmt->fetch();

		echo '<p><a href="#" class="m-btn mini red-stripe" style="display: block;">PRIORITY DETAILS</a></p>';
		echo '<form method="post" action="">';
			echo '<input name="priority_id" value="' . $myrow['priority_id'] . '" type="hidden">';
			echo '<p>
				<input name="priority_name" value="' . $myrow['priority_name'] . '" placeholder="Priority Name" type="text" class="m-wrap"> 
				<input type="submit" name="priority_update" value="Update priority" class="m-btn blue"> 
				<input type="submit" name="priority_delete" value="Delete priority" class="m-btn">';
			echo '<p>';
		echo '</form>';
	}
	?>

	<div><a href="#" class="m-btn mini red-stripe" style="display: block;">PRIORITIES</a></div>

	<?php
    $res = $db->query("SELECT * FROM roo_priorities ORDER BY priority_name ASC");

	echo '<br>';
	echo '<table id="tableCategories" class="stripeMe" summary="projects table">';
	echo '<tbody>';
	foreach($res as $myrow) {
		echo '<tr>';
			echo '<td>';
				echo '<a href="settings-priorities.php?pid=' . $myrow['priority_id'] . '" class="bookmark"><strong>' . $myrow['priority_name'] . '</strong></a>';
			echo '</td>';
		echo '</tr>';
	}

	echo '</tbody>';
	echo '</table>';
	echo '<br>';

	// NEW PRIORITY
	echo '<form method="post" action="">';
		echo '<p><a href="#" class="m-btn mini red-stripe" style="display: block;">ADD NEW PRIORITY</a></p>';
		echo '<p><input name="priority_name" placeholder="Priority Name" type="text" class="m-wrap"> <input type="submit" name="priority_add" value="Add new priority" class="m-btn blue"></p>';
	echo '</form>';
	?>
</div>

<?php include('includes/bottom.php');?>
