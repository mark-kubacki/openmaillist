<?php
final class openmaillist
	implements ErrorHandler
{
	private $db;
	private $factory;

	function __construct(NewADOConnection $db, array $tablenames) {
		$this->db	= $db;
		$this->factory	= new oml_factory($db, $tablenames);
	}

	/**
	 * returns array	All lists associated with this instance of openmaillist.
	 */
	public function get_all_lists() {
		return $this->factory->get_all_lists();
	}
}
?>