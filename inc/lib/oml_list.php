<?php
class oml_list
	extends OMLObject
{
	// due to OMLObject:
	public static $schema_file	= './inc/database/list.adodb.txt';
	protected $unique_key		= 'lid';

	/* administrative */
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
			$sqlarray = $dict->CreateIndexSQL('lname', $tablename, 'lname', array('UNIQUE'));
			$dict->ExecuteSQLArray($sqlarray);
			$sqlarray = $dict->CreateIndexSQL('lemailto', $tablename, 'lemailto', array('UNIQUE'));
			return $dict->ExecuteSQLArray($sqlarray);
		} else {
			throw new Exception('Table "'.$tablename.'" could not be created.');
			return false;
		}
	}

	public static function get_all_lists(ADOConnection $db, oml_factory $factory, $tablename) {
		$result		= array();
		$rs = $db->Execute('SELECT * FROM '.$tablename);
		foreach($rs as $row){
			$tmp		= $factory->get_list();
			$tmp->become($row);
			$result[]	= $tmp;
		}
		return $result;
	}

	public static function get_list_by_name(ADOConnection $db, oml_factory $factory, $tablename, $listname) {
		$row		= $db->GetRow('SELECT * FROM '.$tablename.' WHERE lname='.$db->qstr($listname));
		if(!$row === false) {
			$theList	= $factory->get_list();
			$theList->become($row);
			return $theList;
		} else {
			throw new Exception('List does not exist.');
		}
	}

	public function create_new_thread($threadname) {
		$thread = $this->factory->get_thread();
		$thread->set_name($threadname);
		$thread->associate_with_list($this);
		return $thread;
	}

	public function get_threads() {
		return $this->factory->get_all_threads_of($this->get_unique_value());
	}

	public function register_message(oml_message $msg, $group_same_subjects = true) {
		$subject = $msg->get_essence_of_subject();

		// TODO: make sure that the thread belongs to current list!
		// In-Reply-To und References auswerten.
		$pre	= $this->factory->get_message_quoted_by($msg);
		if(!$pre === false) {
			$thread = $pre->get_owning_thread();
			return $msg->associate_with_thread($thread);
		}

		// Sonst nach bereits vorhandenem Subject fahnden.
		$thread = $this->factory->get_thread_with_name($this->get_unique_value(), $subject);
		if(!$thread === false) {
			return $msg->associate_with_thread($thread);
		}

		// Ansonsten erstelle einen neuen Thread.
		$thread = $this->create_new_thread($subject);
		return $msg->associate_with_thread($thread);
	}

	/* for generating a list */
	public function number_of_threads() {
		return $this->factory->get_num_threads_of($this->get_unique_value());
	}

	public function number_of_messages() {
		return $this->factory->get_list_num_messages($this->get_unique_value());
	}

	public function get_last_message($order_by) {
		return $this->factory->get_lists_last_message($this->get_unique_value(), $order_by);
	}

	/* now come getters and setters */
	public function get_name() {
		return $this->getter('lname');
	}
	public function get_address() {
		return $this->getter('lemailto');
	}
	public function get_description() {
		return $this->getter('ldescription');
	}

	public function set_name($txt) {
		$this->setter('lname', $txt);
	}
	public function set_address($txt) {
		if(preg_match('/([\w0-9][\w0-9\.\-\_\+]{1,}@[\w0-9\.\-\_]{2,}\.[\w]{2,})/i', $txt)) {
			$this->setter('lemailto', $txt);
		} else {
			throw new Exception('Address has to be a valid email-alias.');
		}
	}
	public function set_description($txt) {
		$this->setter('ldescription', $txt);
	}

}
?>