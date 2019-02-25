<?php
include('includes/top.php');

$id = rawurlencode($_GET['id']);

$stmt = $db->prepare("SELECT * FROM data WHERE id='" . $id . "' LIMIT 1");
$stmt->execute();
$myrow = $stmt->fetch();

$stmt = $db->prepare("INSERT INTO flags (user_id, project_id, flag) VALUES ($userid, $id, 1)");
$stmt->execute();

if(isset($_POST['submit_update'])) {
	$projectid 	= $_POST['projectid'];
	$roo_dropbox_file = $_POST['selected-file'];
	if(!empty($_POST['udeadline'])) {
		$udeadline = date('Y-m-d', strtotime($_POST['udeadline']));
	}
	else {
		$udeadline = '0000-00-00';
	}

	$udate 		= date('Y-m-j H:i:s');
	$uuser 		= $currentuser;

	$usubject 	= addslashes($_POST['usubject']); // escape quotes
	if($usubject == '') $usubject = 'No subject';
	$udescription = addslashes($_POST['udescription']); // escape quotes
	if(!empty($roo_dropbox_file))
		$udescription .= '<p>' . $roo_dropbox_file . ' <small>(via <b>Dropbox</b>)</small></p>';

	$type 		= $_POST['type'];
	$assignee 	= $_POST['assignee'];

	$notification_reason = '<strong>'.$uuser.'</strong> posted a new update: <strong>'.$usubject.'</strong>.<br /><br /><em>'.$udescription.'</em>';

	include('notify.php');

    $stmt = $db->prepare("INSERT INTO updates (projectid, udate, udeadline, usubject, udescription, uuser, type, assignee, parent, hidden) VALUES ('$projectid', '$udate', '$udeadline', '$usubject', '$udescription', '$currentuser', '$type', '$assignee', 0, 0)");
    $stmt->execute();
    $last_id = $db->lastInsertId(); // get last update/reply ID

	// Update last modified date
	$lm = date('Y-m-j H:i:s');
    $stmt = $db->prepare("UPDATE data SET lastmodified = '$lm' WHERE id = '$id'");
    $stmt->execute();

    // begin multiple file upload
    $number_of_file_fields = 0;
    $number_of_uploaded_files = 0;
    $number_of_moved_files = 0;
    $uploaded_files = array();
    $upload_directory = dirname(__file__) . '/temporary/full/'; //set upload directory
    /**
     * we get a $_FILES['images'] array, we process this array while iterating with simple for loop 
     * you can check this array by print_r($_FILES['images']); 
     */

    for($i = 0; $i < count($_FILES['file']['name']); $i++) {
        $number_of_file_fields++;
        $u = uniqid();
        if($_FILES['file']['name'][$i] != '') { //check if file field empty or not
            $number_of_uploaded_files++;

            $attachmentname = $_FILES['file']['name'][$i];

            $attachmentname = trim($attachmentname);
            $attachmentname = str_replace(' ', '-', $attachmentname);
            $attachmentname = str_replace('&', '-', $attachmentname);
            $attachmentname = str_replace(';', '-', $attachmentname);
            $attachmentname = strtolower($attachmentname);

            $path_parts = pathinfo($upload_directory.$attachmentname);

            $uploaded_files[] = $attachmentname;
            if(move_uploaded_file($_FILES['file']['tmp_name'][$i], $upload_directory.$attachmentname)) {
                $number_of_moved_files++;
            }
        }
        if($number_of_uploaded_files > 0) {
            $stmt = $db->prepare("INSERT INTO upload (name, user, project_id_fk, reply_id_fk) VALUES ('" . $attachmentname . "', '$currentuser', '" . $projectid . "', '" . $last_id . "')");
            $stmt->execute();
        }
    }
    // end multiple file upload

	echo '<div class="confirm">Update posted!</div>';
}

if(isset($_POST['submit_reply'])) {
	$parent = $_POST['parent'];
	$projectid = $_POST['projectid'];
	$udate = date('Y-m-j H:i:s');
	$uuser = $currentuser;

	$udescription = addslashes($_POST['rdescription']); // escape quotes

	$notification_reason = '<strong>'.$uuser.'</strong> replied to an update.<br /><br /><em>'.$udescription.'</em>';

	include('notify.php');

    $stmt = $db->prepare("INSERT INTO updates (projectid, udate, usubject, udescription, uuser, parent) VALUES ('$projectid', '$udate', 'Reply', '$udescription', '$currentuser', '$parent')");
    $stmt->execute();

	// Mark current project as read
    $res = $db->query("SELECT * FROM flags WHERE user_id = '$userid' AND project_id = '".$myrow['id']."'");
    $read_count = $res->rowCount();
	if($read_count > 0) {
        $stmt = $db->prepare("UPDATE flags SET flag = '0' WHERE project_id = '$id' AND user_id != '$userid'");
        $stmt->execute();
    }
	else {
        $stmt = $db->prepare("INSERT INTO flags (user_id, project_id, flag) VALUES ('$userid', '$id', '0')");
        $stmt->execute();
    }

	// Update last modified date
	$lm = date('Y-m-j H:i:s');
    $stmt = $db->prepare("UPDATE data SET lastmodified = '$lm' WHERE id = '$id'");
    $stmt->execute();

	echo '<div class="confirm">Reply posted!</div>';
}

echo '<h2 style="padding-bottom: 0; margin-bottom: 16px; font-size: 20pt; line-height: 26px;">' . $myrow['name'] . '</h2>';
?>

<p><small><i class="fa fa-clock-o"></i> Created on <?php echo $myrow['date']; ?> | Last modified on <?php echo $myrow['lastmodified']; ?></small></p>

<div>
	<?php echo '<mark class="' . strtolower($myrow['priority']) . ' masterTooltip pure-button pure-button-xsmall" id="editme3" rel="' . $myrow['id'] . '" title="Click to edit"><i class="fa fa-star"></i> ' . strtolower($myrow['priority']) . '</mark>'; ?>

    <a href="view.php?id=<?php echo $myrow['id']; ?>" class="pure-button pure-button-xsmall"><i class="fa fa-refresh"></i></a>

	<a href="view.php?id=<?php echo $myrow['id']; ?>&amp;archive" class="pure-button pure-button-xsmall"><i class="fa fa-archive"></i> View Archive</a>
	<a href="addedit.php?id=<?php echo $myrow['id']; ?>" class="pure-button pure-button-xsmall"><i class="fa fa-users"></i> Manage Members</a>
	<a href="delete.php?id=<?php echo $myrow['id']; ?>" class="pure-button pure-button-xsmall pure-button-error" onclick="return confirmLinkDropACC(this, 'delete this project?')"><i class="fa fa-times"></i> Delete Project</a>
</div>
<hr>




<div class="update">
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?id=<?php echo $id; ?>" class="pure-form" enctype="multipart/form-data">
        <p><i class="fa fa-file-o"></i> Add New Task/Update</p>
		<input type="hidden" name="projectid" value="<?php echo $id; ?>">	

        <div class="update-align-left">
            <p><input type="text" name="usubject" id="usubject" class="pure-input-1" placeholder="Subject/Title"></p>
            <p><textarea name="udescription" class="udescription pure-input-1" rows="8"></textarea></p>
            <p><i class="fa fa-upload"></i> <input name="file[]" type="file" multiple></p>
        </div>

        <div class="update-align-right">
            <p>
				<select name="type" id="type">
                    <?php
                    $res = $db->query("SELECT * FROM update_types");
                    foreach($res as $type_row) {
                        $type_id = $type_row['update_type_id'];
                        $type_name = $type_row['update_type_name'];
                        ?>
                        <option value="<?php echo $type_id; ?>">Mark as <?php echo $type_name; ?></option>
                    <?php } ?>
                </select>
            </p>

            <p>
                <select name="assignee" id="assignee">
                    <option value="">Assign to...</option>
                    <option value="all">All</option>
                    <?php
                    $uid = explode(',', $myrow['uids']);
                    $users = count($uid);
                    for($i=0; $i<$users; $i++) {
                        $stmt = $db->prepare("SELECT username FROM user WHERE userid='" . $uid[$i] . "'");
                        $stmt->execute();
                        $myrow2 = $stmt->fetch();
                        if($myrow2['username'] != '') {
                            echo '<option>' . $myrow2['username'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </p>
            <p><input type="date" name="udeadline" id="udeadline"> Deadline</p>
            <p>
                <!-- https://www.dropbox.com/developers/chooser -->
                <script src="https://www.dropbox.com/static/api/1/dropbox.js" id="dropboxjs" data-app-key="<?php echo $org_db_key; ?>"></script>
                <input type="dropbox-chooser" name="selected-file" style="visibility: hidden;">
            </p>

            <p><input type="submit" name="submit_update" value="Add Task/Update" class="pure-button pure-button-primary"></p>
        </div>
        <br clear="all">
	</form>
</div>







<!-- BEGIN TASK/UPDATE LISTING -->
<div id="listingAJAX">
	<?php
    // OOH! Pagination! Sweet!
    if(!isset($_GET['startrow']))
        $startrow = 0; // we give the value of the starting row 0 because nothing was found in the URL
    else
        $startrow = (int)$_GET['startrow']; //otherwise we take the value from the URL

    if(isset($_GET['archive']))
        $res = $db->query("SELECT * FROM updates WHERE projectid='" . $id . "' AND parent='0' ORDER BY udate DESC");
    else
        $res = $db->query("SELECT * FROM updates WHERE projectid='" . $id . "' AND parent='0' AND hidden='0' ORDER BY udate DESC");
	$updates = $res->rowCount();

	// SHOW UPDATES (AND HIDDEN REPLIES)
    foreach($res as $urow) {
        $stmt = $db->prepare("SELECT * FROM update_types WHERE update_type_id=" . $urow['type'] . "");
        $stmt->execute();
        $type_row = $stmt->fetch();

		$type_name = strtolower($type_row['update_type_name']);
		$type_colour = $type_row['update_type_colour'];

		$flag = '<mark style="background-color: ' . $type_colour . '; color: #ffffff;" class="' . $type_colour . ' uppercase">' . $type_name . '</mark> ';
		$flag_task = $type_colour;

        if($type_name == 'done') {
            $opacity_style = 'style="opacity:0.5;"';
        } else {
            $opacity_style = '';
        }

		echo '<div class="listing clearfix unread active u' . $urow['updateid'] . '" ' . $opacity_style . '>';
            echo '<a name="reply' . $urow['updateid'] . '"></a>';

            if($urow['udeadline'] == '0000-00-00') $status = 'blue-stripe';
			if($urow['udeadline'] != '0000-00-00') $status = 'red-stripe';

            // show replies // child updates //
            $res = $db->query("SELECT * FROM updates WHERE parent='" . $urow['updateid'] . "' ORDER BY updateid DESC");
			$replies = $res->rowCount();

            echo '<div class="h3toggle pure-button pure-fullwidth" style="cursor:pointer; text-align: left;">';
                echo '<div class="meta">';
                    echo '<a class="m-btn mini '.$status.'">';
                    if($urow['assignee'] != '') echo '<b>' . $urow['uuser'] . '</b> &raquo; <b>' . $urow['assignee'] . '</b> | ';
                    else echo '<b>' . $urow['uuser'] . '</b> | ';
                    echo '<span title="' . $urow['udate'] . '">' . nicetime($urow['udate']) . '</span>';
                    echo '</a>';
                echo '</div>';

                echo '<div class="shy">';
                    if($type_name != 'done')
                        echo '<a href="#" onclick="updateComplete(' . $urow['updateid'] . '); return false;"><i class="fa fa-check-circle-o fa-fw" title="Set as done"></i></a>';
                    else
                        echo '<a href="#" onclick="return false;"><i class="fa fa-circle-o fa-fw" title="Done"></i></a>';

                    if($urow['udeadline'] != '0000-00-00') // updateBump
                        echo '<a href="#" onclick="return false;"><i class="fa fa-clock-o fa-fw" title="Deadline set for ' . $urow['udeadline'] . '"></i></a>';
                    else
                        echo '<a href="#" onclick="return false;"><i class="fa fa-circle-o fa-fw" title="No deadline"></i></a>';

                    echo '<a href="#" onclick="updateArchive('.$urow['updateid'].'); return false;"><i class="fa fa-archive fa-fw" title="Archive"></i></a>';
                echo '</div>';

                echo '<span class="' . $status . '" style="font-size: 14px;">' . stripslashes($urow['usubject']) . ' (' . $replies . ') ' . $flag . '<span class="flagMe"></span></span>';

            echo '</div>';
			?>
			<div class="reply<?php echo $urow['updateid'];?> hideMe">
			<?php
			if($urow['udeadline'] != '0000-00-00' && $urow['type'] != '3') echo '<span class="deadline">Deadline is set for <strong>'.date('d/m/Y', strtotime($urow['udeadline'])).'</strong></span><br><br>';

            $res = $db->query("SELECT * FROM upload WHERE reply_id_fk = " . $urow['updateid'] . "");
            foreach($res as $attachments) {
                $link = 'temporary/full/' . $attachments['name'];

                echo '<div class="ratt" id="a' . $attachments['id'] . '"><i class="fa fa-file"></i> <a href="' . $link . '" class="attachment" rel="facebox"><small>' . $attachments['name'] . '</small></a></div>';
            }

            echo '<br clear="all">';
            echo '<div class="l-entry">';
				echo roo_autop($urow['udescription']);
            echo '</div>';
			?>

			<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?id=<?php echo $id; ?>" id="myform" name="myform" class="pure-form">
				<input type="hidden" name="projectid" value="<?php echo $myrow['id']; ?>">
				<input type="hidden" name="uuser" value="<?php echo $urow['uuser']; ?>">
				<input type="hidden" name="parent" value="<?php echo $urow['updateid']; ?>">
				<p>
					<textarea name="rdescription" class="rdescription" rows="3" placeholder="Reply..."></textarea>
				</p>
				<p class="button_block">
					<input type="submit" name="submit_reply" value="Reply" class="pure-button pure-button-xsmall pure-button-primary">
				</p>
			</form>

            <?php
			// show replies // child updates //
            $res = $db->query("SELECT * FROM updates WHERE parent='" . $urow['updateid'] . "' ORDER BY updateid DESC");
            foreach($res as $replyrow) {
				echo '<div class="listing clearfix reply active">';
					echo '<a name="reply'.$replyrow['updateid'].'"></a>';
					echo '<span class="reply-status"></span>';
					echo '<p>';
                        echo '<strong><a href="#">' . $replyrow['uuser'] . '</a></strong> ';

                        echo '<small title="' . $replyrow['udate'] . '">' . nicetime($replyrow['udate']) . '</small><br><br>';
                        $text = preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '<a href="$1" rel="external" target="_blank">$1</a>', $replyrow['udescription']);
						echo nl2br(smilieMe($text));

						echo '<br clear="all">';
					echo '</div>';
				}

				// reply to updates //
			echo '</div>';
			echo '</div>';
		}
		?>
	</div>
<!-- END TASK/UPDATE LISTING -->

<!-- BEGIN MEDIA LIBRARY -->
<div>
	<div class="right-sidebar a" style="overflow: overlay;">
		<p><i class="fa fa-list"></i> Media Library</p>
		<div id="attach"></div>

		<script>
		$('#attach').load('_attachments.php', {pid: <?php echo $id; ?>}).fadeIn('slow');
		</script>
	</div>
</div>
<!-- END MEDIA LIBRARY -->

<?php include('includes/bottom.php');?>
