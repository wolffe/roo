<?php include('includes/top.php'); ?>

<?php
// BEGIN PLAN
$res = $db->query("SELECT id FROM data WHERE author = '$userid'");
$accounts = $res->rowCount();
$percentage = $accounts * 1;

if(isset($usertype) && $usertype == 0) {
    $account_type = '<strong>Standard Plan</strong>';
    $accounts_total = STANDARD_PLAN;
}
if(isset($usertype) && $usertype == 1) {
    $account_type = '<strong>Extended Plan</strong>';
    $accounts_total = EXTENDED_PLAN;
}
if(isset($usertype) && $usertype == 9) {
    $account_type = '<strong>Unlimited Plan</strong>';
    $accounts_total = UNLIMITED_PLAN;
}

$percentage = $accounts * (100/$accounts_total);
// END PLAN
?>

<p class="pure-button pure-fullwidth">
    <small>You are using <b><?php echo $percentage; ?>%</b> from your <?php echo $account_type; ?> account! This means <b><?php echo $accounts; ?></b>/<?php echo $accounts_total; ?> projects! <?php if($accounts == $accounts_total) { echo 'Your account is full! Purchase a new project from below!'; } ?></small>
</p>

<div class="pure-g">
    <div class="pure-u-1">
        <p>
            <span class="pure-button">Welcome <b><?php echo $currentuser; ?></b></span>
    
            <?php if($accounts < $accounts_total) { ?>
                <a class="pure-button pure-button-warning" href="addedit.php"><i class="fa fa-plus"></i> Create free project</a>
            <?php } else { ?>
                <a class="pure-button pure-button-warning pure-button-disabled" href="#"><i class="fa fa-plus"></i> Create free project</a>
            <?php } ?>
    
            <?php if($usertype == 9) { ?>
                <a class="pure-button pure-button-warning" href="addedit.php"><i class="fa fa-th-list"></i> Create a Project <small>ADMIN</small></a>
            <?php } ?>
        </p>
    </div>
</div>

<div>
	<p>
        <small>If you are new to <b><?php echo $org_title; ?></b>, please <a href="profile.php">update your profile</a>.</small>
        <br>

        <?php
		$script_tz = date_default_timezone_get();
		$ini_tz = ini_get('date.timezone');
		$ini_tz = empty($ini_tz) ? 'Europe/Dublin' : $ini_tz;

		if(empty($script_tz) || !isset($script_tz))
			echo '<small>Your profile timezone is empty! Please fix it!</small>';
		else
			echo '<small>Your profile timezone is <b>' . $script_tz . ' (' . $timezone . ')</b> (<b>' . $org_title . '</b>: <b>' . $ini_tz . '</b>)</small>';
		?>
	</p>
</div>

<?php include('includes/bottom.php'); ?>
