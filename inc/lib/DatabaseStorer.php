<?php
/**
 * Implementing classes shall store their data in DB.
 */
interface DatabaseStorer
{
	/**
	 * Centralizes creation of tables.
	 *
	 * @param	db		ADOdb database handler as this is a static function.
	 * @param	tablename	String with the name of the table to be created.
	 * @return			Integer: 0 on failure, 1 on errors, 2 on success
	 * @see				ADOdb: ExecuteSQLArray and table creation
	 */
	static function create_your_table(ADOConnection $db, $tablename);
	function __construct(ADOConnection $database_handler, $preferred_tablename);
	/**
	 * Writes changes (or even new data) to db.
	 *
	 * @return		true on success
	 */
	function write_to_db();
	/**
	 * @return		true on success
	 */
	function remove_from_db();

}

?>