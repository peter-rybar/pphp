<?

class Form
{
	function Form()
	{
		$this->_validators = array();
		$this->_messages = array();
		$this->_is_valid = true;
	}

	function init($data)
	{
		foreach ($data as $name => $value) {
			if (array_key_exists($name, $this->_validators)) {
				if (is_array($value)) {
					foreach ($value as $key => $item) {
						$this->_validators[$name]->_validators[$key]->_value = $item;
					}
				} else {
					$this->_validators[$name]->_value = $value;
				}
			}
		}
	}

	function add_validator($name, $validator, $messages = null)
	{
		$this->_validators[$name] = $validator;
		if (is_array($messages)) {
			$this->_messages[$name] = $messages;
		}
	}

	function get_validator($name)
	{
		return $this->_validators[$name];
	}

	function validate($request_data)
	{
		$validity = true;
		foreach ($this->_validators as $name => $validator) {
			$valid = $validator->validate($request_data[$name]);
			if (!$valid) {
				$validity = false;
			}
		}
		$this->_is_valid = $validity;
		return $validity;
	}

	function value($name, $subname = null)
	{
		if (!array_key_exists($name, $this->_validators)) {
			return '';
		}

		$value = $this->_validators[$name]->value();
		if ($subname !== null and is_array($value)) {
			return $value[$subname];
		} else {
			return $value;
		}
	}

	function values()
	{
		$values = array();
		foreach ($this->_validators as $name => $validator) {
			$values[$name] = $validator->value();
		}
		return $values;
	}

	function error($name, $subname = null)
	{
		if (!array_key_exists($name, $this->_validators)) {
			return '';
		}

		$error = $this->_validators[$name]->error();
		if (is_array($error)) {
			if ($subname !== null) {
				if (array_key_exists($subname, $this->_messages[$name])) {
					foreach ($this->_messages[$name][$subname] as $key => $message) {
						if (preg_match('/^' . $key . '/', $error[$subname])) {
							return $message;
						}
					}
				}
				return $error[$subname];
			} else {
				return $error;
			}
		} else {
			if (array_key_exists($name, $this->_messages)) {
				foreach ($this->_messages[$name] as $key => $message) {
					if (preg_match('/^' . $key . '/', $error)) {
						return $message;
					}
				}
			}
			return $error;
		}
	}

	function is_submitted($submit_label)
	{
		return isset($_REQUEST[$submit_label]);
	}

	function is_valid()
	{
		return $this->_is_valid;
	}

	function errors()
	{
		$errors = array();
		foreach ($this->_validators as $name => $validator) {
			$error = $validator->error($name);
			if (!empty($error)) {
				$errors[$name] = $this->error($name);
			}
		}
		return $errors;
	}
}


class TextValidator
{
	function TextValidator($empty_ok = true, $trim = false, $min = null, $max = null, $preg_match = null)
	{
		$this->_value = '';
		$this->_error = '';

		$this->_empty_ok = $empty_ok;
		$this->_trim = $trim;
		$this->_min = $min;
		$this->_max = $max;
		$this->_preg_match = $preg_match;
	}

	function value()
	{
		return $this->_value;
	}

	function error()
	{
		return $this->_error;
	}

	function validate($value)
	{
		$this->_value = $value;
		if ($this->_trim) {
			$this->_value = trim($value);
		}
		if ($this->_empty_ok and empty($this->_value)) {
			return true;
		}
		if (!$this->_empty_ok and empty($this->_value)) {
			$this->_error = 'empty value';
			return false;
		}
		$strlength = mb_strlen($this->_value, 'utf8');
		if ($this->_min !== null and $this->_min > $strlength) {
			$this->_error = 'value too short: min. ' . $this->_min;
			return false;
		}
		if ($this->_max !== null and $this->_max < $strlength) {
			$this->_error = 'value too long: max. ' . $this->_max;
			return false;
		}
		if ($this->_preg_match !== null) {
			if (!preg_match($this->_preg_match, $this->_value)) {
				$this->_error = "value not match: " . $this->_preg_match;
				return false;
			}
		}
		return true;
	}
}


class NumberValidator
{
	function NumberValidator($empty_ok = true, $trim = false, $min = null, $max = null, $integer = false)
	{
		$this->_value = '';
		$this->_error = '';

		$this->_empty_ok = $empty_ok;
		$this->_trim = $trim;
		$this->_min = $min;
		$this->_max = $max;
		$this->_integer = $integer;
	}

	function value()
	{
		return $this->_value;
	}

	function error()
	{
		return $this->_error;
	}

	function validate($value)
	{
		$this->_value = $value;
		if ($this->_trim) {
			$this->_value = trim($value);
		}
		if ($this->_empty_ok and empty($this->_value)) {
			return true;
		}
		if (!$this->_empty_ok and empty($this->_value)) {
			$this->_error = 'empty value';
			return false;
		}
		if (!is_numeric($this->_value)) {
			$this->_error = 'not numeric value';
			return false;
		}
		if ($this->_integer and (intval($this->_value) != floatval($this->_value))) {
			$this->_error = 'not integer numeric value';
			return false;
		}
		if ($this->_integer) {
			$this->_value = intval($this->_value);
		} else {
			$this->_value = floatval($this->_value);
		}
		if ($this->_min !== null and $this->_min > $this->_value) {
			$this->_error = 'value too small: min. ' . $this->_min;
			return false;
		}
		if ($this->_max !== null and $this->_max < $this->_value) {
			$this->_error = 'value too big: max. ' . $this->_max;
			return false;
		}
		return true;
	}
}


class DateValidator
{
	function DateValidator($empty_ok = true, $trim = false,
			$format = '%d.%m.%Y', $min = null, $max = null)
	{
		$this->_value = '';
		$this->_error = '';

		$this->_empty_ok = $empty_ok;
		$this->_trim = $trim;
		$this->_format = $format;
		$this->_min = $min;
		$this->_max = $max;
	}

	function value()
	{
		return $this->_value;
	}

	function error()
	{
		return $this->_error;
	}

	function validate($value)
	{
		$this->_value = $value;
		if ($this->_trim) {
			$this->_value = trim($value);
		}
		if ($this->_empty_ok and empty($this->_value)) {
			return true;
		}
		if (!$this->_empty_ok and empty($this->_value)) {
			$this->_error = 'empty value';
			return false;
		}
		$timestamp = strtotime($this->_value);
		if ($timestamp === false) {
			$this->_error = 'not date value';
			return false;
		}
		//echo date($this->_format, $timestamp) . "\n";
		if ($this->_min !== null) {
			$timestamp_min = strtotime($this->_min);
			//echo date($this->_format, $timestamp_min) . "\n";
			if ($timestamp_min > $timestamp) {
				$this->_error = 'value too small: min. ' . $this->_min;
				return false;
			}
		}
		if ($this->_max !== null) {
			$timestamp_max = strtotime($this->_max);
			//echo strftime($this->_format, $timestamp_max) . "\n";
			if ($timestamp_max < $timestamp) {
				$this->_error = 'value too big: max. ' . $this->_max;
				return false;
			}
		}
/*
		$ftime = strptime($this->_value, $this->_format);
		if ($ftime === false or !empty($ftime)) {
			$this->_error = 'not date value';
			return false;
		}
		$timestamp = mktime(
			$ftime['tm_hour'],
			$ftime['tm_min'],
			$ftime['tm_sec'],
			1 ,
			$ftime['tm_yday'] + 1,
			$ftime['tm_year'] + 1900);
		//echo strftime($this->_format, $timestamp) . "\n";
		if ($this->_min !== null) {
			$ftime_min = strptime($this->_min, $this->_format);
			$timestamp_min = mktime(
				$ftime_min['tm_hour'],
				$ftime_min['tm_min'],
				$ftime_min['tm_sec'],
				1 ,
				$ftime_min['tm_yday'] + 1,
				$ftime_min['tm_year'] + 1900);
			//echo strftime($this->_format, $timestamp_min) . "\n";
			if ($timestamp_min > $timestamp) {
				$this->_error = 'value too small: min. ' . $this->_min;
				return false;
			}
		}
		if ($this->_max !== null) {
			$ftime_max = strptime($this->_max, $this->_format);
			$timestamp_max = mktime(
				$ftime_max['tm_hour'],
				$ftime_max['tm_min'],
				$ftime_max['tm_sec'],
				1 ,
				$ftime_max['tm_yday'] + 1,
				$ftime_max['tm_year'] + 1900);
			//echo strftime($this->_format, $timestamp_max) . "\n";
			if ($timestamp_max < $timestamp) {
				$this->_error = 'value too big: max. ' . $this->_max;
				return false;
			}
		}
		*/

		return true;
	}
}


class SelectValidator
{
	function SelectValidator($empty_ok = true, $trim = false, $allowed_values)
	{
		$this->_value = '';
		$this->_error = '';

		$this->_empty_ok = $empty_ok;
		$this->_trim = $trim;
		$this->_allowed_values = $allowed_values;
	}

	function value()
	{
		return $this->_value;
	}

	function error()
	{
		return $this->_error;
	}

	function validate($value)
	{
		$this->_value = $value;
		if ($this->_trim) {
			$this->_value = trim($value);
		}
		if ($this->_empty_ok and empty($this->_value)) {
			return true;
		}
		if (!$this->_empty_ok and empty($this->_value)) {
			$this->_error = 'empty value';
			return false;
		}
		if (!in_array($this->_value, $this->_allowed_values)) {
			$this->_error = 'not allowed value: [' . implode(', ', $this->_allowed_values) . ']';
			return false;
		}
		return true;
	}
}


class CheckboxValidator
{
	function CheckboxValidator($valid_value = null)
	{
		$this->_value = true;
		$this->_error = '';

		$this->_valid_value = $valid_value;
	}

	function value()
	{
		return $this->_value;
	}

	function error()
	{
		return $this->_error;
	}

	function validate($value)
	{
		if ($value === true or $value === false) {
			$this->_value = $value;
		} else if ($value === null) {
			$this->_value = false;
		} else {
			$this->_value = true;
		}
		if ($this->_valid_value !== null) {
			if ($this->_value !== $this->_valid_value) {
				$this->_error = 'invalid value: ' . ($this->_value === true ? 'true' : 'false');
				return false;
			}
		}

		return true;
	}
}


class ArrayValidator
{
	function ArrayValidator($validators_map)
	{
		$this->_value = array();
		$this->_error = array();

		$this->_validators = $validators_map;
	}

	function value()
	{
		$value = array();
		foreach ($this->_validators as $name => $validator) {
			$value[$name] = $validator->value();
		}
		return $value;
	}

	function error()
	{
		return $this->_error;
	}

	function validate($value)
	{
		$this->_value = $value;
		if (!is_array($this->_value)) {
			$this->_error = 'value not array';
			return false;
		}
		$validity = true;
		foreach ($this->_validators as $name => $validator) {
			$valid = $validator->validate($this->_value[$name]);
			if (!$valid) {
				$validity = false;
				$error[$name] = $validator->error();
				$this->_error = $error;
			}
		}
		if ($validity === false) {
			return false;
		}
		return true;
	}
}


class BirdthNumberValidator
{
	function BirdthNumberValidator($empty_ok = true, $trim = false)
	{
		$this->_value = '';
		$this->_error = '';

		$this->_empty_ok = $empty_ok;
		$this->_trim = $trim;
	}

	function value()
	{
		return $this->_value;
	}

	function error()
	{
		return $this->_error;
	}

	function validate($value)
	{
		$this->_value = $value;
		if ($this->_trim) {
			$this->_value = trim($value);
		}
		if ($this->_empty_ok and empty($this->_value)) {
			return true;
		}
		if (!$this->_empty_ok and empty($this->_value)) {
			$this->_error = 'empty value';
			return false;
		}

		$rc_parsed = rodne_cislo_detail($this->_value);
		if ($rc_parsed === false) {
			$this->_error = 'not valid value';
			return false;
		}
/*
		if (!((strlen($this->_value) == 9) || (strlen($this->_value) == 10))) {
			$this->_error = 'not valid value';
			return false;
		}

		if (!is_numeric($this->_value)) {
			$this->_error = 'not valid value';
			return false;
		}

		$date_string = substr($this->_value, 0, 6);

		// spare birthnumber for man
		if ("2" === substr($date_string, 2, 1)) {
			$date_string = substr($date_string, 0, 2) . "0" . substr($date_string, 3, 3);
		}
		if ("3" === substr($date_string, 2, 1)) {
			$date_string = substr($date_string, 0, 2) . "1" . substr($date_string, 3, 3);
		}

		//normal woman birthnumber
		if ("5" === substr($date_string, 2, 1)) {
			$date_string = substr($date_string, 0, 2) . "0" . substr($date_string, 3, 3);
		}
		if ("6" === substr($date_string, 2, 1)) {
			$date_string = substr($date_string, 0, 2) . "1" . substr($date_string, 3, 3);
		}

		// spare birthnumber for woman
		if ("7" === substr(2, 1)) {
			$date_string = substr($date_string, 0, 2) . "0" . substr($date_string, 3, 3);
		}
		if ("8" === substr($date_string, 2, 1)) {
			$date_string = substr($date_string, 0, 2) . "1" . substr($date_string, 3, 3);
		}
*/
/*
		$ftime = strptime($date_string, '%y%m%d');
		if ($ftime === false or !empty($ftime)) {
			$this->_error = 'not valid value';
			return false;
		}
*/

		return true;
	}
}

function rodne_cislo_detail($rc)
{
	if (!ctype_digit($rc)) { // musia byt iba cisla
		return false;
	}

	$parsed_rc = array();

	$delka = strlen($rc); // Spočítám z kolika čísel se skládá.

	if ($delka == 9) { //stary narozeny pred 1954
		$rok = substr($rc, 0, -7);    // vrátí 1-2 znak
		$mesic = substr($rc, 2, -5);  // vrátí 3-4 znak
		$den = substr($rc, 4, -3);    // vrátí 5-6 znak

		if ($rok >= 54) { // pokud je rok víc jak 54, je RC spatně, datum narození ale spočítat můžeme.
			return false;
		}

		if( $mesic >= 51 and $mesic <= 62){ // pokud je mesic mezi 51 a 62 jedna se urcite o zenu (zase asi plati jen po roce 54, to nevim jiste, kontrolou to ale nepokazim).
			$mesic = $mesic - 50;
			$parsed_rc['sex'] = 'female';
		} else {
			$parsed_rc['sex'] = 'male';
		}

		$parsed_rc['birdth'] = $den . '.' . $mesic . '.19' . $rok;
		$parsed_rc['birdth_day'] = $den;
		$parsed_rc['birdth_month'] = $mesic;
		$parsed_rc['birdth_year'] = '19' . $rok;
		$parsed_rc['format'] = 'old';

		$m = strlen($mesic);
		if ($m == 1) {
			$zeroM = "0";
		} else {
			$zeroM = "";
		}
		$d = strlen($den);
		if ($d==1) {
			$zeroD = "0";
		} else {
			$zeroD = "";
		}
		$DatNar = "19" . $rok . $zeroM . $mesic . $zeroD . $den;

		$vek= intval((date("Ymd", mktime()) - $DatNar) / 10000);
		$parsed_rc['age'] = $vek;

	} else if ($delka == 10) { // mlady, narozen po 1953
		if (!(substr($rc, 6) == '7777')) {
			$check2 = $rc / 11;   // pokud je RC 10 místné, musí být dělitelné 11 beze zbytku
			$check = floor($check2);  // zaokrouhlím  vždy dolů.

			if ($check != $check2) {  // Kontrola pravosti RC
				return false;
			}
		}

		$rok = substr($rc, 0, -8);    // vrátí 1-2 znak
		$mesic = substr($rc, 2, -6);  // vrátí 3-4 znak
		$den = substr($rc, 4, -4);    // vrátí 5-6 znak

		if ($mesic >= 51 and $mesic <= 62) {  // pokud je mesic mezi 51 a 62 jedna se urcite o zenu (zase asi plati jen po roce 53, to nevim jiste).
			$mesic = $mesic - 50;
			$parsed_rc['sex'] = 'female';
		} else {
			$parsed_rc['sex'] = 'male';
		}

		$letos = date("y");

		if ($letos >= $rok) {
			$stoleti = 20;
		} else {
			$stoleti = 19;
		}

		$parsed_rc['birdth'] = $den . '.' . $mesic . '.' . $stoleti . $rok;
		$parsed_rc['birdth_day'] = $den;
		$parsed_rc['birdth_month'] = $mesic;
		$parsed_rc['birdth_year'] = $stoleti . $rok;
		$parsed_rc['format'] = 'new';

		// Spočítámě věk
		$m = strlen($mesic);
		if ($m == 1) {
			$zeroM = "0";
		} else {
			$zeroM = "";
		}
		$d = strlen($den);
		if ($d == 1) {
			$zeroD = "0";
		} else {
			$zeroD = "";
		}
		$DatNar = $stoleti.$rok.$zeroM.$mesic.$zeroD.$den;

		$vek = intval((date("Ymd", mktime()) - $DatNar) / 10000);
		$parsed_rc['age'] = $vek;
	} else {
		return false;
	}

	return $parsed_rc;
}
// test
//$rc="5651152342";
//$rc="8410274114";     // zadám RC, bez lomítka - RC narozen mezi 1954 a 2000
//$rc="0212319393";     // zadám RC, bez lomítka - RC narozen po r.2000
//$rc="010101111";      // zadám RC, bez lomítka - RC narozen pred r.1954
//$rc="6805067777";
//$rc="7401158446";
//$rc="8607158395";
//var_dump(rodne_cislo_detail($rc));


/* test ------------------------------------------------------------

$request_data['name'] = ' Petčý ';
$request_data['age'] = ' 36.4';
$request_data['birdth'] = ' 11.4.1970';
$request_data['sex'] = 'male ';
$request_data['array']['name'] = 'peteeer4 ';
$request_data['array']['age'] = '36 ';
$request_data['checkbox'] = '';
$request_data['birdth_number'] = '6805067777';

print_r($request_data);


$form = new Form();
$form->add_validator('name', new TextValidator(false, true, 0, 5, '/^.*$/i'));
$form->add_validator('age', new NumberValidator(false, true, 25, 50, true),
	array('not numeric value' => 'zadaj cislo'));
$form->add_validator('birdth', new DateValidator(false, true, 'd.m.Y', '12.4.1970', '1.3.1990'));
$form->add_validator('sex', new SelectValidator(false, true, array('male', 'female')));
$form->add_validator('array', new ArrayValidator(
	array(	'name' => new TextValidator(false, true, 0, 5, '/^peter$/i'),
		'age' => new NumberValidator(false, true, 25, 50))));
$form->add_validator('checkbox', new CheckboxValidator(false));
$form->add_validator('birdth_number', new BirdthNumberValidator(false, true));

$valid = $form->validate($request_data);
var_dump($valid);

$v = $form->get_validator('name')->value();
var_dump($v);

$m = $form->get_validator('name')->error();
var_dump($m);

$v = $form->value('age');
var_dump($v);

$m = $form->error('age');
var_dump($m);

$v = $form->value('birdth');
var_dump($v);

$m = $form->error('birdth');
var_dump($m);

$v = $form->value('sex');
var_dump($v);

$m = $form->error('sex');
var_dump($m);

$v = $form->value('array');
var_dump($v);

$m = $form->error('array');
var_dump($m);

$v = $form->value('checkbox');
var_dump($v);

$m = $form->error('checkbox');
var_dump($m);

$v = $form->value('birdth_number');
var_dump($v);

$m = $form->error('birdth_number');
var_dump($m);

$em = $form->values();
print_r($em);

$em = $form->errors();
print_r($em);

*/

?>
