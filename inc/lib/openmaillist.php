<?php
final class openmaillist
{
	private		$db;
	private		$factory;

	function __construct(ADOConnection $database_handler, oml_factory $factory) {
		$this->db	= $database_handler;
		$this->factory	= $factory;
	}

	/**
	 * returns array	All lists associated with this instance of openmaillist.
	 */
	public function get_all_lists() {
		return $this->factory->get_all_lists();
	}

}
?>