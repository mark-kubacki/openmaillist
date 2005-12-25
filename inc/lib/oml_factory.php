<?php
/**
 * Pattern: (Construct-)Factory
 * Responsible for creation and administration.
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

	public function get_all_lists() {
		return oml_list::get_all_lists($this->db, $this, $this->tables['Lists']);
	}

	public function get_all_threads_of($list_id) {
		return oml_thread::get_threads_of($this->db, $this, $this->tables['Threads'], $list_id);
	}

	public function get_all_messages_of($thread_id) {
		return oml_message::get_messages_of($this->db, $this, $this->tables['Messages'], $thread_id);
	}

	public function get_list_by_name($listname) {
		return oml_list::get_list_by_name($this->db, $this, $this->tables['Lists'], $listname);
	}

	public function get_thread_with($list_id, $message_id) {
		$thread	= oml_message::get_thread_with($this->db, $this, $this->tables['Messages'], $message_id);
		$list	= $thread->get_owning_list();
		if($list_id == $list->get_unique_value()) {
			return $thread;
		} else {
			return false;
		}
	}

	public function get_thread_with_name($list_id, $name) {
		return oml_thread::get_thread_with_name($this->db, $this, $this->tables['Threads'], $list_id, $name);
	}

	public function get_message_quoted_by(oml_message $msg) {
		return oml_message::get_message_quoted_by($this->db, $this, $this->tables['Messages'], $msg);
	}

	public function get_num_threads_of($list_id) {
		return oml_thread::get_num_threads_of($this->db, $this->tables['Threads'], $list_id);
	}

	public function get_list_num_messages($list_id) {
		return $this->db->GetOne(
			'SELECT COUNT(*)
			FROM '.$this->tables['Messages'].', '.$this->tables['Threads'].'
			WHERE '.$this->tables['Messages'].'.tid = '.$this->tables['Threads'].'.tid AND '.$this->tables['Threads'].'.lid = '.$list_id
		);
	}

	public function get_thread_num_messages($thread_id) {
		return $this->db->GetOne(
			'SELECT COUNT(*)
			FROM '.$this->tables['Messages'].', '.$this->tables['Threads'].'
			WHERE '.$this->tables['Messages'].'.tid = '.$this->tables['Threads'].'.tid AND '.$this->tables['Threads'].'.tid = '.$thread_id
		);
	}

	public function delete_empty_threads($list_id) {
		// TODO
	}

	public function get_thread_last_message($thread_id, $order_by) {
		$data = $this->db->GetRow(
			'SELECT '.$this->tables['Messages'].'.*
			FROM '.$this->tables['Messages'].', '.$this->tables['Threads'].'
			WHERE '.$this->tables['Threads'].'.tid = '.$this->tables['Messages'].'.tid
			AND '.$this->tables['Threads'].'.tid ='.$thread_id.'
			ORDER BY '.$order_by.' DESC'
		);
		if(!$data === false) {
			$msg = $this->get_message();
			$msg->become($data);
			return $msg;
		}
		return false;
	}

	public function get_lists_last_message($list_id, $order_by) {
		$data = $this->db->GetRow(
			'SELECT '.$this->tables['Messages'].'.*
			FROM '.$this->tables['Messages'].', '.$this->tables['Threads'].'
			WHERE '.$this->tables['Threads'].'.tid = '.$this->tables['Messages'].'.tid
			AND '.$this->tables['Threads'].'.lid ='.$list_id.'
			ORDER BY '.$order_by.' DESC'
		);
		if(!$data === false) {
			$msg = $this->get_message();
			$msg->become($data);
			return $msg;
		}
		return false;
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

}
?>