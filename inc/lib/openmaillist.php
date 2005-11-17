<?php
class openmaillist {

	var		$error;

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
