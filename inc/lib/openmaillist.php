<?php
class openmaillist {

	function openmaillist() {
		global $cfg;

		$link = mysql_connect($cfg['Servers']['DB'][0]['HOST'],
								$cfg['Servers']['DB'][0]['USER'],
								$cfg['Servers']['DB'][0]['PASS'])
		or die('Could not connect to MySQL Server');
		mysql_select_db($cfg['Servers']['DB'][0]['DB'])
		or die('Could not select database');
	}

	function __destruct() {
		mysql_close();
	}

	function get_lists() {
		global $cfg;
		$tmp	= array();

		$result = mysql_query('
		select li.LID, lname as name, lemailto as emailto, ldescription as description,
			count(th.TID) as threads, 
			count(tm.TID) as posts 		Lists
		from '.$cfg['tablenames']['Lists'].' li
		left outer join '.$cfg['tablenames']['Threads'].' th on (li.LID = th.LID)
		left outer join '.$cfg['tablenames']['ThreadMessages'].' tm on (th.TID = tm.TID)
		group by li.lname, li.lemailto, li.ldescription
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
		select th.TID,th.Threadname as subject, tm.Sender, count(tm.tid) as posts 
		from '.$cfg['tablenames']['Threads'].' th
		left outer join '.$cfg['tablenames']['ThreadMessages'].' tm on (th.tid = tm.tid) 
		where '.$list_id.'= th.lid
		group by th.Threadname, tm.Sender
		order by tm.DateReceived
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
		select tm.tid , tm.Sender as sender, tm.DateSend as send_at, tm.DateReceived as received_at,
			me.Subject as subject,  me.body as text, 
			att.location as location
		from  '.$cfg['tablenames']['ThreadMessages'].' tm
		left outer join '.$cfg['tablenames']['Messages'].' me on (tm.MsgID = me.MsgID)
		left outer join '.$cfg['tablenames']['Attachements'].' att on (tm.MsgID = att.MsgID) 
		where '.$thread_id.' = tm.TID
		order by tm.DateReceived
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
