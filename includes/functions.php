<?php
if(!isset($_SESSION)) {
	session_start();
} 

// roo! settings file //
include('config.php');

$_SESSION['code'] = md5(uniqid('auth', true));

// Refresh page - redirection to 'index.php'
function redirect() {
	echo '<meta http-equiv="refresh" content="0; url=index.php">';
}

// Multiuser routines

function user_register($username, $password, $email) {
	$db = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8', DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $password = hash_hmac('sha512', $password, '#+pT%B[M1X3R),Z(Y+q_tZwX~l@DXum2PdHS8hvmaVENrmI_?cvE#j8]n^.u]Ni+');

    $stmt = $db->prepare("INSERT INTO user (username, password, email, registration_date) VALUES ('$username', '$password', '$email', NOW())");
    $stmt->execute();
}

function user_login($username, $password) {
	$db = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8', DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $password = hash_hmac('sha512', $password, '#+pT%B[M1X3R),Z(Y+q_tZwX~l@DXum2PdHS8hvmaVENrmI_?cvE#j8]n^.u]Ni+');

    $stmt = $db->prepare("SELECT * FROM user WHERE username='$username' AND password='$password'");
    $stmt->execute();
    $user = $stmt->fetch();

    if($password == $user['password']) {
		// Store the data in the session
		$_SESSION['username'] = $username;
		$_SESSION['rcrypt'] = hash_hmac('sha512', $_SESSION['username'], '#+pT%B[M1X3R),Z(Y+q_tZwX~l@DXum2PdHS8hvmaVENrmI_?cvE#j8]n^.u]Ni+');

		return 'Correct';
	}
	else {
		return false;
	}
}

function user_logout() {
	session_unset();
	session_destroy();
}

function is_authed() {
	// Check if the encrypted username is the same as the unencrypted one, if it is, it hasn't been changed
	if(isset($_SESSION['username']) && (hash_hmac('sha512', $_SESSION['username'], '#+pT%B[M1X3R),Z(Y+q_tZwX~l@DXum2PdHS8hvmaVENrmI_?cvE#j8]n^.u]Ni+') == $_SESSION['rcrypt'])) {
		return true;
	}
	else {
		return false;
	}
}



function daysDifference($endDate, $beginDate) {
	//explode the date by "-" and storing to array
	$date_parts1 = explode("-", $beginDate);
	$date_parts2 = explode("-", $endDate);
	//gregoriantojd() Converts a Gregorian date to Julian Day Count
	$start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
	$end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
	return $end_date - $start_date;
}

function nicetime($date) {
	if(empty($date)) {
		return "No date provided";
	}
	$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");
	$now = time();
	$unix_date = strtotime($date);
	// check validity of date
	if(empty($unix_date)) {   
		return "Bad date";
	}
	// is it future date or past date
	if($now > $unix_date) {   
		$difference = $now - $unix_date;
		$tense = "ago";
	}
	else {
		$difference = $unix_date - $now;
		$tense = "from now";
	}
	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		$difference /= $lengths[$j];
	}
	$difference = round($difference);
	if($difference != 1) {
		$periods[$j] .= "s";
	}
	return "$difference $periods[$j] {$tense}";
}

function cleanUrl($title) // take a title, and turn it into a URL-safe string
{
  $title = strtolower($title);
  $title = preg_replace("/[^a-z0-9\s_+]/", '', $title);
  $url = preg_replace("/[\s_]{1,}/", '-', $title);
  return $url;
}

function smilieMe($text) {
	$smiliesFind = array(
		'/:\)/',
		'/:P/',
		'/:D/',
		'/:S/',
		'/:\(/',
		'/:8/',
		'/:tea/',
		'/:o/',
		'/:O/',
		'/:q/',
		'/:Q/',
		'/:hug/',
		'/:joy/',
		'/:yes/',
		'/:no/',
	);
	$smiliesReplace = array(
		'<img src="images/smilies/smile.png" alt=":)" title=":)" />',
		'<img src="images/smilies/tongue.png" alt=":P" title=":P" />',
		'<img src="images/smilies/grin.png" alt=":D" title=":D" />',
		'<img src="images/smilies/confused.png" alt=":S" title=":S" />',
		'<img src="images/smilies/sad.png" alt=":(" title=":(" />',
		'<img src="images/smilies/proud.png" alt=":8" title=":8" />',
		'<img src="images/smilies/tea.gif" alt=":tea" title=":tea" />',
		'<img src="images/smilies/wow.png" alt=":o" title=":o" />',
		'<img src="images/smilies/wow.png" alt=":O" title=":O" />',
		'<img src="images/smilies/yay.png" alt=":q" title=":q" />',
		'<img src="images/smilies/yay.png" alt=":Q" title=":Q" />',
		'<img src="images/smilies/hug.gif" alt=":hug" title=":hug" />',
		'<img src="images/smilies/joy.gif" alt=":joy" title=":joy" />',
		'<img src="images/smilies/yes.gif" alt=":yes" title=":yes" />',
		'<img src="images/smilies/no.gif" alt=":no" title=":no" />',
	);
	return preg_replace($smiliesFind, $smiliesReplace, $text);
}

function roo_autop($content) {
	$content = trim($content);
	$content = stripslashes($content);

	/*
	 * temporarily replace two or more consecutive newlines
	 * into SOH characters (Start of Heading - first character of a message header)
	 */
	$content = preg_replace('~(\r\n|\n){2,}|$~', "\001", $content);

	// convert remaining single newlines into HTML <br>
	$content = nl2br($content);

	// replace SOH characters with paragraphs
	$content = preg_replace('/(.*?)\001/s', "<p>$1</p>\n", $content);

	$content = smilieMe($content);

	// parse URL addresses (ftp, http, https)
	$content = preg_replace('*(f|ht)tps?://[A-Za-z0-9\./?=\+&%]+*', '<a href="$0">$0</a>', $content);

	return $content;
}

function formatbytes($file, $type) {
   switch($type){
      case "KB":
         $filesize = filesize($file) * .0009765625; // bytes to KB
      break;
      case "MB":
         $filesize = (filesize($file) * .0009765625) * .0009765625; // bytes to MB
      break;
      case "GB":
         $filesize = ((filesize($file) * .0009765625) * .0009765625) * .0009765625; // bytes to GB
      break;
   }
   if($filesize <= 0)
      return $filesize = 'unknown file size';
   else
       return round($filesize, 2);
}

function urlExists($url = NULL) {
    if($url == NULL) {
        return false;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    //var_dump($data);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if($httpcode >= 200 && $httpcode < 400) {
        return true;
    } else {
        return false;
    }
}

function pinger($host) {
    if(urlExists($host) == true) {
        return 1;
    } else {
        return 0;
    }
}

function percentage($amount, $total, $decimal = 2) {
	if(0 === (int)$total) {
		return $total;
	}
    $result = number_format((((int)$amount / (int)$total) * 100), $decimal);
    $result = 100 - $result;
	return $result . '%<br><small>Uptime</small>';
}
?>
