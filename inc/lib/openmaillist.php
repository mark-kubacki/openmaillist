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

	public function get_list($list_id) {
		if(is_numeric($list_id)) {
			$list = $this->factory->get_list($list_id);
			if(!$list === false) {
				return $list;
			} else {
				throw new Exception('Given list does not exist.');
			}
		} else {
			throw new Exception('Given list_id is not valid.');
		}
	}

	/**
	 * returns array	All threads associated with that given list.
	 */
	public function get_all_threads(oml_list $list) {
		return $list->get_all_threads();
	}

}
?>