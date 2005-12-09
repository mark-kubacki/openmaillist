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
	 * It might be that we have to strip tags or convert to special entities.
	 *
	 * @returns	string with text. At emails we call this "first displayable part (of body)".
	 */
	public function get_text($strip_tags = false) {
		if($strip_tags) {
			return strip_tags($this->getter('msgtext'));
		} else {
			return $this->getter('msgtext');
		}
	}

	/**
	 * Sets the msgText and strips any HTML or PHP Code, if necessary.
	 *
	 * @param string	text to be set
	 * @param boolean	true for removing tags. false is default
	 */
	public function set_text($text, $strip_tags = false) {
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
		if($this->has('sender')
		   && preg_match($this->rex_name, $this->getter('sender'), $arr)) {
			return trim($arr[1]);
		}
		return false;
	}
	/**
	 * returns	string if successfully determined the sender's email, else false
	 */
	public function get_senders_email() {
		if($this->has('sender')
		   && preg_match($this->rex_email, $this->getter('sender'), $arr)) {
			return $arr[1];
		}
		return false;
	}

	/**
	 * returns	integer	the MID
	 */
	protected function get_mid() {
		if($this->has('mid')) {
			return $this->getter('mid');
		} else {
			throw new Exception('Message has not been stored, yet.');
		}
	}

	/**
	 * Lässt eine Nachricht so tun, als enthielte sie die gegebenen Daten.
	 * Hilfsmethode zum Speichern völlig neuer Nachrichten.
	 */
	public function let($message_id, $DateSend, $DateReceived, $Sender, $Subject, $hasAttachements, $MsgText) {
		$this->become(
		  array('message-id'		=> $message_id,
			'datesend'		=> $DateSend,
			'datereceived'		=> $DateReceived,
			'sender'		=> $Sender,
			'subject'		=> $Subject,
			'hasattachements'	=> $hasAttachements ? 1 : 0,
			));
		$this->set_text($MsgText);
	}

	/**
	 * Wenn viele Nachrichten aus der Datenbank gelesen werden, bietet es sich an,
	 * sie so zu konstruieren und einzelne Querries zu vermeiden.
	 */
	public function be($mid, $message_id, $DateSend, $DateReceived, $Sender, $Subject, $hasAttachements, $MsgText) {
		$this->setter('mid', $mid);
		$this->let($message_id, $DateSend, $DateReceived, $Sender, $Subject, $hasAttachements, $MsgText);
	}

	/**
	 * A thread or other structure might be interested in getting a lot of messages.
	 * This function is to suit that purpose.
	 * @returns array	array of oml_messages belonging to that Thread_ID
	 */
	public function get_messages_belonging_to($thread_id) {
		$result = array();
		$rs = $this->db->Execute('SELECT * FROM '.$this->table.' WHERE TID='.$thread_id);
		foreach($rs as $row) {
			$tmp		= $this->factory->get_message();
			$tmp->become($row);
			$result[]	= $tmp;
		}
		return $result;
	}

}

?>