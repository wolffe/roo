<?php
date_default_timezone_set('Europe/Dublin');
include('includes/functions.php');

if(!is_authed()) {
	header('Location: home.php');
}

if(isset($_SESSION['username'])) {
    $stmt = $db->prepare("SELECT * FROM user WHERE username='" . $_SESSION['username'] . "' LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch();

	$usertype = $row['plan'];
	$upp = $row['upp'];
	$useremail = $row['email'];
	$currency = $row['currency'];
    $userid = $row['userid'];
    $currentuser = $_SESSION['username'];

    // get and set the timezone
    if(empty($row['timezone'])) {
        date_default_timezone_set('Europe/Dublin');
        $timezone = 'Europe/Dublin';
    }
    else {
        date_default_timezone_set($row['timezone']);
        $timezone = $row['timezone'];
    }
}
?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $org_title; ?></title>

<link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,700|Ubuntu+Mono:400,700" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">

<link href="<?php echo ROOPATH; ?>/themes/default/pure.css" rel="stylesheet">
<link href="<?php echo ROOPATH; ?>/themes/default/default.css" rel="stylesheet">

<script src="js/Chart.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>

<script src="<?php echo ROOPATH; ?>/js/chat.js"></script>
<script src="<?php echo ROOPATH; ?>/js/roo-functions.js"></script>
<script src="<?php echo ROOPATH; ?>/js/facebox.js"></script>

<link href="<?php echo ROOPATH; ?>/css/facebox.css" media="screen" rel="stylesheet" type="text/css">

<!-- Date Picker -->
<script src="<?php echo ROOPATH; ?>/js/datepicker/jquery.simple-dtpicker.js"></script>
<link href="<?php echo ROOPATH; ?>/js/datepicker/jquery.simple-dtpicker.css" rel="stylesheet">

<!-- Chosen -->
<script src="<?php echo ROOPATH; ?>/js/chosen/chosen.jquery.js"></script>
<link href="<?php echo ROOPATH; ?>/js/chosen/chosen.css" rel="stylesheet">

<!-- Colorbox -->
<link rel="stylesheet" type="text/css" href="js/colorbox/colorbox.css">
<script src="js/colorbox/jquery.colorbox.1.3.32.js"></script>
<script>
$(document).ready(function(){
$("a[rel*=lightbox]").colorbox({speed:350,initialWidth:"500",initialHeight:"400",maxWidth:"100%",maxHeight:"100%",opacity:0.2,loop:false,scrolling:false,escKey:false,arrowKey:false,top:false,right:false,bottom:false,left:false});
	$(".attachment").colorbox({innerWidth:"70%",innerHeight:"90%",initialWidth:"300",initialHeight:"100",opacity:0.55,overlayClose:false,iframe:true});
});
</script>

<script>
function openpopup4(myarg) {
	var popurl = 'popoutchat.php?user='+myarg;

	winpops = window.open(popurl, '', 'width=275,height=500');
} 


$(document).ready(function() {
	$('a[rel*=facebox]').facebox({
		loadingImage : 'images/loading.gif',
		closeImage   : 'images/closelabel.png'
	})

	$('#viaWindowOpen').click(function(ev){
		user = document.getElementById('viaWindowOpen').title.value;
		alert(user);
		window.open('popoutchat.php?user='+user+'','Chat','width=275,height=400');
		ev.preventDefault();
		return false;
	})

	$('a.bookmark').click(function(){
		$.get('setRead.php', {id: $(this).attr('id')});
	});

	$(".stripeMe tr").mouseover(function() {$(this).addClass("over");}).mouseout(function() {$(this).removeClass("over");});
	$(".stripeMe tr:even").addClass("alt");
})
</script>
</head>

<body>
<div id="header">
	<h3><b><?php echo $org_title; ?></b> <sup><?php echo $org_version; ?></sup></h3>
	<hr>

    <div class="pure-menu pure-menu-open pure-menu-horizontal">
        <ul>
            <li><a href="./">Dashboard</a></li>
            <li><a href="<?php echo ROOPATH; ?>/mod-projects.php">Projects</a></li>
            <li><a href="<?php echo ROOPATH; ?>/profile.php">Profile</a></li>
            <li><a href="<?php echo ROOPATH; ?>/logout.php"><i class="fa fa-sign-out"></i></a></li>
        </ul>
    </div>

    <nav>
		<?php if($usertype == 9) { ?>
            <hr>
            <div><small>
				<a href="<?php echo ROOPATH; ?>/admin.php">Users</a> &bull; 
				<a href="<?php echo ROOPATH; ?>/settings-priorities.php">Project Priorities</a> &bull; 
				<a href="<?php echo ROOPATH; ?>/settings-update-types.php">Update Types</a>
			</small></div>
		<?php } ?>
	</nav>
    <hr>

	<?php
    $res = $db->query("SELECT * FROM updates WHERE udeadline != '0000-00-00' AND type != '3' AND assignee = '$currentuser'");
    $itemsDeadline = $res->rowCount();
    if($itemsDeadline > 0) { ?>
        <div class="pure-button pure-button-error pure-button-xsmall pure-fullwidth">
            You have <b><?php echo $itemsDeadline; ?></b> deadline(s)! Check projects marked with a red exclamation mark.
        </div>
    <?php } ?>
</div>

<div id="content" class="roox">
