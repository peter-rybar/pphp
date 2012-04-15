<?php

/**
 * Converts structured text to xhtml snippet.
 *
 * Rules:
 *
 * <pre>
 * 	em
 * 		_emphasized text_
 *
 * 	strong
 * 		*strong text*
 *
 * 	sub
 * 		_{text}
 *
 * 	sup
 * 		^{text}
 *
 * 	link
 * 		[text](URL "title")
 *
 * 	image
 * 		{alternative text}(URL "title")
 *
 * 	hr
 * 		----
 *
 * 	headings
 * 		heading 1
 * 		=========
 *
 * 		heading 2
 * 		---------
 *
 * 	pre
 * 		| code
 *
 * 	blockquote
 * 		> quoted text
 *
 * 	list
 * 		[white space]- item1
 * 		[white space]- item2
 *
 * 		[white space]1 item1
 * 		[white space]2 item2
 *
 * 	definition list
 * 		[white space]dt -- dd
 * 		[white space]dt -- dd
 *
 * 	tables
 * 		              caption
 * 		+------------------------------------+
 * 		| Fruit     | Nut        | Mammal    |
 * 		|====================================|
 * 		| Apple     | Peanut  as | me        |
 * 		|           | it is      | [link|URL]|
 * 		|------------------------------------|
 * 		| Orange    | Macadamia  | Woodchuck |
 * 		|------------------------------------|
 * 		| Banana    | Walnut     | Dolphin   |
 * 		+------------------------------------+
 * </pre>
 *
 * @author "Peter Rybar <pr.rybar@gmail.com>"
 *
 */
class StructuredText {

	function convertToHtml($text) {
		if ($text == null) {
			$text = "";
		}
		// at first trim leading and ending whitespaces - not
		// because lists depend on whitespace at beginning of para
		// text = trim(text);

		// convert new lines
		$txt = preg_replace("/\r\n/u", "\n", $text);

		// clean from ><&
		$txt = StructuredText::escape_for_HTML($txt);

		// split text into the say "paragraphs"
		$txt = preg_replace("/\n\n+/u", "\n\n", $txt);
		$txt = preg_replace("/\A\n+|\n+\z/u", '', $txt);

		$para = preg_split("/\n\n/u", $txt);

		$result = '';
		// now contert each para separately
		foreach ($para as $p) {
			$result .= StructuredText::convert_para($p) . "\n\n";
		}

		return $result;
	}

	function escape_for_HTML($text) {
		$txt = preg_replace('/&/u', '&amp;', $text);
		$txt = preg_replace('/>/u', '&gt;', $txt);
		$txt = preg_replace('/</u', '&lt;', $txt);
		return $txt;
	}

	function convert_para($text) {
		$re = "";

		if (preg_match("/\A-----*\z/u", $text)) {
			return '<hr />';
		}

		// headings
		if (preg_match("/\A(.|\n)*----*\z/u", $text)) {
			$re = '<h2>' . StructuredText::convert_text(preg_replace("/\s*----*\z/u", '', $text)) . '</h2>';
		} else if (preg_match("/\A(.|\n)*====*\z/u", $text)) {
			$re = '<h1>' . StructuredText::convert_text(preg_replace("/\s*====*\z/u", '', $text)) . '</h1>';
		} else if (StructuredText::is_o_list($text)) { // lists
			$re = StructuredText::do_o_list($text);
		} else if (StructuredText::is_u_list($text)) {
			$re = StructuredText::do_u_list($text);
		} else if (StructuredText::is_definition_list($text)) { // difinition list
			$re = StructuredText::do_definition_list($text);
		} else if (StructuredText::is_blockquote($text)) { // quotes
			$re = StructuredText::do_blockquote($text);
		} else if (StructuredText::is_preformated($text)) { // preformated text
			$re = StructuredText::do_preformated($text);
		} else if (StructuredText::is_table($text)) { // table
			$re = StructuredText::do_table($text);
		} else {
			$re = '<p>' . StructuredText::convert_text($text) . '</p>';
		}

		return $re;
	}

	function convert_text($text) {
		// it is very important to call do_links first
		return StructuredText::do_inline_formating(StructuredText::do_images(StructuredText::do_links($text)));
	}

	function do_links($text) {
		// auto detection of hyperlinks and their conversion to structured links syntax
		$txt = preg_replace("/(\A|\s)([a-z0-9_+.-]+@(?:[a-z0-9-]+\.)+[a-z0-9]{2,4})([\s.,;:?!]|\z)/ui",
				"$1[$2](mailto:$2)$3", $text); // auto detect (simple) mails
		$txt = preg_replace("/(\A|\s)((?:[a-z0-9-]+\.){2,}[a-z]{2,4}[^\s\"<>*\[\]\|{}\(\)]*)(\z|\s)/u",
				"$1[$2](http://$2)$3", $txt); // auto detect links
		$txt = preg_replace("/(\A|\s)(https?:\/\/(?:[a-z0-9-]+\.)+[a-z0-9]{2,4}[^\s\"<>*\[\]\|{}\(\)]*)(\z|\s)/u",
				"$1[$2]($2)$3", $txt); // auto detect links
		$txt = preg_replace("/\[([^\[\]<>]+)\]\(([^\s\"<>\[\]\|{}]+)(?:\s\"([^\"<>{}_*]*)\")?\)/u",
				"<a href=\"$2\" title=\"$3\">$1</a>", $txt); // links
		$txt = preg_replace("/<a href=\"javascript:/ui", "<a href=\"", $txt); // stop bad boys
		return $txt;
	}

	function do_images($text) {
		
		$txt = preg_replace("/\{([^\"<>\[\]{}_*]+)\}\(([^\s\"<>\[\]\|{}]+)(?:\s\"([^\"<>{}_*]*)\")?\)/u",
				"<img src=\"$2\" alt=\"$1\" title=\"$3\" />", $text); // images
		$txt = preg_replace("/<img src=\"javascript:/ui", "<img src=\"", $txt); // stop bad boys
		return $txt;
	}

	function do_inline_formating($text) {
		$txt = preg_replace("/([\s>]|\A)_([^_<>]*)_([\s<.,;:?!]|\z)/u", "$1<em>$2</em>$3", $text); // em
		$txt = preg_replace("/([\s>]|\A)\*([^\*<>]*)\*([\s<.,;:?!]|\z)/u", "$1<strong>$2</strong>$3", $txt); // strong
		$txt = preg_replace("/_\{([^\{\}_\^<>]*)\}/u", "<sub>$1</sub>", $txt); // sub
		$txt = preg_replace("/\^\{([^\{\}_\^<>]*)\}/u", "<sup>$1</sup>", $txt); //sup
		return $txt;
	}

	function is_blockquote($text) {
		return preg_match("/\A\s*&gt;\s(.|\n)*\z/u", $text);
	}

	function do_blockquote($text) {
		if (preg_match("/\A(\s*&gt;\s.*\n?)+\z/u", $text)) {
			$text = preg_replace("/\n\s*&gt;\s/u", "\n", $text);
		}

		return "<blockquote>\n<p>" .
				StructuredText::convert_text(preg_replace("/\A\s*&gt;\s/u", "", $text)) .
				"</p>\n</blockquote>";
	}

	function is_preformated($text) {
		return preg_match("/\A\s*\|\s(.|\n)*\z/u", $text);
	}

	function do_preformated($text) {
		if (preg_match("/\A(\s*\|\s.*\n?)+\z/u", $text)) {
			$text = preg_replace("/\n\s*\|\s/u", "\n", $text);
		}

		return "<pre>" . StructuredText::convert_text(preg_replace("/\A\s*\|\s/u", "", $text)) . "</pre>";
	}

	// i sugest: \s > [ \t]
	function is_o_list($text) {
		return preg_match("/\A[ \t]+[1-9#][0-9]*[.\)]?[ \t](.|\n)*\z/u", $text);
	}

	function do_o_list($text) {
		$parts = preg_split("/\n/u", $text);

		$re = "<ol>\n";

		for ($i = 0; $i < count($parts); $i++) {
			if (preg_match("/\A\s+[1-9#][0-9]*[.\)]?\s.*\z/u", $parts[$i])) {
				if ($i == 0) {
					$re .= "\t<li>" . StructuredText::convert_text(preg_replace("/\A\s*[1-9#][0-9]*[.\)]?/u", "", $parts[$i]));
				} else {
					$re .= "</li>\n\t<li>" . StructuredText::convert_text(preg_replace("/\A\s*[1-9#][0-9]*[.\)]?/u", "", $parts[$i]));
				}
			} else {
				$re .= StructuredText::convert_text($parts[$i]);
			}
		}

		$re .= "</li>\n</ol>";

		return $re;
	}

	function is_u_list($text) {
		return preg_match("/\A[ \t]+[-\+\*][ \t](.|\n)*\z/u", $text);
	}

	function do_u_list($text) {
		$parts = preg_split("/\n/u", $text);

		$re = "<ul>\n";

		for ($i = 0; $i < count($parts); $i++) {
			if (preg_match("/\A\s+[-\+\*]\s.*\z/u", $parts[$i])) {
				if ($i == 0) {
					$re .= "\t<li>" . StructuredText::convert_text(preg_replace("/\A\s*[-\+\*]/u", "", $parts[$i]));
				} else {
					$re .= "</li>\n\t<li>" . StructuredText::convert_text(preg_replace("/\A\s*[-\+\*]/u", "", $parts[$i]));
				}
			} else {
				$re .= StructuredText::convert_text($parts[$i]);
			}
		}

		$re .= "</li>\n</ul>";

		return $re;
	}

	// in this list definition term is [^<>\f\n\r\v\u00A0\u2028\u2029]+ so it
	// can
	// contain no markup at all. Otherwise there will be problem with checking
	// consistency
	// now solved with convert_text()
	function is_definition_list($text) {
		return preg_match("/\A[ \t]+[^\f\n\v\x{00A0}\x{2028}\x{2029}]+[ \t]--(.|\n)*\z/u", $text);
	}

	function do_definition_list($text) {
		$parts = preg_split("/\n/u", $text);
		$re = "<dl>\n";

		for ($i = 0; $i < count($parts); $i++) {
			if (preg_match("/\A\s+[^\f\v\x{00A0}\x{2028}\x{2029}]+\s--.*\z/u", $parts[$i])) {
			//if (preg_match("/\A\s+[^\f\v\u00A0\u2028\u2029]+\s--.*\z/u", $parts[$i])) {
				preg_match("/\A\s*(\s[^\f\v\x{00A0}\x{2028}\x{2029}]+)--(.*)\z/u", $parts[$i], $matches);
				if ($i == 0) {
					$re .= "\t<dt>" . StructuredText::convert_text($matches[1]) . "</dt>\n";
					$re .= "\t<dd>" . StructuredText::convert_text($matches[2]);
				} else {
					$re .= "</dd>\n";
					$re .= "\t<dt>" . StructuredText::convert_text($matches[1]) . "</dt>\n";
					$re .= "\t<dd>" . StructuredText::convert_text($matches[2]);
				}
			} else {
				$re .= StructuredText::convert_text($parts[$i]);
			}
		}

		$re .= "</dd>\n</dl>";

		return $re;
	}

	function is_table($text) {
		if (!preg_match("/\A(.+\n)?[ \t]*\+-+\+\n([ \t]*\|.+\|\n)[ \t]*\|=+\|\n([ \t]*\|.+\|\n)+[ \t]*\+-+\+\z/u", $text)) {
			return false;
		}

		$txt = preg_replace("/\A([^\+\|].*\n)?/u", "", $text); // remove caption

		// now we have raw table
		$lines = preg_split("/\n/u", $txt);

		// asert
		if (count($lines) < 5) {
			return false;
		}

		// check if lines are equaly long
		for ($i = 0, $first = count($lines[0]); $i < count($lines); $i++) {
			if (count($lines[$i]) != $first) {
				return false;
			}
		}

		// trim leading white spaces
		for ($i = 0; $i < count($lines); $i++) {
			$lines[$i] = preg_replace("/\A[ \t]*/u", "", $lines[$i]);
		}

		// table head dictates table form
		$txt = preg_replace("/\A\|/u", "", $lines[1]);
		$txt = preg_replace("/\|\z/u", "", $txt);
		$thead = preg_split("/\|/u", $txt);

		// construct form
		$form_re = "\|";
		foreach ($thead as $element) {
			$ch = "";
			for ($l = 0; $l < mb_strlen($element, 'UTF-8'); $l++) {
				$ch .= ".";
			}
			$form_re .= $ch . "\|";
		}
		$form_re = "\A" . $form_re . "\z";

		// control form of lines inside table
		$delimiter = false;
		for ($i = 3; $i < (count($lines) - 1); $i++) {
			if (preg_match("/\|-+\|/u", $lines[$i])) {
				if (($i == 3) || ($i == (count($lines) - 2))) {// first or last line can not be table row delimiter
					return false;
				}
				if ($delimiter) { // there can not be two table row delimeters one after another
					return false;
				}
				$delimiter = true;
				continue;
			}

			if (!(preg_match("/" . $form_re . "/u", $lines[$i]))) {
				return false;
			}
			$delimiter = false;
		}

		return true; // now we shold have valid table
	}

	function do_table($text) {
		$re = "<table>\n";

		// caption
		$caption = preg_split("/\n/u", $text)[0];
		$caption = preg_match("/\A\+.*/u", $caption) ? "" : $caption;
		$re .= "\t<caption>" . StructuredText::convert_text($caption) . "</caption>\n";

		$txt = preg_replace("/\A([^\+\|].*\n)?/u", "", $text);// remove caption

		// now we have raw table
		$lines = preg_split("/\n/u", $txt);

		// trim leading white spaces
		for ($i = 0; $i < count($lines); $i++) {
			$lines[$i] = preg_replace("/\A[ \t]*/u", "", $lines[$i]);
		}

		// table head dictates table form
		$txt = preg_replace("/\A\|/u", "", $lines[1]);
		$txt = preg_replace("/\|\z/u", "", $txt);
		$thead = preg_split("/\|/u", $txt);

		$columns = count($thead);

		// construct form
		$form_re = "\|";
		foreach ($thead as $element) {
			$ch = "";
			for ($l = 0; $l < mb_strlen($element, 'UTF-8'); $l++) {
				$ch .= ".";
			}
			$form_re .= "(" . $ch . ")" . "\|";
		}
		$form_re = "\A" . $form_re . "\z";

		// thead
		$re .= "\t<thead>\n\t\t<tr>\n";
		for ($i = 0; $i < $columns; $i++) {
			$re .= "\t\t\t<th>" . trim(StructuredText::convert_text($thead[$i])) . "</th>\n";
		}
		$re .= "\t\t</tr>\n\t</thead>\n\t<tbody>\n";

		// tbody
		$stack = [];
		for ($p = 0; $p < $columns; $p++) {
			$stack[$p] = "";
		}
		for ($i = 3; $i < (count($lines) - 1); $i++) {
			if (!(preg_match("/\|-+\|/u", $lines[$i]))) {
				// fill stack
				preg_match("/" . $form_re . "/u", $lines[$i], $matches);
				for ($p = 0; $p < $columns; $p++) {
					$stack[$p] .= $matches[$p + 1];
				}
			}

			if (preg_match("/\|-+\|/u", $lines[$i]) || ($i == (count($lines) - 2))) {
				// empty stack
				$re .= "\t\t<tr>\n";
				for ($p = 0; $p < $columns; $p++) {
					$re .= "\t\t\t<td>" . trim(StructuredText::convert_text($stack[$p])) . "</td>\n";
				}

				$re .= "\t\t</tr>\n";

				// clear
				for ($p = 0; $p < $columns; $p++) {
					$stack[$p] = "";
				}
			}
		}

		$re .= "\t</tbody>\n</table>";

		return $re;
	}

}


/*-----------------------------------------------------------------------------
 * test
$structuredText = "This is structure text example"
		. "\n"
		. "=============================="
		. "\n"
		. "\n\n\n\n"
		. "Introduction"
		. "\n"
		. "------------"
		. "\n\n\n\n"
		. " & & & < >"
		. "\n\n"
		. "------------"
		. "\n\n\n\n"
		. ""
		. "\n"
		. "Simple paragraph with some 1_{2} features 1^{2}: *strong*  *strong1*,"
		. "\n"
		. " _emphasized_  _emphasized_ text. Or some [link](http://www.google.com),"
		. "\n"
		. " image {apple}(http://www.apple.com/favicon.ico \"apple\")"
		. "\n"
		. " or even image in link"
		. "\n"
		. " [{apple}(http://www.apple.com/favicon.ico)](http://www.apple.com \"apple site\")."
		. "\n" . "" . "\n"
		. "Advance" . "\n"
		. "-------" . "\n"
		. "" . "\n"
		. "Advantages" . "\n"
		. "" . "\n"
		. " 1. _nice_ look in text editor" . "\n"
		. " 2. *nice* look in browser" . "\n"
		. " # nice look in browser" . "\n"
		. "" . "\n"
		. " - *nice* look in text editor" . "\n"
		. " * nice look in browser" . "\n"
		. "" . "\n"
		. " aaaa -- nice look www.apple.com in text aaa@mail.net editor" . "\n"
		. " bbbb -- nice look in browser" . "\n"
		. "" . "\n"
		. "\n"
		. " *Salsa víkend = Hudba + tanec + príroda + dobrá nálada* " . "\n"
		. "\n"
		. "> KDE: V lone prírody malebnej dedinky *KĽAČNO*" . "\n"
		. "\n"
		. "table caption" . "\n"
		. "+--------+" . "\n"
		. "|1  ť|2  |" . "\n"
		. "|========|" . "\n"
		. "|1 č |2  |" . "\n"
		. "|--------|" . "\n"
		. "|3   |4  |" . "\n"
		. "+--------+" . "\n"
		. "" . "\n"
		. "|" . "\n"
		. "/ *code snippet* /" . "\n"
		. "function(param1, param2)"
		. "\n"
		. "{" . "\n"
		. "    return this;" . "\n"
		. "};"
		. "\n"
		. "" . "\n"
		. ">" . "\n"
		. "/ *code snippet* /" . "\n"
		. "" . "\n"
		. "-------" . "\n";

echo "=========================================================================\n";
echo $structuredText;

$htmlText = StructuredText::convertToHtml($structuredText);

echo "=========================================================================\n";
echo $htmlText . "\n";

 */

?>

