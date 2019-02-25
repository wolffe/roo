<?php date_default_timezone_set('Europe/Dublin'); ?>
<?php include('includes/functions.php'); ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $org_title; ?></title>

<link href="https://fonts.googleapis.com/css?family=Ubuntu:300,400,700|Ubuntu+Mono:400,700" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet">

<link href="themes/default/pure.css" rel="stylesheet">
<link href="themes/default/default.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $('#tabby').each(function(){
        var $active, $content, $links = $(this).find('a');

        $active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
        $active.addClass('pure-button-secondary');
        $content = $($active.attr('href'));

        $links.not($active).each(function () {
            $($(this).attr('href')).hide();
        });

        $(this).on('click', 'a', function(e){
            $active.removeClass('pure-button-secondary');
            $content.hide();

            $active = $(this);
            $content = $($(this).attr('href'));

            $active.addClass('pure-button-secondary');
            $content.show();

            e.preventDefault();
        });
    });
});
</script>
</head>
<body class="page-home">

<div class="home">
    <h3><b><?php echo $org_title; ?></b></h3>

    <!-- MAIN TABBED MENU -->
    <div class="" id="tabby">
        <a href="#login" class="pure-button">Log in</a>
        <a href="#register" class="pure-button">Register</a>
    </div>
    <br>

    <?php
    if(isset($_POST['signup'])) {
        if($_POST['password'] != $_POST['confirmpass']) {
            $reg_error = '<p><b>Your passwords do not match.</b></p>';
        }
        else {
            if(isset($_POST['subscription_purchase'])) {
                user_register($_POST['username'], $_POST['password'], $_POST['email']);
            }

            echo '<p><b>You are now registered.</b></p>';
        }
    }
    ?>

    <div id="login">
        <?php
        if(isset($_POST['submit'])) {
            $result = user_login($_POST['username'], $_POST['password']);

            if($result != 'Correct') {
                $login_error = $result;
            }
            else {
                echo '<p><i class="fa fa-cog fa-spin"></i></p>';
                echo '<meta http-equiv="refresh" content="0; url=index.php">';
            }
            if(isset($login_error)) {
                echo '<div><a href="#" class="m-btn mini red-stripe">Invalid user and/or password combination.</a></div>';
            }
        }
        ?>
        <form action="#" method="post" class="pure-form pure-form-stacked" role="form">
            <fieldset>
                <legend>Welcome to <b><?php echo $org_title; ?></b></legend>
    
                <input type="text" class="pure-input-1" name="username" placeholder="Username" autofocus required>
                <input type="password" class="pure-input-1" name="password" placeholder="Password" required>
    
                <input type="submit" name="submit" value="Log in" class="pure-button pure-button-secondary pure-button-xlarge">
            </fieldset>
        </form>
    </div>

    <div id="register">
        <?php if(isset($reg_error)) { ?>
            There was an error: <?php echo $reg_error; ?>, please try again.
        <?php } ?>
    
        <form action="#" method="post" class="pure-form pure-form-stacked" role="form">
            <input type="hidden" name="subscription_purchase" value="1">
            <fieldset>
                <legend>Sign up for <b><?php echo $org_title; ?></b></legend>
    
                <input type="text" class="pure-input-1" id="username" name="username" placeholder="Username" required>
                <input type="email" class="pure-input-1" id="email" name="email" placeholder="Email Address" required>
    
                <br>
                <input type="password" class="pure-input-1" id="password" name="password" placeholder="Password" autocomplete="off" required> 
                <input type="password" class="pure-input-1" id="confirmpass" name="confirmpass" placeholder="Password (again)" autocomplete="off" required>
    
                <br>
                <input type="submit" name="signup" value="Sign up" class="pure-button pure-button-secondary pure-button-xlarge">
            </fieldset>
        </form>
    </div>

	<p class="home-footer">
		<br><br><small><?php echo $org_version; ?> | &copy;2009-<?php echo date('Y'); ?> <b><?php echo $org_title; ?></b></small>
	</p>
</div>
</body>
</html>
