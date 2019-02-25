<?php
/*
 *
 * roo! 5.0.0
 *
 */

// define your roo! path
define('ROOPATH', 'https://roo.ie'); // no end slash

// define your plans
define('STANDARD_PLAN', 3);
define('EXTENDED_PLAN', 10);
define('UNLIMITED_PLAN', 1000);

// define Dropbox
$org_db_key = 'gn0c1uwc4nxxd4b';

// general settings
$org_title = 'roo'; // this is the text to appear in the title bar of every page

define('DBHOST', 'mysql2640int.cp.blacknight.com');
define('DBNAME', 'db1392057_roo');
define('DBUSER', 'u1392057_roo');
define('DBPASS', '41EN)8eme8');

$db = new PDO('mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=utf8', DBUSER, DBPASS);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

// do not change // internal use only
$org_version = '5.0.0'; // this is the current version of roo!
$org_auth_salt = '#+pT%B[M1X3R),Z(Y+q_tZwX~l@DXum2PdHS8hvmaVENrmI_?cvE#j8]n^.u]Ni+';
?>
