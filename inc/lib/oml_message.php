<?php
class oml_message
	extends OMLObject
{
	// due to OMLObject:
	public static $schema_file	= './inc/database/message.adodb.txt';
	protected $unique_key		= 'mid';

	// variables for own purpose
	private static $rex_name	= '/([\w][\w0-9\-\.\,\s]+[\w0-9\.])\s*\</i';
	private static $rex_email	= '/\<([\w0-9][\w0-9\.\-\_\+]{1,}@[\w0-9\.\-\_]{2,}\.[\w]{2,})\>/i';
	private static $essen_subject	= '/(?:re|aw|fwd)?:?\s?(?:\[.*\])?\s?(.+)\s*(?:\(was:.*\))?/i';

	/**
	 * @returns integer	0 if failure, 1 if errors, 2 if successful
	 * @see_also		adodb: ExecuteSQLArray and table creation
	 */
	public static function create_your_table(ADOConnection $db, $tablename) {
		$flds		= file_get_contents(self::$schema_file);
		$taboptarray	= array('mysql' => 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$dict = NewDataDictionary($db);

		$sqlarray = $dict->CreateTableSQL($tablename, $flds, $taboptarray);
		if($dict->ExecuteSQLArray($sqlarray)) {
			$sqlarray = $dict->CreateIndexSQL('tid', $tablename, 'tid');
			$dict->ExecuteSQLArray($sqlarray);
			$sqlarray = $dict->CreateIndexSQL('message_id', $tablename, 'message_id', array('UNIQUE'));
			return $dict->ExecuteSQLArray($sqlarray);
		} else {
			throw new Exception('Table "'.$tablename.'" could not be created.');
			return false;
		}
	}

	/**
	 * A thread or other structure might be interested in getting a lot of messages.
	 * This function is to suit that purpose.
	 * @returns array	array of oml_messages belonging to that Thread_ID
	 */
	public static function get_messages_of(ADOConnection $db, oml_factory $factory, $tablename, $thread_id) {
		$result = array();
		$rs = $db->Execute('SELECT * FROM '.$tablename.' WHERE tid='.$thread_id);
		foreach($rs as $row) {
			$tmp		= $factory->get_message();
			$tmp->become($row);
			$result[]	= $tmp;
		}
		return $result;
	}

	public static function get_thread_with(ADOConnection $db, oml_factory $factory, $tablename, $message_id) {
		$tid	= $db->GetOne('SELECT tid FROM '.$tablename.' WHERE message_id='.$db->qstr($message_id).' ORDER BY datereceived DESC');
		if(!$tid === false) {
			$tmp	= $factory->get_thread($tid);
			return $tmp;
		}
		return false;
	}

	public static function get_message_quoted_by(ADOConnection $db, oml_factory $factory, $tablename, oml_message $msg) {
		// TODO
		return false;
	}

	public function associate_with_thread(oml_thread $thread) {
		$this->setter('tid', $thread->get_unique_value());
	}

	/**
	 * It might be that we have to strip tags or convert to special entities.
	 *
	 * @returns	string with text. At emails we call this "first displayable part (of body)".
	 */
	public function get_text($strip_tags = false) {
		if($strip_tags) {
			return strip_tags(imap_qprint($this->getter('msgtext')));
		} else {
			return imap_qprint($this->getter('msgtext'));
		}
	}

	/**
	 * Sets the msgText and strips any HTML or PHP Code, if necessary.
	 *
	 * @param string	text to be set
	 * @param boolean	true for removing tags. false is default
	 */
	public function set_text($text, $strip_tags = false) {
		$text	= imap_qprint($text);
		if($strip_tags) {
			$this->setter('msgtext', strip_tags($text));
		} else {
			$this->setter('msgtext', $text);
		}
	}

	/**
	 * returns	string if successfully determined the sender's name, else false
	 */
	public function get_senders_name() {
		if(preg_match(self::$rex_name, $this->getter('sender'), $arr)) {
			return trim($arr[1]);
		}
		return false;
	}
	/**
	 * returns	string if successfully determined the sender's email, else false
	 */
	public function get_senders_email() {
		if(preg_match(self::$rex_email, $this->getter('sender'), $arr)) {
			return $arr[1];
		}
		return false;
	}

	public function get_author() {
		$name = $this->get_senders_name();
		if($name === false) {
			if(preg_match('/(\w+).*\@(\w+)/i', $this->get_senders_email(), $arr)) {
				$localpart	= str_capitalize($arr[1]);
				$domain		= str_capitalize($arr[2]);
				$name		= sprintf('%s from %s', $localpart, $domain);
			} else {
				$name	= 'unknown';
			}
		}
		return $name;
	}

	public function get_subject() {
		return $this->getter('subject');
	}

	public function get_essence_of_subject() {
		if(preg_match(self::$essen_subject, $this->get_subject(), $arr)) {
			return $arr[1];
		} else {
			throw new Exception('No suitable subject for naming a new thread was found. Subject was "'.$this->get_subject().'".');
		}
	}

	private function get_date($send_or_received, $format = null) {
		if(is_null($format)) {
			return $this->getter($send_or_received);
		} else {
			return date($format, $this->getter($send_or_received));
		}
	}

	public function get_date_send($format = null) {
		return $this->get_date('datesend', $format);
	}

	public function get_date_received($format = null) {
		return $this->get_date('datereceived', $format);
	}

	/**
	 * Lässt eine Nachricht so tun, als enthielte sie die gegebenen Daten.
	 * Hilfsmethode zum Speichern völlig neuer Nachrichten.
	 */
	public function let($message_id, $DateSend, $DateReceived, $Sender, $Subject, $hasAttachments, $MsgText) {
		$this->become(
		  array('message_id'		=> $message_id,
			'datesend'		=> $DateSend,
			'datereceived'		=> $DateReceived,
			'sender'		=> $Sender,
			'subject'		=> $Subject,
			'hasattachments'	=> $hasAttachments ? 1 : 0,
			));
		$this->set_text($MsgText);
	}

	/**
	 * Wenn viele Nachrichten aus der Datenbank gelesen werden, bietet es sich an,
	 * sie so zu konstruieren und einzelne Querries zu vermeiden.
	 */
	public function be($mid, $message_id, $DateSend, $DateReceived, $Sender, $Subject, $hasAttachments, $MsgText) {
		$this->setter('mid', $mid);
		$this->let($message_id, $DateSend, $DateReceived, $Sender, $Subject, $hasAttachments, $MsgText);
	}

	public function get_owning_thread() {
		return $this->factory->get_thread($this->getter('tid'));
	}

}

?>