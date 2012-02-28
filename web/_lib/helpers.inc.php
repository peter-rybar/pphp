<?

function htmlentities_utf8($text)
{
	return htmlentities($text, ENT_COMPAT, 'UTF-8');
}


setlocale(LC_ALL, 'en_US.UTF8`');

function utf8_to_ascii($text)
{
	return iconv("UTF-8", "ASCII//TRANSLIT", $text);
}


function uri_from_text($text)
{
	$text = utf8_to_ascii($text);
	$text = preg_replace('/[^A-Za-z0-9-]/', ' ', $text);
	$text = preg_replace('/ +/', ' ', $text);
	$text = trim($text);
	$text = str_replace(' ', '-', $text);
	$text = preg_replace('/-+/', '-', $text);
	$text = strtolower($text);
	return $text;
}


function tags_normalize($text)
{
	$text = utf8_to_ascii($text);
	$text = preg_replace('/[^A-Za-z0-9 ]/', '', $text);
	$text = preg_replace('/ +/', ' ', $text);
	$text = trim($text);
	$text = strtolower($text);
	return explode(' ', $text);
}


//var_dump(utf8_to_ascii('123 45 - 67 qwerty +ľščťžýáíé ~!@#$%^&*()_+'));
//var_dump(uri_from_text('123 45 - 67 qwerty +ľščťžýáíé ~!@#$%^&*()_+'));
//var_dump(tags_normalize('123 45 - 67 qwerty +ľščťžýáíé ~!@#$%^&*()_+'));

//$uri = uri_from_text($title);
//$secret = sha1($title . $tags . $content);

?>
