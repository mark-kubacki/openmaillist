<?php
/**
 * Interestingly, MIME parts can be nested. Any email is a MIME part.
 *
 * @author		W-Mark Kubacki; wmark@hurrikane.de
 * @version		$LastChangedDate$ $LastChangedBy$
 * @see			<a href="http://www.ietf.org/rfc/rfc1341.txt">RFC 1341</a>
 */
class MIME_Part
{
	protected	$header;
	protected	$body;

	/** Regexp, divides header and body parts. */
	const	rex_header_body_split	= '/(.*?)\r?\n\r?\n(.*)/s';
	/** Regexp, detects "key: value" pairs with multiline values. */
	const	rex_key_value_ml	= '/([^:\s]+):\s([^\r\n]+(?:\r?\n\s+[^\r\n]+)*)/';
	/** This one catches boundaries. */
	const	rex_boundary		= '/boundary=\"(.*)\"/s';

	/**
	 * @param	raw_part	Mimepart as raw text.
	 * @param	recursive	Whether to decode nested parts (true) or leave them as they are (false).
	 * @throw		InvalidArgumentException if raw_message cannot be split into header and body.
	 */
	public function __construct($raw_part, $recursive = true) {
		if(preg_match(MIME_Part::rex_header_body_split, $raw_part, $arr)) {
			unset($arr[0]);
			$this->header	= $this->make_header_hash(trim($arr[1]));
			$this->body	= trim($arr[2]);
			unset($arr);
			$this->decode_body();
			if($recursive) {
				$this->recurse_if_multipart();
			}
		} else {
			throw new InvalidArgumentException();
		}
	}

	/**
	 * This one is to be called if iconv_mime_decode_headers does not exist.
	 * 
	 * @return		Hash with headers.
	 */
	private function decode_headers($raw_header_part) {
		$tmp	= array();
		if(preg_match_all(MIME_Part::rex_key_value_ml, $raw_header_part, $arr)) {
			unset($arr[0]);
			for($i = 0; isset($arr[1][$i]); $i++) {
				$key	= $arr[1][$i];
				$value	= $arr[2][$i];
				if(isset($tmp[$key])) {
					if(!is_array($tmp[$key])) {
						$tmp[$key] = array($tmp[$key]);
					}
					array_push($tmp[$key], $value);
				} else {
					$tmp[$key] = $value;
				}
			}
		}
		return $tmp;
	}

	/**
	 * @return		Hash with headers and lowercase keys.
	 */
	private function make_header_hash($raw_header_part) {
		if(function_exists('iconv_mime_decode_headers')) {
			$tmp = iconv_mime_decode_headers($raw_header_part, 2);
		} else {
			$tmp = $this->decode_headers($raw_header_part);
		}
		if(is_array($tmp)) {
			return array_change_key_case($tmp, CASE_LOWER);
		} else {
			return array();
		}
	}

	private function decode_body() {
		if(isset($this->header['content-transfer-encoding'])) {
			switch($this->header['content-transfer-encoding']) {
				case 'quoted-printable':
					if(function_exists('imap_qprint')) {
						$this->body	= imap_qprint($this->body);
					} else {
						$this->body	= quoted_printable_decode($this->body);
					}
					break;
				case 'base64':
					$this->body	= base64_decode($this->body);
					break;
				case '7bit':
				case '8bit':
				default:
					// do nothing
					break;
			}
		}
	}

	/**
	 * @return		Boolean whether further recursion was successfull. Always true if none was needed.
	 */
	private function recurse_if_multipart() {
		if(isset($this->header['content-type']) && strstr($this->header['content-type'], 'multipart')) {
			if(!preg_match(MIME_Part::rex_boundary, $this->header['content-type'], $arr)) {
				return false;
			}
			$boundary	= str_replace(array("\n", "\r"), array('', ''), $arr[1]);
			$arr_body	= explode('--'.$boundary, $this->body);
			// [preamble] + {parts} + suffix -- else someone's mailer is messed up
			if(count($arr_body) > 2) {
				unset($arr_body[0]);
				unset($arr_body[count($arr_body)]);
				array_walk($arr_body,
					create_function('&$item,$index',
							'$item = new MIME_Part($item, false);'
							));
				$this->body	= $arr_body;
				return true;
			}
			return false;
		}
		return true;
	}

	/**
	 * @return		Desired field's contents or false, if the field does not exist.
	 */
	public function __get($key) {
		if('body' == $key) {
			return $this->body;
		} else {
			if(array_key_exists($key, $this->header)) {
				return $this->header[$key];
			} else {
				return false;
			}
		}
	}

	/**
	 * @return		Boolean
	 */
	public function __isset($key) {
		return array_key_exists($key, $this->header);
	}

}
?>