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

	private $attachments		= array();

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
	 *
	 * @return	array of oml_messages belonging to that thread_id
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

	/**
	 * Registering messages needs this.
	 */
	public static function get_thread_with(ADOConnection $db, oml_factory $factory, $tablename, $message_id) {
		$tid	= $db->GetOne('SELECT tid FROM '.$tablename.' WHERE message_id='.$db->qstr($message_id).' ORDER BY datereceived DESC');
		if(!$tid === false) {
			$tmp	= $factory->get_thread($tid);
			return $tmp;
		}
		return false;
	}

	/**
	 * Registering messages needs this.
	 *
	 * @see		oml_list::register_message
	 */
	public function associate_with_thread(oml_thread $thread) {
		return $this->tid	= $thread->get_unique_value();
	}

	/**
	 * It might be that we have to strip tags or convert to special entities.
	 *
	 * @return	String with text. At emails we call this "first displayable part (of body)".
	 */
	public function get_text($strip_tags = false) {
		if($strip_tags) {
			return strip_tags(imap_qprint($this->msgtext));
		} else {
			return imap_qprint($this->msgtext);
		}
	}

	/**
	 * Enforces decoding of messages which contain UTF7 characters.
	 *
	 * @param	text	Text to be written to DB.
	 */
	public function set_text($text) {
		$text	= imap_qprint($text);
		$this->msgtext	= $text;
	}

	/**
	 * The given message_id is necessary for keeping track of threads.
	 *
	 * @param	message_id	... of message this is the reply to. Need not exist in DB.
	 */
	public function set_in_reply_to($message_id) {
		$this->in_reply_to	= $message_id;
	}

	/**
	 * @param	references	References as list of message_ids.
	 */
	public function set_referenced($references) {
		$this->refers	= $references;
	}

	/**
	 * @return		message_id of message this one replies to
	 * @warning		If this is not a reply an exception might be thrown by DataCarrier::__get.
	 */
	public function get_in_reply_to() {
		return $this->in_reply_to;
	}

	/**
	 * @return		References as list of message_ids.
	 */
	public function get_referenced() {
		return $this->refers;
	}

	/**
	 * @return	String if successfully determined the sender's name, else false.
	 */
	public function get_senders_name() {
		if(preg_match(self::$rex_name, $this->sender, $arr)) {
			return trim($arr[1]);
		}
		return false;
	}

	/**
	 * @return	String if successfully determined the sender's email, else false.
	 */
	public function get_senders_email() {
		if(preg_match(self::$rex_email, $this->sender, $arr)) {
			return $arr[1];
		}
		return false;
	}

	/**
	 * This shall ensure a non-empty value is returned.
	 *
	 * @see			get_senders_name, get_senders_email
	 */
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

	/**
	 * Subject can also be called "topic".
	 */
	public function get_subject() {
		return $this->subject;
	}

	/**
	 * Subject could contain tags like "Re:" or badges like "[mylist]",
	 * which are ugly in names of threads.
	 *
	 * @throw		If subject is empty or does only consist of tags and badges.
	 */
	public function get_essence_of_subject() {
		if(preg_match(self::$essen_subject, $this->get_subject(), $arr)) {
			return $arr[1];
		} else {
			throw new Exception('No suitable subject for naming a new thread was found. Subject was "'.$this->get_subject().'".');
		}
	}

	/**
	 * @param	send_or_received	Either 'datesend' or 'datereceived' as found in email headers.
	 * @param	format			Formatting string as demanded by PHP's date()
	 * @see					http://de3.php.net/manual/en/function.date.php
	 */
	private function get_date($send_or_received, $format = null) {
		if(is_null($format)) {
			return $this->{$send_or_received};
		} else {
			return date($format, $this->{$send_or_received});
		}
	}

	/**
	 * Wrapper for get_date.
	 * @see		get_date
	 */
	public function get_date_send($format = null) {
		return $this->get_date('datesend', $format);
	}

	/**
	 * Wrapper for get_date.
	 * @see		get_date
	 */
	public function get_date_received($format = null) {
		return $this->get_date('datereceived', $format);
	}

	/**
	 * Lets the current message be one with the given values.
	 * Used in creation of new messages with defined contents.
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
	 * Used at displaying threads' messages.
	 */
	public function get_owning_thread() {
		return $this->factory->get_thread($this->tid);
	}

	/**
	 * @return		Boolean
	 */
	public function has_attachments() {
		return ($this->hasattachments == 1);
	}

	public function add_attachment(oml_attachment $att) {
		$att->register_message($this);
		$this->attachments[$att->get_unique_value()]	= $att;
		$this->hasattachments	= 1;
		$this->write_to_db();
	}

	public function remove_attachment(oml_attachment $att) {
		if(isset($this->attachments[$att->get_unique_value()])) {
			unset($this->attachments[$att->get_unique_value()]);
			if(count($this->attachments < 1)) {
				$this->hasattachments	= 0;
			}
		}
	}

	/**
	 * @return		Attachments as array, unique_id as key and oml_attachment objects as value.
	 */
	public function get_attachments() {
		if($this->hasattachments == 1 && count($this->attachments) == 0) {
			$this->attachments	= $this->factory->get_attachments_of_mid($this->get_unique_value());
		}
		return	$this->attachments;
	}

}

?>