<?php
include('includes/top.php');
$currentDate = date("Y-m-j H:i:s");
$currentuser = $_SESSION['username'];

$stmt = $db->prepare("SELECT userid FROM user WHERE username='$currentuser' LIMIT 1");
$stmt->execute();
$row = $stmt->fetch();

$uid = $row['userid'];

if(isset($_POST['addme'])) {
	$membername = $_POST['membername'];
	$projectid = $_POST['projectid'];

    $res = $db->query("SELECT * FROM user WHERE username='".$membername."'");
    $howmany = $res->rowCount();

    $stmt = $db->prepare("SELECT * FROM user WHERE username='".$membername."'");
    $stmt->execute();
    $row = $stmt->fetch();

	if($howmany != '0') {
        $stmt = $db->prepare("SELECT * FROM data WHERE id='".$projectid."'");
        $stmt->execute();
        $row1 = $stmt->fetch();

        $tokeep = $row1['uids'];
		$toadd = $tokeep.','.$row['userid'];

        $stmt = $db->prepare("UPDATE data SET uids='$toadd' WHERE id='$projectid'");
        $stmt->execute();

		// update last modified date for current project
		$currentDate = date("Y-m-j H:i:s");

		echo '<div class="confirm"><strong>'.$row['username'].'</strong> has been successfully added to the project!<br /><br /><strong>'.$row['username'].'</strong> will now be able to view and modify the current project.</div>';
		echo '<p><a href="view.php?id='.$projectid.'">Back to project</a></p>';
	}
	else {
		echo '<p>No such member!</p>';
		echo '<p><a href="view.php?id='.$projectid.'">Back to project</a></p>';
	}
}

if(isset($_POST['submit'])) {
	$currentDate = date("Y-m-j H:i:s");

	$date = $_POST['date'];
	$name = $_POST['name'];
	$projecttype = $_POST['projecttype'];

    $stmt = $db->prepare("INSERT INTO data (date, projecttype, name, lastmodified, author, uids) VALUES ('$date', '$projecttype', '$name', '$currentDate', '$uid', '$uid')");
    $stmt->execute();

	echo '<div class="confirm">Item added!</div>';
}
else if(isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT * FROM data WHERE id='" . $_GET['id'] . "'");
    $stmt->execute();
    $myrow = $stmt->fetch();
	?>
	<h2>Manage Project Members</h2>
	<p>Add a <b><?php echo $org_title; ?></b> member to this project</p>

	<form method="post" action="" class="pure-form">
        <fieldset>
            <input type="hidden" name="projectid" value="<?php echo $myrow['id']; ?>">
            <input type="text" name="membername" id="membername" placeholder="<?php echo $org_title; ?> username">
            <input type="submit" name="addme" value="Add member" class="pure-button pure-button-primary"> <small>(should already be a <b><?php echo $org_title; ?></b> user)</small>
        </fieldset>
	</form>

		<br>
		<h2>Members and notifications</h2>
		<p><small>The following users are members of this project. They will be notified when changes to the project occur, according to their preferences.</small></p>
		<?php
        $stmt = $db->prepare("SELECT * FROM data WHERE id='".$myrow['id']."'");
        $stmt->execute();
        $myrow1 = $stmt->fetch();

		$uid = explode(',', $myrow['uids']);
		$users = count($uid);
		for($i=0; $i<$users; $i++) {
            $stmt = $db->prepare("SELECT * FROM user WHERE userid='".$uid[$i]."'");
            $stmt->execute();
            $myrow2 = $stmt->fetch();

            if($myrow2['username'] != '') {
				echo '<a href="javascript:void(0)" onclick="javascript:chatWith(\''.$myrow2['username'].'\')"><i class="fa fa-comments"></i></a> ';

				if($myrow2['isnotify'] == '0')
					echo '<em title="This member has turned off notifications!" style="cursor: help">'.$myrow2['username'].'</em>';
				else
					echo $myrow2['username'];
				echo ' <a href="members-remove.php?userid='.$myrow2['userid'].'&amp;pid='.$myrow['id'].'" style="float:right"><i class="fa fa-trash"></i></a>';
				echo '<br style="clear:both" />';
			}
		}
		?>
	<?php
}
else {
	$user = $_SESSION['username'];
	?>
	<h2>Add new project</h2>

	<form method="post" action="addedit.php" class="pure-form pure-form-stacked">
		<input type="hidden" name="user" value="<?php echo $user; ?>">
		<input type="hidden" name="date" value="<?php echo date('Y-m-d'); ?>">
		<input type="hidden" name="projecttype" value="0">

        <?php
        // BEGIN PLAN
        $res = $db->query("SELECT id FROM data WHERE author = '$userid'");
        $accounts = $res->rowCount();
        $percentage = $accounts * 1;
    
        if($usertype == 0) {
            $account_type = '<strong>Standard Plan</strong>';
            $accounts_total = STANDARD_PLAN;
        }
        if($usertype == 1) {
            $account_type = '<strong>Extended Plan</strong>';
            $accounts_total = EXTENDED_PLAN;
        }
        if($usertype == 9) {
            $account_type = '<strong>Unlimited Plan</strong>';
            $accounts_total = UNLIMITED_PLAN;
        }
    
        $percentage = $accounts * (100/$accounts_total);
        // END PLAN
    
        ?>
    
        <?php if($accounts < $accounts_total) { ?>
            <p><input type="text" name="name"> <input type="submit" name="submit" value="Add Project" class="pure-button pure-button-primary"></p>
        <?php } else { ?>
            <p>
                <input type="text" name="name"> <input type="button" name="submit-disabled" value="Your account is full! Purchase a new project!" class="pure-button pure-button-primary pure-button-disabled">
            </p>
        <?php } ?>
        <?php if($usertype == 9) { ?>
            <p><input type="submit" name="submit" value="Add Project (Administrator only)" class="pure-button pure-button-primary"></p>
        <?php } ?>
	</form>
<?php
}

include('includes/bottom.php');
?>
