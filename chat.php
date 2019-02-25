<?php
include('includes/config.php');

session_start();

if($_GET['action'] == "chatheartbeat") { chatHeartbeat(); } 
if($_GET['action'] == "sendchat") { sendChat(); } 
if($_GET['action'] == "closechat") { closeChat(); } 
if($_GET['action'] == "startchatsession") { startChatSession(); } 

if(!isset($_SESSION['chatHistory'])) {
	$_SESSION['chatHistory'] = array();	
}

if(!isset($_SESSION['openChatBoxes'])) {
	$_SESSION['openChatBoxes'] = array();	
}

function chatHeartbeat() {
	$items = '';
	$chatBoxes = array();

	$db = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8', DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

	$res = $db->query("SELECT * FROM chat WHERE (chat.to = '" . $_SESSION['username'] . "' AND recd = 0) ORDER BY id ASC");
	foreach($res as $chat) {
		if(!isset($_SESSION['openChatBoxes'][$chat['from']]) && isset($_SESSION['chatHistory'][$chat['from']])) {
			$items = $_SESSION['chatHistory'][$chat['from']];
		}

		$chat['message'] = sanitize($chat['message']);

		$items .= <<<EOD
			{
			"s": "0",
			"f": "{$chat['from']}",
			"m": "{$chat['message']}"
			},
EOD;

		if(!isset($_SESSION['chatHistory'][$chat['from']])) {
			$_SESSION['chatHistory'][$chat['from']] = '';
		}

		$_SESSION['chatHistory'][$chat['from']] .= <<<EOD
						   {
			"s": "0",
			"f": "{$chat['from']}",
			"m": "{$chat['message']}"
	   },
EOD;
		
		unset($_SESSION['tsChatBoxes'][$chat['from']]);
		$_SESSION['openChatBoxes'][$chat['from']] = $chat['sent'];
	}

	if(!empty($_SESSION['openChatBoxes'])) {
		foreach ($_SESSION['openChatBoxes'] as $chatbox => $time) {
			if(!isset($_SESSION['tsChatBoxes'][$chatbox])) {
				$now = time()-strtotime($time);
				$time = date('g:iA M dS', strtotime($time));

				$message = "<small>Sent at $time</small>";
				if($now > 180) {
					$items .= <<<EOD
{
"s": "2",
"f": "$chatbox",
"m": "{$message}"
},
EOD;

					if(!isset($_SESSION['chatHistory'][$chatbox])) {
						$_SESSION['chatHistory'][$chatbox] = '';
					}

					$_SESSION['chatHistory'][$chatbox] .= <<<EOD
		{
"s": "2",
"f": "$chatbox",
"m": "{$message}"
},
EOD;
					$_SESSION['tsChatBoxes'][$chatbox] = 1;
				}
			}
		}
	}

	$db = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8', DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

	$stmt = $db->prepare("update chat set recd = 1 where chat.to = '" . $_SESSION['username'] . "' and recd = 0");
	$stmt->execute();

	if($items != '') {
		$items = substr($items, 0, -1);
	}
	header('Content-type: application/json');
	?>
{
		"items": [
			<?php echo $items;?>
        ]
}

	<?php
	exit(0);
}

function chatBoxSession($chatbox) {
	$items = '';

	if(isset($_SESSION['chatHistory'][$chatbox])) {
		$items = $_SESSION['chatHistory'][$chatbox];
	}

	return $items;
}

function startChatSession() {
	$items = '';
	if (!empty($_SESSION['openChatBoxes'])) {
		foreach ($_SESSION['openChatBoxes'] as $chatbox => $void) {
			$items .= chatBoxSession($chatbox);
		}
	}


	if ($items != '') {
		$items = substr($items, 0, -1);
	}

header('Content-type: application/json');
?>
{
		"username": "<?php echo $_SESSION['username'];?>",
		"items": [
			<?php echo $items;?>
        ]
}

<?php


	exit(0);
}

function sendChat() {
	$from = $_SESSION['username'];
	$to = $_POST['to'];
	$message = $_POST['message'];

	$_SESSION['openChatBoxes'][$_POST['to']] = date('Y-m-d H:i:s', time());

	$messagesan = sanitize($message);

	if(!isset($_SESSION['chatHistory'][$_POST['to']])) {
		$_SESSION['chatHistory'][$_POST['to']] = '';
	}

	$_SESSION['chatHistory'][$_POST['to']] .= <<<EOD
		{
		"s": "1",
		"f": "{$to}",
		"m": "{$messagesan}"
		},
EOD;

	unset($_SESSION['tsChatBoxes'][$_POST['to']]);

	$db = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8', DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

	$stmt = $db->prepare("INSERT INTO chat (chat.from, chat.to , message, sent) VALUES ('" . $from . "', '" . $to . "', '" . $message . "', NOW())");
	$stmt->execute();

	echo "1";
	exit(0);
}

function closeChat() {

	unset($_SESSION['openChatBoxes'][$_POST['chatbox']]);
	
	echo "1";
	exit(0);
}

function sanitize($text) {
	$text = htmlspecialchars($text, ENT_QUOTES);
	$text = str_replace("\n\r","\n",$text);
	$text = str_replace("\r\n","\n",$text);
	$text = str_replace("\n","<br>",$text);

	return '<small>['.date('H:i:s').']</small> '.$text;
}
?>
