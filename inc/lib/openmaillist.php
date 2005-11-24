<?php
class openmaillist {

// private:
	var	$regex_valid_email	= '[\w0-9]{1,}[\w0-9\.\-\_\+]*@[\w0-9\.\-\_]{2,}\.[\w]{2,}';
// protected:
	var	$error;

	function openmaillist() {
		global $cfg;

		$link = mysql_connect($cfg['Servers']['DB'][0]['HOST'],
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

	function split_email($from_or_to) {
		if(preg_match('/(.*)\s?\<(.+)\>/', $from_or_to, $arr)) {
			$ret	= array('name'	=> $arr[1],
					'email'	=> $arr[2],
					);
			return $ret;
		}
		else if(preg_match('/('.$this->regex_valid_email.')/', $from_or_to, $arr)) {
			$ret	= array('email'	=> $arr[1],
					);
			return $ret;
		}

		return false;
	}

	function get_attachements($msgid) {
		global $cfg;
		$ret = array();

		$result = mysql_query('SELECT *
		FROM '.$cfg['tablenames']['Attachements'].'
		WHERE MsgID = "'.$msgid.'"
		ORDER BY AttID
		');
		while($ret[] = mysql_fetch_assoc($result));
		mysql_free_result($result);

		return $ret;
	}

	function store_message($msg, $list_id = null) {
		// If not list_id was given (bad) we have to determine it...
		if(is_null($list_id)) {
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

			if(! $list_id) {
				$this->add_error('No list has been found which the message could belong to. Tag we tried to search for was "'.$list_tag.'".');
			}
			unset($list_tag); unset($list_rec);
		}
		// Our first task is to determine whether the given message belongs to an already opened thread.
			// Does the message refer to a known Message-ID?
			// If not, does it reference a known message?
			// Maybe a similar subject was opened lately?
		// If not, we are to create a new thread.
	}

	function get_messages($thread_id) {
		global $cfg;
		$tmp	= array();

		$result = mysql_query('
		SELECT tm.tid, tm.Sender AS sender, tm.DateSend AS datesend, tm.DateReceived AS datereceived,
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
