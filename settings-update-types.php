<?php include('includes/top.php');?>

<div>
	<?php
	if(isset($_POST['type_add'])) {
		$update_type_name = $_POST['update_type_name'];
		$update_type_colour = $_POST['update_type_colour'];

        $stmt = $db->prepare("INSERT INTO update_types (update_type_name, update_type_colour) VALUES ('$update_type_name', '$update_type_colour')");
        $stmt->execute();
		echo '<div><a href="#" class="m-btn mini green-stripe"">Update type added successfully!</a></div><br>';
	}
	if(isset($_POST['type_update'])) {
		$update_type_id = $_POST['update_type_id'];
		$update_type_name = $_POST['update_type_name'];
		$update_type_colour = $_POST['update_type_colour'];

        $stmt = $db->prepare("UPDATE update_types SET update_type_name='$update_type_name', update_type_colour='$update_type_colour' WHERE update_type_id='$update_type_id' LIMIT 1");
        $stmt->execute();
		echo '<div><a href="#" class="m-btn mini green-stripe"">Update type updated successfully!</a></div><br>';
	}
	if(isset($_POST['type_delete'])) {
		$update_type_id = $_POST['update_type_id'];

        $stmt = $db->prepare("DELETE FROM update_types WHERE update_type_id='$update_type_id' LIMIT 1");
        $stmt->execute();
		header('Location: ' . $_SERVER['PHP_SELF']);
	}
	if(isset($_GET['tid'])) {
		$tid = mysql_real_escape_string($_GET['tid']);

        $stmt = $db->prepare("SELECT * FROM update_types WHERE update_type_id=$tid");
        $stmt->execute();
        $myrow = $stmt->fetch();

		echo '<p><a href="#" class="m-btn mini red-stripe" style="display: block;">UPDATE TYPE DETAILS</a></p>';
		echo '<form method="post" action="">';
			echo '<input name="update_type_id" value="' . $myrow['update_type_id'] . '" type="hidden">';
			echo '<p>
				<input name="update_type_name" value="' . $myrow['update_type_name'] . '" placeholder="Type Name" type="text" class="m-wrap"> 
				<select name="update_type_colour" class="m-wrap">
					<option>' . $myrow['update_type_colour'] . '</option>
					<option>blue</option>
					<option>red</option>
					<option>purple</option>
					<option>black</option>
					<option>green</option>
				</select>
				<input type="submit" name="type_update" value="Update type" class="m-btn blue"> 
				<input type="submit" name="type_delete" value="Delete type" class="m-btn">';
			echo '<p>';
		echo '</form>';
	}
	?>

	<div><a href="#" class="m-btn mini red-stripe" style="display: block;">PRIORITIES</a></div>

	<?php
    $res = $db->query("SELECT * FROM update_types ORDER BY update_type_name ASC");

	echo '<br>';
	echo '<table id="tableCategories" class="stripeMe" summary="projects table">';
	echo '<tbody>';
    foreach($res as $myrow) {
		echo '<tr>';
			echo '<td>';
				echo '<span class="m-btn mini ' . $myrow['update_type_colour'] . '">&nbsp;</span> <a href="settings-update-types.php?tid=' . $myrow['update_type_id'] . '" class="bookmark"><strong>' . $myrow['update_type_name'] . '</strong></a>';
			echo '</td>';
		echo '</tr>';
	}

	echo '</tbody>';
	echo '</table>';
	echo '<br>';

	// NEW PRIORITY
	echo '<form method="post" action="">';
		echo '<p><a href="#" class="m-btn mini red-stripe" style="display: block;">ADD NEW UPDATE TYPE</a></p>';
		echo '<p>
			<input name="update_type_name" placeholder="Update Type Name" type="text" class="m-wrap"> 
			<select name="update_type_colour" class="m-wrap">
				<option>Update Type Colour</option>
				<option>blue</option>
				<option>red</option>
				<option>purple</option>
				<option>black</option>
				<option>green</option>
			</select>
			<input type="submit" name="type_add" value="Add new type" class="m-btn blue">
		</p>';
	echo '</form>';
	?>
</div>

<?php include('includes/bottom.php');?>
