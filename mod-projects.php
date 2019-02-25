<?php include('includes/top.php');?>

<div>

<?php
// Show only open projects
// Select the projects which have only currently assigned users // Needs tweaking
$res = $db->query("SELECT * FROM data WHERE FIND_IN_SET('" . $userid . "', uids) AND priority != 'closed' ORDER BY lastmodified DESC");
foreach($res as $myrow) {
    $read_count = $res->rowCount();

	// BEGIN DEADLINE CHECK
	$hasDeadline = '';
    $res = $db->query("SELECT * FROM updates WHERE udeadline != '0000-00-00' AND type != 3 AND assignee = '$currentuser' AND projectid = '" . $myrow['id'] . "'");
    $itemsDeadline = $res->rowCount();
	if($itemsDeadline > 0)
		$hasDeadline = '<div class="pure-button pure-button-error pure-button-xsmall"><i class="fa fa-warning"></i> deadline</div>';
	// END DEADLINE CHECK

	echo '<div class="item-project">';
        echo '<mark class="' . strtolower($myrow['priority']) . ' pure-button pure-button-xsmall"><i class="fa fa-tags"></i> ' . strtolower($myrow['priority']) . '</mark> ';

        echo $hasDeadline . ' ';

        // ribbons
        $date1 = date('Y-m-d', strtotime($myrow['lastmodified']));
        $date2 = date('Y-m-d');
        $totaldays = abs(daysDifference($date1, $date2));
        if($totaldays > 30) echo ' <div class="pure-button pure-button-secondary pure-button-xsmall"><i class="fa fa-clock-o"></i> idle</div>';
        if($read_count == 0) echo ' <div class="pure-button pure-button-error pure-button-xsmall"><i class="fa fa-exclamation"></i> new</div>';
        if($myrow['projecttype'] == 1) echo ' <div class="ribbon ribbon-green">invoice</ribbon>';

        if($read_count == 0) echo ' <a href="view.php?id=' . $myrow['id'] . '" class="bookmark" id="bookmark_' . $myrow['id'] . '"><b>' . $myrow['name'] . '</b></a> ';
        else echo ' <a href="view.php?id=' . $myrow['id'] . '" class="bookmark" id="bookmark_' . $myrow['id'] . '">' . $myrow['name'] . '</a> ';

        $res = $db->query("SELECT * FROM updates WHERE projectid = '".$myrow['id']."' AND type != 3 AND type != 0 AND hidden != 1");
        $update_count = $res->rowCount();

        echo '<br><small style="color: #444444; font-size: 12px;" class="pure-hide note">' . $update_count . ' active updates | <span class="dataLastModified">Last modified on ' . date('d/m/Y', strtotime($myrow['lastmodified'])) . '</span></small>';
    echo '</div>';
}
echo '<table id="tableCategories" class="stripeMe" summary="projects table">';
echo '<tr><td colspan="4" class="intermission">&nbsp;</td></tr>';
echo '<tr class="alt"><th>Priority</th><th>Name</th><th class="aligncenter">Archived</th></tr>';

// Show only closed projects
$res = $db->query("SELECT * FROM data WHERE FIND_IN_SET('" . $userid . "',uids) AND priority = 'Closed' ORDER BY lastmodified DESC");
foreach($res as $myrow) {
	echo '<tr>';
	echo '<td class="priority-wrap"><mark class="' . strtolower($myrow['priority']) . '">' . strtolower($myrow['priority']) . '</mark></td>';
	echo '<td><a href="view.php?id='.$myrow['id'].'">'.$myrow['name'].'</a>';

    $res = $db->query("SELECT * FROM updates WHERE projectid = '".$myrow['id']."'");
    $update_count = $res->rowCount();
	echo ' <small style="color: #444444; font-size: 12px;" class="pure-hide note">('.$update_count.' updates)<small>';

	echo '</td><td class="dataLastModified aligncenter">';
	echo date('d/m/Y', strtotime($myrow['lastmodified']));
	echo '</td>';
}
echo '</tbody>';
echo '<tfoot><tr class="alt"><th>Priority</th><th>Name</th><th class="aligncenter">Archived</th><th></th></tr></tfoot>';
echo '</table>';
?>

</div>

<?php include('includes/bottom.php');?>
