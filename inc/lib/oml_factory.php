<?php
/**
 * Pattern: Factory
 * Responsible for creation.
 */
class oml_factory
{
	protected	$db;
	protected	$tables;

	function __construct(ADOConnection $database_handler, array $table_names) {
		$this->db	= $database_handler;
		$this->tables	= $table_names;
	}

	public function create_lists_table() {
		return oml_list::create_your_table($this->db, $this->tables['Lists']);
	}

	public function create_threads_table() {
		return oml_thread::create_your_table($this->db, $this->tables['Threads']);
	}

	public function create_messages_table() {
		return oml_message::create_your_table($this->db, $this->tables['Messages']);
	}

	public function create_attachments_table() {
		return oml_attachment::create_your_table($this->db, $this->tables['Attachments']);
	}

	public function get_list($lid = null) {
		$tmp = new oml_list($this->db, $this->tables['Lists'], $this);
		if(!is_null($lid)) {
			$tmp->set_unique_value($lid);
		}
		return $tmp;
	}

	public function get_thread($tid = null) {
		$tmp = new oml_thread($this->db, $this->tables['Threads'], $this);
		if(!is_null($tid)) {
			$tmp->set_unique_value($tid);
		}
		return $tmp;
	}

	public function get_message($mid = null) {
		$tmp = new oml_message($this->db, $this->tables['Messages'], $this);
		if(!is_null($mid)) {
			$tmp->set_unique_value($mid);
		}
		return $tmp;
	}

	public function get_attachment($aid = null) {
		$tmp = new oml_attachment($this->db, $this->tables['Attachments'], $this);
		if(!is_null($aid)) {
			$tmp->set_unique_value($aid);
		}
		return $tmp;
	}

}
?>