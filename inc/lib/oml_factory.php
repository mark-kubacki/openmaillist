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

	public function get_message($mid = null) {
		$tmp = new oml_message($this->db, $this->tables['Messages']);
		if(!is_null($mid)) {
			$tmp->assign_mid($mid);
		}
		return $tmp;
	}

	public function get_thread($tid = null) {
		$tmp = new oml_thread($this->db, $this->tables['Threads']);
		if(!is_null($tid)) {
			$tmp->assign_tid($mid);
		}
		return $tmp;
	}

}
?>