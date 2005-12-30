<?php
class oml_thread
	extends OMLObject
{
	// due to OMLObject:
	public static $schema_file	= './inc/database/thread.adodb.txt';
	protected $unique_key		= 'tid';

	/* administrative */
	public static function create_your_table(ADOConnection $db, $tablename) {
		$flds		= file_get_contents(self::$schema_file);
		$taboptarray	= array('mysql' => 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$dict = NewDataDictionary($db);

		$sqlarray = $dict->CreateTableSQL($tablename, $flds, $taboptarray);
		if($dict->ExecuteSQLArray($sqlarray)) {
			$sqlarray = $dict->CreateIndexSQL('lid', $tablename, 'lid');
			return $dict->ExecuteSQLArray($sqlarray);
		} else {
			throw new Exception('Table "'.$tablename.'" could not be created.');
			return false;
		}
	}

	/**
	 * Retrieving a particular named thread is necessary while registering a message with a list.
	 *
	 * @return		The thread or false.
	 */
	public static function get_thread_with_name(ADOConnection $db, oml_manager $factory, $tablename, $list_id, $name) {
		$result		= array();
		$tid = $db->GetOne('SELECT tid FROM '.$tablename.' WHERE lid='.$list_id.' AND threadname='.$db->qstr($name));
		if(!$tid === false) {
			return $factory->get_thread($tid);
		}
		return false;
	}

	/**
	 * Factory cannot know that we store list_id along with all data.
	 * Don't mix this up with number_of_messages().
	 *
	 * @return		Unsigned integer.
	 */
	public static function get_num_threads_of(ADOConnection $db, $tablename, $list_id) {
		return $db->GetOne('SELECT COUNT(*) FROM '.$tablename.' WHERE lid='.$list_id);
	}

	/**
	 * Used in displaying thread's contents.
	 */
	public function get_messages() {
		return $this->factory->get_all_messages_of($this->get_unique_value());
	}

	/**
	 * Used in displaying the threads' list.
	 *
	 * @return		Integer greater than 0 as threads without messages cannot exist.
	 */
	public function number_of_messages() {
		if(isset($this->posts)) {
			$this->posts	= $this->factory->get_thread_num_messages($this->get_unique_value());
		}
		return $this->posts;
	}

	/**
	 * Usefull at displaying threads' list.
	 */
	public function get_last_message() {
		return $this->factory->get_thread_last_message($this->get_unique_value());
	}

	/**
	 * Usefull at displaying threads' list.
	 */
	public function get_first_message() {
		return $this->factory->get_thread_first_message($this->get_unique_value());
	}

	/* now come getters and setters */
	public function get_name() {
		return $this->threadname;
	}

	public function get_owning_list() {
		return $this->factory->get_list($this->lid);
	}

	public function get_views() {
		if(isset($this->views)) {
			return (int) $this->views;
		} else {
			return 0;
		}
	}

	public function set_name($txt) {
		$this->threadname	= $txt;
	}

	/**
	 * Writes thread immediately to db after having increased the counter.
	 */
	public function inc_views() {
		$n = $this->get_views();
		$this->views	= ++$n;
		$this->write_to_db();
	}

	/**
	 * Used in creation of new thread by oml_list.
	 */
	public function associate_with_list(oml_list $partner) {
		$this->lid	= $partner->get_unique_value();
	}

}
?>