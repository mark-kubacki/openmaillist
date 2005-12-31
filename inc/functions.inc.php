<?php
/**
 * Converts a given string character by character in its HTML unicode entities.
 *
 * @param	string	the string to be converted
 * @param	hex	True for hex- and false for numeric representation.
 * @return		String with unicode representations. (in iso8859-1)
 */
function UniCodeString($string, $hex = true) {
	$len = strlen($string);
	$encoded = '';
	if($hex) {
		for($i = 0; $i < $len; $i++) {
			$encoded .= '&#x'.base_convert(ord(substr($string,$i)), 10, 16).';';
		}
	} else {
		for($i = 0; $i < $len; $i++) {
			$encoded .= '&#'.ord(substr($string,$i)).';';
		}
	}
	return $encoded;
}

/**
 * In order to prevent email harverster to steal addresses we are expected to protect them.
 *
 * @see			UniCodeString
 */
function EncodeEmail($string) {
	return UniCodeString($string, true);
}

/**
 * Makes the first character an uppercase one.
 *
 * @return		Capitalized string.
 */
function str_capitalize($string) {
	if(strlen($string) > 0) {
		$string{0}	= strtoupper($string{0});
	}
	return $string;
}

/**
 * This is so we can use classes and interfaces without typeing long include lists.
 */
function __autoload($class_name) {
	require_once('./inc/lib/'.$class_name.'.php');
}

/**
 * Formats plain text to be more pleasant for the user by adding colors, links and other things.
 */
function format_quotings($text) {
	for($i = 1; $i < 6; $i++) {
		$text	= preg_replace(	'/((?:^(?:(?:\>|&gt;|\#|\|) ?){'.$i.'}.*\r?\n?)+)/m',
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
 * Makes all necessary conversions of given string for being included in RSS feeds.
 *
 * @param	text	The text to be formatted and converted.
 * @param	length	If not 0, makes sure the string is no longer than given amount of characters.
 * @param	append	If length is set and given string needs to be cropped, this is appended. It's length is taken into account at cropping.
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