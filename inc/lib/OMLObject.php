<?php
/**
 * A collection of common implementations.
 * To demand superior as constructor.
 */
abstract class OMLObject
	extends OMLStoredItem
{
	protected $superior;

	function __construct(ADOConnection $database_handler, $preferred_tablename = null, oml_manager $superior = null) {
		$this->db	= $database_handler;
		$this->table	= $preferred_tablename;
		$this->superior	= $superior;
	}

}

?>