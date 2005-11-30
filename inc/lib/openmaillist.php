<?php

include('oml_email.php');

class openmaillist {

// private:
	var	$regex_valid_email	= '[\w0-9]{1,}[\w0-9\.\-\_\+]*@[\w0-9\.\-\_]{2,}\.[\w]{2,}';
	var	$regex_essen_subject	= '(?:re|aw|fwd)?:?\s?(?:\[.*\])?\s?(.+)\s*(?:\(was:.*\))?';
// protected:
	var	$error;

	function openmaillist() {
		global $cfg;

		$link = mysql_connect(	$cfg['Servers']['DB'][0]['HOST'],
					$cfg['Servers']['DB'][0]['USER'],
					$cfg['Servers']['DB'][0]['PASS'])
		or die('Could not connect to MySQL Server');
		mysql_select_db($cfg['Servers']['DB'][0]['DB'])
		or die('Could not SELECT database');
	}

	function __destruct() {
		mysql_close();
	}

	function add_error($error) {
		$this->error[]	= $error;
	}

	function get_lists() {
		global $cfg;
		$tmp	= array();

		$result = mysql_query('
		SELECT li.lid, lname AS name, lemailto AS emailto, ldescription AS description,
			COALESCE(thc.TAnzahl, 0) AS threads,
			COUNT(tm.TID) AS posts,
			MAX(tm.DateReceived) AS lastdate
		FROM '.$cfg['tablenames']['Lists'].' AS li
		LEFT OUTER JOIN
			( SELECT LID as LID, COUNT(TID) AS TAnzahl
			FROM '.$cfg['tablenames']['Threads'].'
			GROUP BY TID) AS thc ON (li.LID = thc.LID)
		LEFT OUTER JOIN '.$cfg['tablenames']['Threads'].' AS th ON (li.LID = th.LID)
		LEFT OUTER JOIN '.$cfg['tablenames']['ThreadMessages'].' AS tm ON (th.TID = tm.TID)
		GROUP BY li.lname, thc.LID
		ORDER BY lid
		');

		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_assoc($result)) {
				$tmp[] = $row;
			}
			mysql_free_result($result);
		}
		else {
			echo('<strong>'.mysql_error().'</strong>');
		}
		return $tmp;
	}

	function get_list_id($list_name, $list_email) {
		global $cfg;
		$id = 0;

		$result = mysql_query('
		SELECT LID
		FROM '.$cfg['tablenames']['Lists'].'
		WHERE LName = "'.$list_name.'" OR LEmailTo = "'.$list_email.'"
		LIMIT 1');

		if(mysql_num_rows($result) > 0) {
			$id = mysql_result($result, 0, 0);
			mysql_free_result($result);
		}

		return $id;
	}

	function get_threads($list_id) {
		global $cfg;
		$tmp	= array();

		$result = mysql_query('
		SELECT th.tid AS tid,th.Threadname AS name, tm.Sender AS lastfrom, COUNT(tm.tid) AS posts, MAX(tm.DateReceived) AS lastdate
		FROM '.$cfg['tablenames']['Threads'].' AS th
		LEFT OUTER JOIN '.$cfg['tablenames']['ThreadMessages'].' AS tm ON (th.tid = tm.tid)
		WHERE '.$list_id.'= th.lid
		GROUP BY th.Threadname
		ORDER BY tm.DateReceived
		');

		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_assoc($result)) {
				$tmp[] = $row;
			}
			mysql_free_result($result);
		}

		return $tmp;
	}

	function create_thread($threadname, $list_id) {
		global $cfg;

		$result = mysql_query('
		INSERT INTO '.$cfg['tablenames']['Threads'].'
		(LID, Threadname) VALUES
		('.intval($list_id).', "'.$threadname.'")
		');

		if(mysql_affected_rows($result) < 1) {
			if(mysql_errno() != 0) {
				$this->add_error(mysql_error());
			}
			return null;
		}

		return mysql_insert_id();
	}

	function get_attachements($msgid) {
		global $cfg;
		$ret = array();

		$result = mysql_query('
		SELECT *
		FROM '.$cfg['tablenames']['Attachements'].'
		WHERE MsgID = "'.$msgid.'"
		ORDER BY AttID
		');
		while($row = mysql_fetch_assoc($result)) {
			$ret[] = $row;
		}
		mysql_free_result($result);

		return $ret;
	}

	function store_message($msg, $list_id = null, $thread_id = null) {
		global $cfg;

		// If no list_id was given (bad) we have to determine it...
		if(is_null($list_id)) {
			$list_id = $this->email_list_id($msg);
			if(is_null($list_id)) {
				return false;
			}
		}
		// Our first task is to determine whether the given message belongs to an already opened thread.
		if(is_null($thread_id)) {
			$thread_id = $this->email_thread_id($msg);
		}
		// If not, we are to create a new thread.
		if(is_null($thread_id)) {
			// extract subject's essential parts
			if(preg_match('/'.$this->regex_essen_subject.'/i', $msg->get_header('subject'), $arr)
			   && isset($arr[1])) {
				// create that thread
				$thread_id = $this->create_thread(mysql_real_escape_string($arr[1]), $list_id);
			}
			else {
				$this->add_error('No suitable subject for naming a new thread was found.');
				return false;
			}
		}

		// Then, store any attachements.
		if($msg->has_attachements()) {
			$storage_rel	= $cfg['upload_dir'].'/'.md5($msg->get_header('message-id'));
			$storage_dir	= $_SERVER['DOCUMENT_ROOT'].'/'.$storage_rel;
			// ... create directory for attachement-storage
			mkdir($storage_dir, 0775);
			$msg->set_attachement_storage($storage_dir);
			// ... and retrieve any attachements.
			$attachements = $msg->get_attachements();
		}

		// Now we can store the message.
		if($this->store_message_in_db($msg)
		   && $this->register_message_with_thread($thread_id, $msg)) {
			// ... and register its attachements
			if(isset($attachements)) {
				$this->register_attachements($storage_rel, $attachements);
			}
			return true;
		}
		else {
			// ... delete the attachements, if there are any
			if(isset($attachements)) {
				// (we have to call this even if 0 attachements are there - we are to delete the directory
				$this->delete_attachements($storage_dir, $attachements);
			}
		}

		return false;
	}

	// private
	function register_attachements($msgid, $folder, $att) {
		global $cfg;

		if(count($att) > 0) {
			// iterate through every attachement and create the VALUES part of SQL query
			$values = array();
			foreach($att as $filename) {
				$values[] = '("'.$msgid.'", "'.$folder.'/'.$filename.'")';
			}
			// actually add the lines to DB
			$result = mysql_query('
			INSERT DELAYED INTO '.$cfg['tablenames']['Attachements'].'
			(MsgID, Location) VALUES
			'.implode(', ', $values).'
			');

			if(mysql_affected_rows($result) < 1) {
				if(mysql_errno() != 0) {
					$this->add_error(mysql_error());
				}
				return false;
			}
		}

		return true;
	}
	// private
	function delete_attachements($storage_dir, $att) {
		rm_r($storage_dir);
		return true;
	}

	//private
	function register_message_with_thread($thread_id, $msg) {
		global $cfg;

		// Is the message already registered?
		$result = mysql_query('
		SELECT COUNT(*)
		FROM '.$cfg['tablenames']['ThreadMessages'].'
		WHERE TID='.$thread_id.' AND MsgID="'.$msg->get_header('message-id').'"
		LIMIT 1
		');

		if($result && mysql_result($result, 0, 0) > 0) {
			return true;
		}

		// If not, add that line.
		$result = mysql_query('
		INSERT INTO '.$cfg['tablenames']['ThreadMessages'].'
		(MsgID, TID, DateSend, DateReceived, Sender) VALUES
		("'.$msg->get_header('message-id').'", '.$thread_id.', '.$msg->get_header('date-send').', '.$msg->get_header('date-received').', "'.$msg->get_header('from').'")
		');

		if(mysql_affected_rows($result) < 1) {
			if(mysql_errno() != 0) {
				$this->add_error(mysql_error());
			}
			return false;
		}

		return true;
	}

	// private
	function store_message_in_db($msg) {
		global $cfg;

		$has_attachements	= $msg->has_attachements() ? 1 : 0;
		$headers		= mysql_real_escape_string($msg->get_header_part());
		$body			= mysql_real_escape_string($msg->get_first_part());

		$result = mysql_query('
		INSERT DELAYED INTO '.$cfg['tablenames']['Messages'].'
		(MsgID, Subject, Body, Header, Attach) VALUES
		("'.$msg->get_header('message-id').'", "'.$msg->get_header('subject').'",
		 "'.$headers.'", "'.$body.'", '.$has_attachements.')
		');

		if(mysql_affected_rows($result) < 1) {
			if(mysql_errno() != 0) {
				$this->add_error(mysql_error());
			}
			return false;
		}

		return true;
	}

	function email_list_id($oml_email_msg) {
		// RE: [listname] ...
		$list_tag = strstr($msg->get_header('subject'), '[');
		if($list_tag) {
			// We are naive to expect an correct tag, but that does not matter.
			$list_tag = substr($list_tag, 0, strpos($list_tag, ']'));
		}

		// the address the message was send to
		$list_rec = $this->split_email($msg->get_header('_recipient'));

		// query for the id
		$list_id = $this->get_list_id(mysql_real_escape_string($list_tag), mysql_real_escape_string($list_rec['email']));

		if(!$list_id) {
			$this->add_error('No list has been found which the message could belong to. Tag we tried to search for was "'.$list_tag.'".');
			return null;
		}

		return $list_id;
	}

	/**
	 * Tries to detect which thread the given messae could belong to.
	 * @param $msg		oml_email message
	 * @return		possible thread_id or NULL
	 */
	function email_thread_id($msg) {
		global $cfg;

		$thread_id = null;
		// Does the message refer to a known Message-ID?
		if($msg->get_header('in-reply-to') != '') {
			$result = mysql_query('
			SELECT TID
			FROM '.$cfg['tablenames']['ThreadMessages'].'
			WHERE MsgID="'.$msg->get_header('in-reply-to').'"
			ORDER BY DateReceived DESC
			LIMIT 1
			');
			if(mysql_num_rows($result) > 0) {
				$thread_id = mysql_result($result, 0, 0);
				mysql_free_result($result);
				return $thread_id;
			}
		}
		// If not, does it reference a known message?
		if($msg->get_header('references') != '') {
			$result = mysql_query('
			SELECT TID
			FROM '.$cfg['tablenames']['ThreadMessages'].'
			WHERE "'.$msg->get_header('references').'" LIKE CONCAT("%", MsgID, "%")
			ORDER BY DateReceived DESC
			LIMIT 1
			');
			if(mysql_num_rows($result) > 0) {
				$thread_id = mysql_result($result, 0, 0);
				mysql_free_result($result);
				return $thread_id;
			}
		}
		// Maybe a similar subject was opened lately?
		if($cfg['thread']['guess_from_subject']) {
			if(preg_match('/'.$this->regex_essen_subject.'/i', $msg->get_header('subject'), $arr)
			   && isset($arr[1])) {
				$result = mysql_query('
				SELECT TID
				FROM '.$cfg['tablenames']['Threads'].'
				WHERE Threadname="'.$arr[1].'"
				ORDER BY TID DESC
				LIMIT 1
				');
				if(mysql_num_rows($result) > 0) {
					$thread_id = mysql_result($result, 0, 0);
					mysql_free_result($result);
					return $thread_id;
				}
			}
		}

		return $thread_id;
	}

	function get_messages($thread_id) {
		global $cfg;
		$tmp	= array();

		$result = mysql_query('
		SELECT tm.Sender AS sender, tm.DateSend AS datesend, tm.DateReceived AS datereceived,
			me.Subject AS subject, me.body AS body, me.attach AS numattachements, me.MsgID
		FROM '.$cfg['tablenames']['ThreadMessages'].' AS tm
		LEFT OUTER JOIN '.$cfg['tablenames']['Messages'].' AS me ON (tm.MsgID = me.MsgID)
		WHERE '.$thread_id.' = tm.TID
		ORDER BY tm.DateReceived
		') ;

		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_assoc($result)) {
				// If a message has attachements, we are to query their location.
				if($row['numattachements'] > 0) {
					$row['attach'] = $this->get_attachements($row['MsgID']);
				}
				else {
					$row['attach'] = array();
				}

				$tmp[] = $row;
			}
			mysql_free_result($result);
		}

		return $tmp;
	}

}

?>
