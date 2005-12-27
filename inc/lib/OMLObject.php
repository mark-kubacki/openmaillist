<?php
/**
 * A collection of common implementations.
 */
abstract class OMLObject
	extends OMLStoredItem
{
	protected $factory;

	function __construct(ADOConnection $database_handler, $preferred_tablename = null, oml_factory $factory = null) {
		$this->db	= $database_handler;
		$this->table	= $preferred_tablename;
		$this->factory	= $factory;
	}

}

?>