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