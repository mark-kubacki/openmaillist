<?php
class openmaillist {

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
		SELECT li.LID, lname AS name, lemailto AS emailto, ldescription AS description,
			COUNT(th.TID) AS threads, 
			COUNT(tm.TID) AS posts 		Lists
		FROM '.$cfg['tablenames']['Lists'].' li
		LEFT OUTER JOIN '.$cfg['tablenames']['Threads'].' th ON (li.LID = th.LID)
		LEFT OUTER JOIN '.$cfg['tablenames']['ThreadMessages'].' tm ON (th.TID = tm.TID)
		GROUP BY li.lname, li.lemailto, li.ldescription
		');

		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_assoc($result)) {
				$tmp[] = $row;
			}
			mysql_free_result($result);
		}
		return $tmp;
	}

	function get_threads($list_id) {
		global $cfg;
		$tmp	= array();

		$result = mysql_query('
		SELECT th.TID,th.Threadname AS subject, tm.Sender AS sender, COUNT(tm.tid) AS posts 
		FROM '.$cfg['tablenames']['Threads'].' th
		LEFT OUTER JOIN '.$cfg['tablenames']['ThreadMessages'].' tm ON (th.tid = tm.tid) 
		WHERE '.$list_id.'= th.lid
		GROUP BY th.Threadname, tm.Sender
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

	function get_messages($thread_id) {
		global $cfg;
		$tmp	= array();

		$result = mysql_query('
		SELECT tm.tid , tm.Sender AS sender, tm.DateSend AS send_at, tm.DateReceived AS received_at,
			me.Subject AS subject,  me.body AS text, 
			att.location AS location
		FROM  '.$cfg['tablenames']['ThreadMessages'].' tm
		LEFT OUTER JOIN '.$cfg['tablenames']['Messages'].' me ON (tm.MsgID = me.MsgID)
		LEFT OUTER JOIN '.$cfg['tablenames']['Attachements'].' att ON (tm.MsgID = att.MsgID) 
		WHERE '.$thread_id.' = tm.TID
		ORDER BY tm.DateReceived
		') ;

		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_assoc($result)) {
				$tmp[] = $row;
			}
			mysql_free_result($result);
		}

		return $tmp;
	}

}

?>
