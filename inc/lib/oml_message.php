<?php
class oml_message
	extends OMLObject
{
	// due to OMLObject:
	public static $schema_file	= './inc/database/oml_message.adodb.txt';
	// variables for own purpose
	protected $mid		= null;	// null means: not in or taken from db -> new, pristine
	// see table for them:

	/**
	 * @returns integer	0 if failure, 1 if errors, 2 if successful
	 * @see_also		adodb: ExecuteSchema
	 */
	public static function create_your_table($db, $tablename) {
		$filds		= file_get_contents(oml_message::schema_file);
		$taboptarray	= array('mysql' => 'ENGINE=InnoDB CHARSET=utf8 COLLATE=utf8_unicode_ci');
		$idxname = 'message_id';
		$idxflds = 'message_id';
		$idxopts = array('UNIQUE');

		$sqlarray = $dict->CreateTableSQL($tabname, $flds, $taboptarray);
		if($dict->ExecuteSQLArray($sqlarray)) {
			$sqlarray = $dict->CreateIndexSQL($idxname, $tabname, $idxflds, $idxopts);
			return $dict->ExecuteSQLArray($sqlarray);
		}
		else {
			throw new Exception('Table could not be created.');
			return false;
		}
	}

	/**
	 * Writes changes (or even new data) to db.
	 */
	public function write_to_db() {
	}

	/**
	 * Execute if you want this message be filled with contents from an already existing entry.
	 * Changes made prior to it's call will be discarded, so don't forget serialization.
	 */
	public function assign_mid($mid) {
	}

	/**
	 * It might be that we have to strip tags or convert to special entities.
	 *
	 * @returns	string with text. At emails we call this "first displayable part (of body)".
	 */
	public function get_text() {
	}

}

?>