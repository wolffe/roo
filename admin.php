<?php
include('includes/top.php');

echo '<h2>Administration</h2>';

if(isset($usertype) && $usertype == 9) {
	$user = $_SESSION['username'];
	$stmt = $db->prepare("SELECT * FROM user WHERE username='$user'");
	$stmt->execute();
	$row = $stmt->fetch();
	$uid = $row['userid'];
	$plan = $row['plan'];

	$res = $db->query("SELECT * FROM data WHERE author = '$uid'");
	$accounts = $res->rowCount();
	$percentage = $accounts * 1;
	?>

	<h5><?php echo $org_title; ?> users</h5>

	<table width="100%" class="widefat">
		<thead>
			<th>User</th>
			<th>Plan</th>
			<th>Projects</th>
			<th>Actions</th>
		</thead>
	<?php
	$res = $db->query("SELECT * FROM user");
	foreach($res as $myrow) {
		$plan = $myrow['plan'];
		if($plan == 0) $account_type = 'Standard Plan';
		if($plan == 9) $account_type = '<b>Unlimited Plan</b>';

		if($myrow['registration_date'] == '0000-00-00 00:00:00') {
			$registration_date = 'alpha';
		}
		else {
			$registration_date = $myrow['registration_date'];
		}

		// begin projects check
		$res = $db->query("SELECT id FROM data WHERE author = '" . $myrow['userid'] . "'");
		$project_amount = $res->rowCount();
		// end projects check

		// begin uploads check
		$upload_size = 0;
		$path = getcwd();
		$res = $db->query("SELECT name FROM upload WHERE user = '" . $myrow['username'] . "'");
		foreach($res as $upload_row) {
			$upload_size = $upload_size + formatbytes("$path/temporary/full/" . $upload_row['name'], "MB");
		}
		// end uploads check

		echo '<tr>';
			echo '<td><b>' . $myrow['username'] . '</b><br><small>' . $myrow['email'] . '</small></td>';
			echo '<td>' . $account_type . '<br><small>' . $registration_date . '</small></td>';
			echo '<td>' . $project_amount . ' <small>(' . $upload_size . 'MB)</small></td>';
			echo '<td><a href="user-make-admin.php?id=' . $myrow['userid'] . '"><i class="fa fa-user-plus"></i> Make admin</a> | <a href="delete-user.php?id=' . $myrow['userid'] . '" onclick="return confirmLinkDropACC(this, \'delete this user?\')"><i class="fa fa-trash"></i> Delete</a></td>';
		echo '</tr>';
	}
	?>
	</table>
<?php } else { ?>
	<p>Access denied.</p>
<?php } ?>

<?php include('includes/bottom.php');?>
