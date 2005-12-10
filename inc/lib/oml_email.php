<?php

include('Mail/mimeDecode.php');
include('mimedecode.php');

/**
 * This class is an specialization for OML. It's purposes are:
 * - Taking raw messages and analyzing them.
 * - Be passed as parameter in OML methods.
 *
 * See
 * . RFC 2822	- http://www.ietf.org/rfc/rfc2822.txt
 */
class oml_email {

// private:
	var	$mime_message;
	var	$decode_message;
	var	$structure;
	var	$decode_result;
	var	$hoi		= array();	// headers of interest

	var	$studied	= false;
	var	$decoded	= false;

// public:
	function oml_email(&$raw_message) {
		$this->mime_message = new Mail_mimeDecode($raw_message, "\r\n");
		$this->decode_message = new DecodeMessage();
	}

	/**
	 * Splits the message in header and body and creates an array of containing all headers.
	 * @return	false; If the given message turns out to not comply with standards.
	 */
	function study() {
		// This way we receive an hash with headers.
		$this->structure = $this->mime_message->decode();

		// Now we have to pick all the interesting values from the headers.
		$this->hoi['message-id']	= substr($this->structure->headers['message-id'], 1, -1);
		$this->hoi['from']		= $this->structure->headers['from'];
		$this->hoi['date-received']	= strtotime(substr(strrchr($this->structure->headers['received'][0], ';'), 2));
		$this->hoi['subject']		= $this->structure->headers['subject'];

		$this->hoi['_recipient']	= $this->structure->headers['to'];

		if(isset($this->structure->headers['in-reply-to'])) {
			$this->hoi['in-reply-to']	= substr($this->structure->headers['in-reply-to'], 1, -1);
		}
		if(isset($this->structure->headers['references'])) {
			$this->hoi['references']	= $this->structure->headers['references'];
		}

		// Get the chronological first "Received"-entry.
		$i = count($this->structure->headers['received']) - 1;
		if($i > 0) {
			$this->hoi['date-send']		= strtotime(substr(strrchr($this->structure->headers['received'][$i], ';'), 2));
		}
		else {
			$this->hoi['date-send']		= $this->hoi['date-received'];
		}

		$this->studied = true;

		return true;
	}

	/**
	 * Decodes given message and stores any attachments in the given directory.
	 * @return		!! currently always returns true. Will return whether decoding process was successfull.
	 */
	function decode() {
		if(!$this->studied) {
			if(!$this->study()) {
				return '';
			}
		}

		$this->decode_message->InitHeaderAndBody($this->get_header_part(), $this->get_entire_body(), $this->get_entire_msg());

		$this->decode_result = $this->decode_message->Result();
		$this->decoded = true;

		return true;
	}

	function get_header($key) {
		if(!$this->studied) {
			if(!$this->study()) {
				return '';
			}
		}

		if(isset($this->hoi[$key])) {
			return $this->$hoi[$key];
		}
		else if(isset($this->structure->headers[$key])) {
			return $this->structure->headers[$key];
		}
		else {
			return '';
		}
	}

	function get_header_part() {
		return $this->mime_message->_header;
	}

	// private
	function get_entire_body() {
		return $this->mime_message->_body;
	}

	function get_entire_msg() {
		return $this->mime_message->_input;
	}

	// public
	/**
	 * @param $where	has to be the absolute path without trailing slash to the location where the attachments will be stored
	 * @return		true if the given path exists, is a directory and writeable
	 */
	function set_attachment_storage($where) {
		$this->decode_message->attachment_path = $where;
		return is_dir($where) && is_writable($where);
	}

	function has_attachments() {
		return ($this->structure->ctype_secondary != 'plain');
	}

	/**
	 * @return	an array with all (relative) paths to the attachments.
	 */
	function get_attachments() {
		if(! $this->has_attachments()) {
			return array();
		}

		if(!$this->decoded) {
			$this->decode();
		}

		$attachments = array();

		if(isset($this->decode_result[0])) {
			for($i = 0; isset($this->decode_result[0][$i]); $i++) {
				if(isset($this->decode_result[0][$i]['attachments'])) {
					$attachments[] = $this->decode_result[0][$i]['attachments'];
				}
			}
		}

		return $attachments;
	}

	/**
	 * @param $strip_html	Whether to strip html and PHP tags if the first displayable part is marked as containing html.
	 * @retrun		first displayable part or empty string
	 */
	function get_first_displayable_part($strip_html = false) {
		if(!$this->decoded) {
			$this->decode();
		}

		if(isset($this->decode_result[0])) {
			// this is faster than foreach
			for($i = 0; isset($this->decode_result[0][$i]); $i++) {
				if(isset($this->decode_result[0][$i]['body']['type'])
				   && strstr($this->decode_result[0][$i]['body']['type'], 'text')) {

					if(!$strip_html
					   || strstr($this->decode_result[0][$i]['body']['type'], 'html')) {
						return trim($this->decode_result[0][$i]['body']['body']);
					}
					else {
						return strip_tags(trim($this->decode_result[0][$i]['body']['body']));
					}
				}
			}
		}

		return '';
	}
}
?>