<?php
class oml_attachment
	extends OMLObject
{
	// due to OMLObject:
	public static $schema_file	= './inc/database/attachment.adodb.txt';
	protected $unique_key		= 'aid';

	/* administrative */
	public static function create_your_table(ADOConnection $db, $tablename) {
		$flds		= file_get_contents(self::$schema_file);
		$taboptarray	= array('mysql' => 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');

		$dict = NewDataDictionary($db);

		$sqlarray = $dict->CreateTableSQL($tablename, $flds, $taboptarray);
		if($dict->ExecuteSQLArray($sqlarray)) {
			$sqlarray = $dict->CreateIndexSQL('lid', $tablename, 'lid');
			$dict->ExecuteSQLArray($sqlarray);
			$sqlarray = $dict->CreateIndexSQL('mid', $tablename, 'mid');
			return $dict->ExecuteSQLArray($sqlarray);
		} else {
			throw new Exception('Table "'.$tablename.'" could not be created.');
			return false;
		}
	}

	public function register_message(oml_message $msg) {
		$this->mid	= $msg->get_unique_value();
		$this->lid	= $msg->get_owning_thread()->get_owning_list()->get_unique_value();
		$this->write_to_db();
	}

	public function set_filename($filename) {
		$this->send_as	= $filename;
	}

	public function get_filename() {
		return $this->send_as;
	}

	public function set_storage_name($filename) {
		$this->stored_as	= $filename;
	}

	public function get_storage_name() {
		return $this->stored_as;
	}

}
?>