<?php
/**
 * Every email can be seen as a MIME_Part with some special headers.
 * This class is to make handling with it's recursive structure and headers easier.
 *
 * @author		W-Mark Kubacki; wmark@hurrikane.de
 * @version		$LastChangedDate$ $LastChangedBy$
 * @see			<a href="http://www.ietf.org/rfc/rfc2822.txt">RFC 2822</a>
 */
class oml_email
	extends MIME_Part
{
	/** Additional information as auxiliary header to ease information extraction. */
	protected	$aux_headers	= array();
	/** We must know where to write the attachments. */
	protected	$attachment_dir	= '/tmp';
	/** Regexp, to be applied on 'received' fields for detecting the true recipient. */
	const	rex_recevied_recipient	= '/for (\<.+\>)/s';

	public function __construct($raw_part) {
		parent::__construct($raw_part);
		$this->aux_headers	= array();

		$this->determine_datae();
		$this->determine_recipient();
		if(isset($this->header['message-id'])) {
			$this->aux_headers['message-id']	= substr($this->header['message-id'], 1, -1);
		}
	}

	/**
	 * We don't trust any user's PC but we trust our server and the other servers a bit.
	 * Therefore preferanly taking date/times from field 'received' is the consequence.
	 */
	private function determine_datae() {
		if(isset($this->header['received'])) {
			$i = count($this->header['received']) - 1;
			$this->aux_headers['date-received']	= strtotime(trim(substr(strrchr($this->header['received'][0], ';'), 2)));
			if($i > 0) {
				$datum = trim(substr(strrchr($this->header['received'][$i], ';'), 2));
				$this->aux_headers['date-send']		= strtotime($datum);
			}
			else {
				$this->aux_headers['date-send']		= $this->aux_headers['date-received'];
			}
		} else {
			// this could be the case when we got a draft for analyzation
			if(isset($this->header['date'])) {
				$this->aux_headers['date-send']		= strtotime($this->header['date']);
				$this->aux_headers['date-received']	= strtotime($this->header['date']);
			}
		}
	}

	/**
	 * What is in field 'to' need not be the true recipient.
	 */
	private function determine_recipient() {
		$this->aux_headers['_recipient']	= $this->header['to'];
		if(isset($this->header['received'])) {
			foreach($this->header['received'] as $receive_line) {
				if(preg_match(oml_email::rex_recevied_recipient, $receive_line, $arr)) {
					$this->aux_headers['_recipient'] = $arr[1];
				}
			}
		}
	}

	/**
	 * This is a wrapper which ensures the message has already been processed
	 * and this' class cache is used.
	 *
	 * @param $key		Field of the header. (Lowercase)
	 * @return		Value of that field.
	 * @throw		If header-field does not exist or message is invalid.
	 */
	public function get_header($key) {
		if(isset($this->aux_headers[$key])) {
			return $this->aux_headers[$key];
		} else {
			return $this->header[$key];
		}
	}

	/**
	 * In case you allow optional fields you could utilize this.
	 */
	public function has_header($key) {
		return isset($this->header[$key]);
	}

	/**
	 * This functions set where attachments will be written to.
	 *
	 * @param $where	Has to be the absolute path without trailing slash to the location where the attachments will be stored.
	 * @return		True if the given path exists, is a directory and writeable.
	 */
	public function set_attachment_storage($where) {
		if(is_dir($where) && is_writable($where)) {
			$this->attachment_dir = $where;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @return		Boolean.
	 */
	public function has_attachments() {
		return is_array($this->body);
	}

	/**
	 * @return		first displayable part or empty string
	 * @throw		UnderflowException if no displayable part can be found.
	 */
	public function get_first_displayable_part() {
		if(!$this->has_attachments()) {
			return $this->body;
		} else {
			foreach($this->body as $mime_part) {
				if(strstr($mime_part->{'content-type'}, 'text/plain')) {
					return $mime_part->body;
				}
			}
			throw new UnderflowException();
		}
	}

}
?>