<?php
/**
 * Custom methods for OML reside here.
 *
 * @see		<a href="http://www.ietf.org/rfc/rfc2183.txt">RFC 2183</a>
 * @see		<a href="http://www.emaillab.org/essay/japanese-filename.html">Japanese Filename</a>
 * @todo	Implement filenames consisting of non-latin-1 chars.
 */
class oml_email
	extends MIME_Mail
{
	/** We must know where to write the attachments. */
	protected	$attachment_dir	= '/tmp';

	/**
	 * Makes sure the directory for storing attachments is writeable by OML.
	 *
	 * @param $where	Has to be the absolute path without trailing slash to the location where the attachments will be stored.
	 * @return		True if the given path exists, is a directory and writeable.
	 */
	public function set_attachment_storage($where) {
		if(is_dir($where) && is_writeable($where)) {
			$this->attachment_dir = $where;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * These are administrative emails:
	 * - disposition notifications
	 * - notices about delete mailboxes
	 * - in general, everything not written by a human
	 *
	 * @return		Boolean
	 */
	public function is_administrative() {
		if((isset($this->header['content-type']) && strstr($this->header['content-type'], 'report'))
		  || (isset($this->header['x-failed-recipients']))
		  || (isset($this->header['return-path']) && strstr(strtolower($this->header['return-path']), 'mailer-daemon'))
		  ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @return		Boolean
	 */
	public function is_disposition_notification() {
		if(isset($this->header['content-type']) && strstr($this->header['content-type'], 'disposition-notification')) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @return		Array with filenames of the successfully written attachments.
	 */
	public function write_attachments_to_disk() {
		$att	= $this->get_attachments();
		$t	= array();

		foreach($att as $name=>$data) {
			$filename = $this->attachment_dir.'/'.basename($name);
			if(is_file($filename)) {
				$t	= false;
			} else {
				file_put_contents($filename, $data);
				$t[]	= $name;
			}
		}
		return $t;
	}

	/**
	 * Wrapper for malformed headers.
	 * @see			MIME_Mail::get_header
	 */
	public function get_header($key) {
		switch($key) {
			case 'subject':
				return rawurldecode(parent::get_header($key));
				break;
			case 'in-reply-to':
				if(preg_match('/\<([^\<]+?@[^\>]+)\>/', parent::get_header($key), $arr)) {
					return $arr[1];
				}
				break;
			case 'references':
				if(preg_match_all('/\<([^\<]+?@[^\>]+)\>/', parent::get_header($key), $arr)) {
					return implode(' ', $arr[0]);
				}
				break;
		}
		return parent::get_header($key);
	}

}
?>