<?php
require_once('./config.inc.php');
require_once('application.class.php');
require_once('template.class.php');

//print_r($_REQUEST);

// data
$data = array();
//print_r($data);


// view
$template = 'templates';
$app = & new Application($SITE, $_SESSION['lang']);
$app->headTemplate = & new Template($template . '/head_' . $_SESSION['lang'] . '.php');
$app->contentTemplate = & new Template($template . '/content_' . $_SESSION['lang'] . '.php');
//$app->contentTemplate->set('data', $data);

echo $app->render();

?>
