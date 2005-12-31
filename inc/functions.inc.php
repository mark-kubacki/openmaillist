<?php
/**
 * Codiert einen String in Unicode-Schreibweise. (Dezimal oder Hex-Schreibweise)
 *
 * @param $string	die umzuwandelnde Zeichenkette
 * @param $hex		soll die Ausgabe als Hex-Code erfolgen? sonst Zahl
 * @return		html unicode entities der Zeichenkette (in iso8859-1)
 */
function UniCodeString($string, $hex = true) {
	$len = strlen($string);
	$encoded = '';
	if($hex) {
		for($i = 0; $i < $len; $i++) {
			$encoded .= '&#x'.base_convert(ord(substr($string,$i)), 10, 16).';';
		}
	}
	else {
		for($i = 0; $i < $len; $i++) {
			$encoded .= '&#'.ord(substr($string,$i)).';';
		}
	}
	return $encoded;
}

/**
 * Wandelt einen String in Unicode um, auf dass Spam-Crawler ihn nicht erfassen.
 * Ein wrapper für UniCodeString.
 *
 * @param $string	die umzuwandelnde Zeichenkette
 * @return		html unicode entities der Zeichenkette (in iso8859-1)
 */
function EncodeEmail($string) {
	return UniCodeString($string, true);
}

/**
 * Makes the first letter an uppercase one.
 *
 * @return		Capitalized string.
 */
function str_capitalize($string) {
	if(strlen($string) > 0) {
		$string{0}	= strtoupper($string{0});
		return $string;
	}
	return $string;
}

/**
 * This is so we can use classes and interfaces without typeing long include lists.
 */
function __autoload($class_name) {
	require_once('./inc/lib/'.$class_name.'.php');
}

function format_quotings($text) {
	for($i = 1; $i < 6; $i++) {
		$text	= preg_replace(	'/((?:^(?:\>|&gt;|\#|\|){'.$i.'}[^\n\r]+\r?\n?)+)/m',
					'<span class="quote level'.$i.'">\1</span>',
					$text);
	}
	return preg_replace(	array(	'/(\s*(?:^_{5,}|^--\s?\n\w)(?:.*\s?)*)/m',
					'/((?:http|https|svn|ftp):\/\/\S{5,})/',
				),
				array(	'',
					'<a href="\1" title="URL">\1</a>',
				),
				$text);
}

/**
 *
 */
function format_for_rss($text, $length = 0, $append = '...') {
	$text	= strip_tags($text);
	if($length - strlen($append) > 0 && strlen($text) > $length) {
		$text	= substr($text, 0, $length - strlen($append)).$append;
	}
	$text	= utf8_encode(htmlspecialchars($text));

	return $text;
}
?>
