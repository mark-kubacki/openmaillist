<?php
class oml_thread
	extends OMLObject
	implements UniqueItem
{
	// due to OMLObject:
	public static $schema_file	= './inc/database/thread.adodb.txt';
	// my
	protected	$data;

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

	/**
	 * Writes changes (or even new data) to db.
	 */
	public function write_to_db() {
		if(!isset($this->data['tid'])) {
			$result = $this->db->AutoExecute($this->table, $this->data, 'INSERT');
			if($result) {
				$this->data['tid'] = $this->db->Insert_ID();
				return true;
			}
			return false;
		} else {
			$result = $this->db->Replace($this->table, $this->data, 'tid', true);
			return ($result > 0);
		}
	}

	/**
	 * returns boolean	true on success
	 */
	public function remove_from_db() {
		if(isset($this->data['tid'])) {
			$this->db->Execute('DELETE FROM '.$this->table.' WHERE tid='.$this->data['lid']);
			return true;
		}
		return false;
	}

	/* due to UniqueItem */
	public function get_unique_value() {
		return $this->data['tid'];
	}

	public function set_unique_value($value) {
		$rs = $this->db->GetRow('SELECT * FROM '.$this->table.' WHERE tid='.$value);
		if(!$rs === false) {
			$this->data = $rs;
			return true;
		}
		return false;
	}

	/* now come getters and setters */
	/**
	 * For internal use only.
	 */
	public function become($data) {
		$this->data	= $data;
	}
	protected function getter($key) {
		if(isset($this->data[$key])) {
			return $this->data[$key];
		} else {
			throw new Exception(__CLASSNAME__.' does not contain value for "'.$key.'".');
		}
	}
	protected function setter($key, $value) {
		$this->data[$key] = $value;
	}

	public function get_name() {
		return $this->getter('threadname');
	}

	public function set_name($txt) {
		$this->setter('threadname', $txt);
	}

	public function associate_with_list(oml_list $partner) {
		$this->setter('lid', $partner->get_unique_value());
	}

}
?>