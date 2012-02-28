<?php

$ROOT_DIR = '/home/rybar/public_html/pphp';

$SITE['root_url'] = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://') . $_SERVER["HTTP_HOST"] . '/~rybar/pphp';

$SITE['version'] = '1.0.0';
$SITE['author'] = 'Peter Rybar';
$SITE['author_mail'] = 'pr.rybar@gmail.com';
$SITE['author_web'] = 'http://centaur.sk/~prybar';

//$DATABASE = $ROOT_DIR . '/_lib/db/data/db.sqlite'; // SQLite
//$DATABASE = array(
//	'host_port' => 'localhost',
//	'username' => 'root',
//	'password' => 'pwd',
//	'db_name' => 'database');



//-----------------------------------------------------------------------------
// force https if it is supported

/*
if ($_SERVER['HTTPS'] == 'off') {
	$URL = 'https://' . $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] == '80' ? '' : ':' . $_SERVER['SERVER_PORT']) . $_SERVER['REQUEST_URI'];
	//echo '<h1>' . preg_replace('/^http:(.*)$/', 'https:${1}', $URL) . "</h1>\n";
	header('Location: ' . preg_replace('/^http:(.*)$/', 'https:${1}', $URL));
	exit;
}
*/

//-----------------------------------------------------------------------------
// error reporting

if ( $_SERVER["HTTP_HOST"] == 'localhost' ) {
	error_reporting(E_ALL);
} else {
	error_reporting(0);
}

//-----------------------------------------------------------------------------
// add include path

$INCLUDE_PATH = $ROOT_DIR . '/_lib';
set_include_path(get_include_path() . PATH_SEPARATOR . $INCLUDE_PATH);


//-----------------------------------------------------------------------------
// suppress magic quotes for _GET _POST _COOKIE ( _REQUEST )

if ( get_magic_quotes_gpc() ) {
	// Overrides GPC variables
	foreach ( $_REQUEST as $k => $v ) {
		if ( is_string($v) ) 
			$_REQUEST[$k] = stripslashes($v);
	}
}


//-----------------------------------------------------------------------------
// session

session_cache_limiter('nocache');
session_start();

/*
if ( isset($_SESSION['session_count']) ) {
	$_SESSION['session_count']++;
	//$_SESSION['session_count'] = 0;
} else {
	$_SESSION['session_count'] = 0;
	$_SESSION['lang'] = '';
}
*/

//-----------------------------------------------------------------------------
// lang

if (empty($_SESSION['lang'])) {
	$_SESSION['lang'] = '';
	//$_SESSION['lang'] = 'sk';
}
if (isset($_REQUEST['lang'])) {
	$_SESSION['lang'] = $_REQUEST['lang'];
}
//$_SESSION['lang'] = '';

//-----------------------------------------------------------------------------
// XHTML
/*
if ((isset($_SERVER["HTTP_ACCEPT"]) and stristr($_SERVER["HTTP_ACCEPT"], "application/xhtml+xml")) or
	stristr($_SERVER["HTTP_USER_AGENT"], "W3C_Validator") )
{
	header("Content-type: application/xhtml+xml");
	print("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");
} else {
	header("Content-type: text/html; charset=utf-8");
}
*/

//-----------------------------------------------------------------------------
// hit info
/*
//if (!eregi("dmpc", $_SERVER['HTTP_HOST'])) {
	$mtext = '';
	//$mtext = $_SERVER['HTTP_REFERER']."\n";
	$mtext .= $_SERVER['REMOTE_ADDR']."\n";
	//$mtext .= $_SERVER['HTTP_ADDR']."\n";
	$mtext .= $_SERVER['REQUEST_URI']."\n";
	$mtext .= $_SERVER['HTTP_USER_AGENT']."\n";
	$mtext .= $_SERVER['QUERY_STRING']."\n";
	ob_start();
	print_r($_REQUEST);
	print_r($_SERVER);
	$obstr = ob_get_contents();
	ob_end_clean();
	$mtext .= $obstr."\n";
	mail('rybar@dmpc.dbp.fmph.uniba.sk', 'HOME', $mtext);
//}
*/

?>
