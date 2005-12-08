<?php
/**
 * Pattern: (Construct-)Factory
 */
class oml_factory
{
	protected	$db;
	protected	$tables;

	function __construct(NewADOConnection $database_handler, array $table_names) {
		$this->db	= $database_handler;
		$this->tables	= $table_names;
	}

	public function get_all_lists() {
		return oml_list::get_all_lists($this->db, $this, $this->tables['Lists']);
	}


	public function get_list($lid = null) {
		$tmp = new oml_list($this->db, $this, $this->tables['Lists']);
		if(!is_null($lid)) {
			$tmp->set_unique_value($lid);
		}
		return $tmp;
	}

/*
	public function get_thread($tid = null) {
		$tmp = new oml_thread($this->db, $this, $this->tables['Threads']);
		if(!is_null($tid)) {
			$tmp->set_unique_value($tid);
		}
		return $tmp;
	}
*/

	public function get_message($mid = null) {
		$tmp = new oml_message($this->db, $this, $this->tables['Messages']);
		if(!is_null($mid)) {
			$tmp->set_unique_value($mid);
		}
		return $tmp;
	}

}
?>