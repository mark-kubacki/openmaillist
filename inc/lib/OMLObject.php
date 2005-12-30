<?php
/**
 * A collection of common implementations.
 * To demand factory as constructor.
 */
abstract class OMLObject
	extends OMLStoredItem
{
	protected $factory;

	function __construct(ADOConnection $database_handler, $preferred_tablename = null, oml_manager $factory = null) {
		$this->db	= $database_handler;
		$this->table	= $preferred_tablename;
		$this->factory	= $factory;
	}

}

?>