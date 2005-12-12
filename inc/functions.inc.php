<?php
/**
 * The equivalent of "rm -rf" from the *nix shell.
 * Even removes any hidden files.
 *
 * @author		stefano at takys dot it
 * @source		http://de3.php.net/manual/de/function.rmdir.php; 24-Nov-2005
 * @license		public domain
 *
 * @param	$dir	Absolute path to the directory to be deleted.
 * @return		Nothing. Don't rely on it.
 */
function rm_r($dir) {
	if(!$dh = @opendir($dir))
		return;
	while (($obj = readdir($dh))) {
		if($obj=='.' || $obj=='..')
			continue;
		if (!@unlink($dir.'/'.$obj))
			rm($dir.'/'.$obj);
	}
	@rmdir($dir);
}

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
	return preg_replace(	array(	'/(\s*(?:-{2,6}\s?Original|^_{5,}|^On.*\w:|^\>From.*|^--\s?\n\w)(?:.*\s?)*)/m',
					'/((?:^(\>|&gt;|\#|\|).*\s*)+)(?:\s|$)/m',
				),
				array(	'',
					'<span class="quote">\1</span>',
				),
				$text);
}

?>
