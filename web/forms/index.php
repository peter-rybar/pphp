<?php
require_once('./config.inc.php');
require_once('application.class.php');
require_once('template.class.php');
require_once('formvalidators.inc.php');
require_once('helpers.inc.php');

//print_r($_REQUEST);

// data
$data = array();

// form data validation
$form = new Form();
$form->add_validator('pohlavi', new SelectValidator(false, true, array('muz', 'zena')), array(
	'empty value' => 'Vyberte pohlaví pojištěného.',
	'not allowed value' => 'Vyberte pohlaví pojištěného.'));
$form->add_validator('castka', new NumberValidator(false, true, 30000, 999000, true), array(
	'empty value' => 'Zadejte částku jednorázového pojistného.',
	'not numeric value' => 'Do pole mohou být zadány pouze číslice.',
	'value too small' => 'Pojištění lze sjednat na minimální částku 30 000 Kč.',
	'value too big' => 'Prostřednictvím internetu lze sjednat pojištění do maximální výše vkladu 999 000 Kč (včetně).',
	'not integer numeric value' => 'Zadejte celočíselný udaj.'));

//print_r($form->values());

if ($form->is_submitted('submit')) {
	$form->validate($_REQUEST);
	if ($form->is_valid()) {
		$form_values = $form->values();

		// DO ACTION width $form_values

		//header('Location: ' . $SITE['root_url'] . '/next-step/');
		//exit;
	}
} else {
	$form_values['castka'] = 30000;
	$form->init($form_values);
}

$data['form'] = $form;

//print_r($data);


// view
$template = 'templates/forms';
$app = & new Application($SITE, $_SESSION['lang']);
$app->headTemplate = & new Template($template . '/head_' . $_SESSION['lang'] . '.php');
$app->contentTemplate = & new Template($template . '/content_' . $_SESSION['lang'] . '.php');
$app->contentTemplate->set('data', $data);

echo $app->render();

?>
