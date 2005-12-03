<?php
class oml_message
	extends OMLObject
{
	// due to OMLObject:
	public static $schema_file	= './inc/database/message.adodb.txt';
	// variables for own purpose
	protected $mid			= null;	// null means: not in or taken from db -> new, pristine
	private static $rex_name	= '/([\w][\w0-9\-\.\,\s]+[\w0-9\.])\s*\</i';
	private static $rex_email	= '/\<([\w0-9][\w0-9\.\-\_\+]{1,}@[\w0-9\.\-\_]{2,}\.[\w]{2,})\>/i';
	// see table for them, but access carefully:
	public $data			= array();

	/**
	 * @returns integer	0 if failure, 1 if errors, 2 if successful
	 * @see_also		adodb: ExecuteSQLArray and table creation
	 */
	public static function create_your_table(NewADOConnection $db, $tablename) {
		$filds		= file_get_contents(self::$schema_file);
		$taboptarray	= array('mysql' => 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');
		$idxname = 'message_id';
		$idxflds = 'message_id';
		$idxopts = array('UNIQUE');

		$dict = NewDataDictionary($db);

		$sqlarray = $dict->CreateTableSQL($tablename, $flds, $taboptarray);
		if($dict->ExecuteSQLArray($sqlarray)) {
			$sqlarray = $dict->CreateIndexSQL($idxname, $tablename, $idxflds, $idxopts);
			return $dict->ExecuteSQLArray($sqlarray);
		} else {
			throw new Exception('Table "'.$tablename.'" could not be created.');
			return false;
		}
	}

	/**
	 * Writes changes (or even new data) to db.
	 */
	public function write_to_db() {
		if(isset($this->data['mid']) && is_null($this->data['mid'])) {
			unset($this->data['mid']);
		}
		if(!isset($this->data['mid'])) {
			$result = $this->db->AutoExecute($this->table, $this->data, 'INSERT');
			if($result) {
				$tmp = $this->db->Insert_ID();
				if(!$tmp === false) {
					$this->data['mid'] = $tmp;
				} else {
					$this->data['mid'] = $this->db->GetOne('SELECT MID FROM '.$this->table.' WHERE message_id='.$this->db->qstr($this->data['message_id']));
				}
				return true;
			}
			return false;
		} else {
			$result = $this->db->Replace($this->table, $this->data, 'mid', true);
			return ($result > 0);
		}
	}

	/**
	 * Execute if you want this message be filled with contents from an already existing entry.
	 * Changes made prior to it's call will be discarded, so don't forget serialization.
	 *
	 * @returns boolean	whether MID was found an we acquired data successfully
	 */
	public function assign_mid($mid) {
		$rs = $this->db->Execute('SELECT * FROM '.$this->table.' WHERE MID='.$mid);
		if(!$rs === false) {
			$this->data = $rs->fields;
			return true;
		}
		return false;
	}

	/**
	 * It might be that we have to strip tags or convert to special entities.
	 *
	 * @returns	string with text. At emails we call this "first displayable part (of body)".
	 */
	public function get_text($strip_tags = false) {
		if($strip_tags) {
			return strip_tags($this->msgText);
		} else {
			return $this->msgText;
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
			$this->msgText = strip_tags($text);
		} else {
			$this->msgText = $text;
		}
	}

	/**
	 * returns	string if successfully determined the sender's name, else false
	 */
	public function get_senders_name() {
		if(isset($this->data['Sender'])
		   && preg_match($this->rex_name, $this->data['Sender'], $arr)) {
			return trim($arr[1]);
		}
		return false;
	}
	/**
	 * returns	string if successfully determined the sender's email, else false
	 */
	public function get_senders_email() {
		if(isset($this->data['Sender'])
		   && preg_match($this->rex_email, $this->data['Sender'], $arr)) {
			return $arr[1];
		}
		return false;
	}

}

?>