<?php
include('includes/top.php');

$stmt = $db->prepare("SELECT * FROM user WHERE username='$currentuser'");
$stmt->execute();
$row = $stmt->fetch();

$fullname 	= $row['fullname'];
$username 	= $row['username'];
$password 	= $row['password'];
$email 		= $row['email'];
$fullname 	= $row['fullname'];
$isnotify 	= $row['isnotify'];
$upp 		= $row['upp'];

// BEGIN PLAN
$plan = $usertype;

$res = $db->query("SELECT * FROM data WHERE author = '$userid'");
$accounts = $res->rowCount();
$percentage = $accounts * 1;

if($plan == 0) {
	$account_type = '<strong>Standard Plan</strong>';
	$accounts_total = STANDARD_PLAN;
}
if($plan == 1) {
	$account_type = '<strong>Extended Plan</strong>';
	$accounts_total = EXTENDED_PLAN;
}
if($plan == 9) {
	$account_type = '<strong>Unlimited Plan</strong>';
	$accounts_total = UNLIMITED_PLAN;
}

$percentage = $accounts * (100/$accounts_total);
// END PLAN

$timezone = $row['timezone'];
//$tz = date_default_timezone_set($row['timezone']);

// begin uploads check
$upload_size = 0;
$path = getcwd();
$res = $db->query("SELECT name FROM upload WHERE user = '$username'");
foreach($res as $upload_row) {
    $upload_size = $upload_size + formatbytes("$path/temporary/full/" . $upload_row['name'], 'MB');
}
// end uploads check

if(isset($_POST['update'])) {
	$npassword1    = $_POST['npassword1'];
	$npassword2    = $_POST['npassword2'];
	$fullname      = $_POST['fullname'];
	$email         = $_POST['email'];
	$isnotify      = $_POST['isnotify'];
	$upp           = $_POST['upp'];
	$timezone      = $_POST['timezone'];

	$stmt = $db->prepare("UPDATE user SET fullname = '$fullname', email = '$email', isnotify = '$isnotify', upp = '$upp', timezone = '$timezone' WHERE username = '$currentuser' LIMIT 1");
	$stmt->execute();

	if($npassword1 != '') {
		if($npassword1 == $npassword2) {
			$npassword1 = hash_hmac('sha512', $npassword1, '#+pT%B[M1X3R),Z(Y+q_tZwX~l@DXum2PdHS8hvmaVENrmI_?cvE#j8]n^.u]Ni+'); // hash the new password
            $stmt = $db->prepare("UPDATE user SET password = '$npassword1' WHERE username = '$currentuser' LIMIT 1");
            $stmt->execute();
		}
		else {
			echo '<div class="error_message"><p>Passwords do not match!</p></div>';
		}
	}

    echo '<div class="confirm"><p>Settings updated!</p></div>';
}
?>

<h2>Your Profile</h2>
<p>
	<strong><?php echo $username; ?></strong><br>
	<small><?php echo $email; ?></small><br>
</p>
<p>
	You are using <strong><?php echo $percentage;?>%</strong> from your <?php echo $account_type;?> account! This means <strong><?php echo $accounts;?></strong>/<?php echo $accounts_total;?> projects!
	<?php if($accounts == $accounts_total) {?>
		<br>Your account is full! Do you want to <a href="">upgrade now</a>?
	<?php } ?>
    <br><small>You are using <?php echo $upload_size; ?>MB from your upload quota.</small>
</p>

<hr />

<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" class="pure-form pure-form-aligned">
    <fieldset>
        <div class="pure-control-group">
            <label for="fullname">Full name</label>
            <input type="text" name="fullname" id="fullname" value="<?php echo $fullname; ?>" placeholder="Full name (optional)">
        </div>
        <div class="pure-control-group">
            <label for="email">Email address</label>
            <input type="email" name="email" id="email" value="<?php echo $email; ?>" placeholder="Email address">
        </div>
        <div class="pure-control-group">
            <label for="upp">Updates/tasks per page</label>
			<select name="upp" id="upp">
				<option value="<?php echo $upp; ?>">Show <?php echo $upp; ?> updates per page</option>
				<option value="5">Show 5 updates per page</option>
				<option value="10">Show 10 updates per page</option>
				<option value="15">Show 15 updates per page</option>
				<option value="25">Show 25 updates per page</option>
				<option value="50">Show 50 updates per page</option>
			</select>
        </div>
        <div class="pure-control-group">
            <label for="isnotify">Email notifications</label>
			<select name="isnotify" id="isnotify">
				<option value="1"<?php if($isnotify == 1) echo ' selected'; ?>>Allow email notifications</option>
				<option value="0"<?php if($isnotify == 0) echo ' selected'; ?>>Do not allow email notifications</option>
			</select>
        </div>
        <div class="pure-control-group">
            <label for="timezone">Timezone/location</label>
			<select name="timezone" id="timezone">
				<?php
				$tzs = timezone_identifiers_list();
				echo '<option value="' . $timezone . '" selected="selected">' . $timezone . '</option>';    
				foreach($tzs as $x) {
					echo '<option value="' . $x . '">' . $x . '</option>';
				}
				?>
			</select> <small>Currently set as <strong><?php echo $timezone; ?></strong> - <?php echo date('Y-m-j H:i:s'); ?></small>
        </div>

        <div class="pure-control-group">
            <label for="npassword1">New password</label>
            <input type="password" name="npassword1" id="npassword1" placeholder="New password">
        </div>
        <div class="pure-control-group">
            <label for="npassword2">New password (again)</label>
            <input type="password" name="npassword2" id="npassword2" placeholder="New password (again)">
        </div>

        <div class="pure-controls">
            <input type="submit" name="update" value="Save changes" class="pure-button pure-button-primary">
        </div>
    </fieldset>
</form>
<?php include('includes/bottom.php');?>
