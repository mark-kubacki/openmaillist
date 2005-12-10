<?php
class oml_thread
	extends OMLObject
{
	// due to OMLObject:
	public static $schema_file	= './inc/database/thread.adodb.txt';
	protected $unique_key		= 'tid';

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
			$sqlarray = $dict->CreateIndexSQL('lid', $tablename, 'lid');
			return $dict->ExecuteSQLArray($sqlarray);
		} else {
			throw new Exception('Table "'.$tablename.'" could not be created.');
			return false;
		}
	}

	public static function get_threads_of(ADOConnection $db, oml_factory $factory, $tablename, $list_id) {
		$result		= array();
		$rs = $db->Execute('SELECT * FROM '.$tablename.' WHERE lid='.$list_id);
		foreach($rs as $row){
			$tmp		= $factory->get_thread();
			$tmp->become($row);
			$result[]	= $tmp;
		}
		return $result;
	}

	public static function get_thread_with_name(ADOConnection $db, oml_factory $factory, $tablename, $list_id, $name) {
		$result		= array();
		$tid = $db->GetOne('SELECT tid FROM '.$tablename.' WHERE lid='.$list_id.' AND threadname='.$db->qstr($name).' ORDER BY lastpost DESC');
		if(!$tid === false) {
			return $factory->get_thread($tid);
		}
		return false;
	}

	public static function get_num_threads_of(ADOConnection $db, $tablename, $list_id) {
		return $db->GetOne('SELECT COUNT(*) FROM '.$tablename.' WHERE lid='.$list_id);
	}

	/* now come getters and setters */
	public function get_name() {
		return $this->getter('threadname');
	}

	public function get_owning_list() {
		return $this->factory->get_list($this->getter('lid'));
	}

	public function set_name($txt) {
		$this->setter('threadname', $txt);
	}

	public function set_lastpost($timestamp) {
		if($this->has('lastpost')) {
			if($this->getter('lastpost') < $timestamp) {
				$this->setter('lastpost', $timestamp);
			}
		} else {
			$this->setter('lastpost', $timestamp);
		}
	}

	public function associate_with_list(oml_list $partner) {
		$this->setter('lid', $partner->get_unique_value());
	}

}
?>