<?php
/**
 * Responsible for administration.
 */
class oml_manager
	extends oml_factory
{
	public function get_all_lists() {
		return oml_list::get_all_lists($this->db, $this, $this->tables['Lists']);
	}

	public function get_all_threads_of($list_id) {
		$threads	= array();
		$rs = $this->db->Execute(
			'SELECT th.*, COUNT(tm.tid) AS posts, MAX(tm.DateReceived) AS lastdate
			FROM '.$this->tables['Threads'].' AS th
			LEFT OUTER JOIN '.$this->tables['Messages'].' AS tm ON (th.tid = tm.tid)
			WHERE '.$list_id.'=th.lid
			GROUP BY th.Threadname
			HAVING posts > 0
			ORDER BY tm.datereceived DESC'
		);
		foreach($rs as $row) {
			$tmp	= $this->get_thread();
			$tmp->become($row);
			$threads[]	= $tmp;
		}
		return $threads;
	}

	public function get_all_messages_of($thread_id) {
		return oml_message::get_messages_of($this->db, $this, $this->tables['Messages'], $thread_id);
	}

	public function get_list_by_name($listname) {
		return oml_list::get_list_by_name($this->db, $this, $this->tables['Lists'], $listname);
	}

	/**
	 * @return		Particular thread or false.
	 */
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

	/**
	 * @param	msg	Message whose reference has to be retrieved.
	 * @param	list_id	Only lists with this ID will be searched through.
	 * @return	Message the given one refers to or false.
	 */
	public function get_latest_msg_referred_to(oml_message $msg, $list_id) {
		try {
			$data = $this->db->GetRow(
				'SELECT tm.*
				FROM '.$this->tables['Messages'].' AS tm, '.$this->tables['Threads'].' AS tt
				WHERE tt.tid = tm.tid AND tt.lid='.$list_id.'
				AND (tm.message_id='.$this->db->qstr($msg->get_in_reply_to()).' OR '.$this->db->qstr($msg->get_referenced()).' LIKE CONCAT("%", tm.message_id, "%"))
				ORDER BY tm.datereceived DESC'
			);
			if(!$data === false) {
				$msg = $this->get_message();
				$msg->become($data);
				return $msg;
			}
		} catch (exception $e) {
		}
		return false;
	}

	public function get_num_threads_of($list_id) {
		return oml_thread::get_num_threads_of($this->db, $this->tables['Threads'], $list_id);
	}

	public function get_list_num_messages($list_id) {
		return $this->db->GetOne(
			'SELECT COUNT(*)
			FROM '.$this->tables['Messages'].' AS tm, '.$this->tables['Threads'].' AS tt
			WHERE tm.tid = tt.tid AND tt.lid = '.$list_id
		);
	}

	public function get_thread_num_messages($thread_id) {
		return $this->db->GetOne(
			'SELECT COUNT(*)
			FROM '.$this->tables['Messages'].' AS tm, '.$this->tables['Threads'].' AS tt
			WHERE tm.tid = tt.tid AND tt.tid = '.$thread_id
		);
	}

	public function delete_empty_threads($list_id) {
		$this->db->Execute(
			'DELETE FROM Threads WHERE tid IN (
				SELECT th.tid
				FROM '.$this->tables['Threads'].' AS th
				LEFT OUTER JOIN '.$this->tables['Messages'].' AS tm ON (th.tid = tm.tid)
				WHERE '.$list_id.'=th.lid
				GROUP BY th.Threadname
				HAVING COUNT(tm.tid)=0
			)'
		);
		return $this->db->Affected_Rows();
	}

	/**
	 * @return	Message or false if none has been found.
	 */
	public function get_thread_last_message($thread_id) {
		$data = $this->db->GetRow(
			'SELECT tm.*
			FROM '.$this->tables['Messages'].' AS tm, '.$this->tables['Threads'].' AS tt
			WHERE tt.tid = tm.tid
			AND tt.tid ='.$thread_id.'
			ORDER BY tm.datereceived DESC'
		);
		if(!$data === false) {
			$msg = $this->get_message();
			$msg->become($data);
			return $msg;
		}
		return false;
	}

	/**
	 * @return	Message or false if none has been found.
	 */
	public function get_thread_first_message($thread_id) {
		$data = $this->db->GetRow(
			'SELECT tm.*
			FROM '.$this->tables['Messages'].' AS tm, '.$this->tables['Threads'].' AS tt
			WHERE tt.tid = tm.tid
			AND tt.tid ='.$thread_id.'
			ORDER BY tm.datereceived ASC'
		);
		if(!$data === false) {
			$msg = $this->get_message();
			$msg->become($data);
			return $msg;
		}
		return false;
	}

	/**
	 * @return	Message or false if none has been found.
	 */
	public function get_lists_last_message($list_id) {
		$tmp = $this->get_lists_latest_messages($list_id, 1);
		if(isset($tmp[0])) {
			return $tmp[0];
		}
		return false;
	}

	/**
	 * @see		oml_list::get_num_latest_entries
	 */
	public function get_lists_latest_messages($list_id, $max) {
		$ret	= array();
		$data = $this->db->SelectLimit(
			'SELECT tm.*
			FROM '.$this->tables['Messages'].' AS tm, '.$this->tables['Threads'].' AS tt
			WHERE tt.tid = tm.tid
			AND tt.lid ='.$list_id.'
			ORDER BY tm.datereceived DESC'
			, $max);
		if(!$data === false) {
			foreach($data as $row) {
				$tmp	= $this->get_message();
				$tmp->become($row);
				$ret[]	= $tmp;
			}
		}
		return $ret;
	}

	public function get_attachments_of_mid($mid) {
		$att	= array();
		$rs = $this->db->Execute(
			'SELECT ta.*
			FROM '.$this->tables['Attachments'].' AS ta
			WHERE '.$mid.'=ta.mid'
		);
		foreach($rs as $row) {
			$tmp	= $this->get_attachment();
			$tmp->become($row);
			$att[]	= $tmp;
		}
		return $att;
	}

	public function create_attachment($filename, $storage_name) {
		$tmp	= $this->get_attachment();
		$tmp->set_filename($filename);
		$tmp->set_storage_name($storage_name);
		return $tmp;
	}

}
?>