<?php
/**
 * Implementing classes shall store their data in DB.
 */
interface DatabaseStorer
{
	function __construct(ADOConnection $database_handler, $preferred_tablename);
	function write_to_db();
	function remove_from_db();

}

?>