<?php
/**
 * Contains specific methods for OML which need not be general applicable to emails.
 */
class oml_email
	extends MIME_Mail
{
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
}
?>