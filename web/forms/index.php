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


// TextValidator
$form->add_validator('text', new TextValidator(false, true, 1, 10 /*, '/(*UTF8)^\S+$/'*/), array(
	'empty value' => 'Empty value.',
	'value too short' => 'Value too short.',
	'value too long' => 'Value too long.',
	'value not match' => 'Value not match.'));

// NumberValidator
$form->add_validator('number', new NumberValidator(false, true, 10, 100, true), array(
	'empty value' => 'Empty value.',
	'not numeric value' => 'Not a numeric value.',
	'value too small' => 'Value too small.',
	'value too big' => 'Value too big.',
	'not integer numeric value' => 'Not a numeric integer value.'));

// DateValidator
$form->add_validator('date', new DateValidator(false, true, 'd.m.Y', '1.1.1970', date('d.m.Y')), array(
	'empty value' => 'Empty value.',
	'not date value' => 'Not a date value, format DD.MM.RRRR.',
	'value too small' => 'Value too small.',
	'value too big' => 'Value too big.'));

// CheckboxValidator
$form->add_validator('checkbox', new CheckboxValidator(true), array(
	'invalid value' => 'Invalid value.'));

// SelectValidator
$form->add_validator('select', new SelectValidator(false, true, array('one', 'two')), array(
	'empty value' => 'Empty value.',
	'not allowed value' => 'Not allowed value.'));

// ArrayValidator
$form->add_validator('array'.$i, new ArrayValidator(
	array(  'text' => new TextValidator($empty_ok, true, 1, 73, "/^([^0-9*?';]*)$/"),
		'number' => new NumberValidator($empty_ok, true, 1, 100, true))),
	array(	'text' => array(
			'empty value' => 'Empty value.',
			'value too short' => 'Value too short.',
			'value too long' => 'Value too long.',
			'value not match' => 'Value not match.'),
		'number' => array(
			'empty value' => 'Empty Value.',
			'not numeric value' => 'Not a numeric value.',
			'value too small' => 'Value too small.',
			'value too big' => 'Value too big.',
			'not integer numeric value' => 'Not integer numric value.'))
	);

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
